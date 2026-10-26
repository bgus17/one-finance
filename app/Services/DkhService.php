<?php

namespace App\Services;

use App\Models\Angsuran;
use App\Models\Dc;
use App\Models\Dkh;
use App\Models\DkhDetail;
use App\Models\Pinjaman;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DkhService
{
    /**
     * Dapatkan minggu dan tahun operasional aktif berdasarkan aturan siklus Selasa - Senin.
     * Pada hari Senin, sistem menganggap siklus berjalan masih milik minggu sebelumnya.
     */
    public function getMingguDanTahunAktif(?Carbon $date = null): array
    {
        $date = $date ? $date->copy() : Carbon::today();

        if ($date->dayOfWeek == Carbon::MONDAY) {
            $minggu = $date->copy()->subWeek()->weekOfYear;
            $tahun = $date->copy()->subWeek()->year;
        } else {
            $minggu = $date->weekOfYear;
            $tahun = $date->year;
        }

        return [
            'minggu' => (int) $minggu,
            'tahun' => (int) $tahun,
        ];
    }

    /**
     * Dapatkan rentang tanggal (Selasa s/d Senin) untuk minggu dan tahun tertentu.
     */
    public function getPeriodeMinggu(int $tahun, int $minggu): array
    {
        // ISO weekday: 2 = Selasa
        $dari = Carbon::now()->setISODate($tahun, $minggu, 2)->startOfDay();
        // Selasa + 6 hari = Senin
        $sampai = (clone $dari)->addDays(6)->endOfDay();

        return [
            'dari' => $dari,
            'sampai' => $sampai,
            'label' => $dari->format('d/m/Y') . ' - ' . $sampai->format('d/m/Y'),
        ];
    }

    /**
     * Generate atau Sinkronisasi DKH untuk minggu dan tahun tertentu.
     * Mengikuti tanggal_cair pinjaman:
     * - Pinjaman yang baru cair di minggu berjalan belum ditagihkan (grace period).
     * - Pinjaman mulai ditagihkan saat tanggal_jatuh_tempo angsuran pertama telah tiba (1 minggu setelah tanggal_cair).
     * - Angsuran yang ditagihkan adalah yang jatuh tempo pada periode minggu tersebut.
     * - Angsuran sebelum periode ini yang belum lunas diakumulasikan ke JB (Jumlah Belum Bayar).
     *
     * @param int $minggu
     * @param int $tahun
     * @param bool $forceResync Jika true, akan menambahkan pinjaman baru yang belum masuk ke DKH yang sudah ada
     * @return array Rekap hasil generate [created, updated, skipped, total_dc]
     */
    public function generateUntukMinggu(int $minggu, int $tahun, bool $forceResync = false): array
    {
        $periode = $this->getPeriodeMinggu($tahun, $minggu);
        $periodeMulai = $periode['dari'];
        $periodeSelesai = $periode['sampai'];
        $tanggalDkh = Carbon::today();

        $dcList = Dc::query()->orderBy('nama_dc', 'asc')->get();

        $createdCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($dcList as $dc) {
                $dkh = Dkh::query()
                    ->where('dc_id', $dc->id)
                    ->where('minggu_ke', $minggu)
                    ->where('tahun', $tahun)
                    ->first();

                // Jika DKH sudah ada dan tidak diminta sinkronisasi ulang
                if ($dkh && !$forceResync) {
                    $skippedCount++;
                    continue;
                }

                $isNew = false;
                if (!$dkh) {
                    $dkh = Dkh::create([
                        'dc_id' => $dc->id,
                        'nomor_dkh' => $this->generateNomorDkh($dc->id, $minggu, $tahun),
                        'tanggal_dkh' => $tanggalDkh,
                        'minggu_ke' => $minggu,
                        'tahun' => $tahun,
                        'total_nasabah' => 0,
                        'total_jb' => 0,
                        'total_angsuran' => 0,
                        'total_denda' => 0,
                        'total_tagihan' => 0,
                        'status' => 'draft',
                    ]);
                    $isNew = true;
                }

                // Ambil seluruh pinjaman aktif milik DC ini
                $pinjamanList = Pinjaman::with(['nasabah', 'angsuran'])
                    ->where('dc_id', $dc->id)
                    ->where('status', 'aktif')
                    ->where('sisa_tagihan', '>', 0)
                    ->orderBy('id')
                    ->get();

                foreach ($pinjamanList as $pinjaman) {
                    $nasabah = $pinjaman->nasabah;
                    if (!$nasabah) continue;

                    // Cek apakah pinjaman ini sudah ada di detail DKH ini
                    $detailAda = DkhDetail::where('dkh_id', $dkh->id)
                        ->where('pinjaman_id', $pinjaman->id)
                        ->first();

                    if ($detailAda) {
                        // Jika sudah ada dan sudah ada pembayaran, jangan timpa
                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | 🔥 FILTER ATURAN TANGGAL CAIR:
                    | 1. Angsuran pertama jatuh tempo = tanggal_cair + 1 minggu (7 hari).
                    | 2. Jika tanggal jatuh tempo pertama masih berada SETELAH periode selesai DKH ini,
                    |    berarti pinjaman baru saja cair di minggu ini (atau masa depan).
                    |    Nasabah BELUM boleh ditagihkan di DKH minggu ini -> SKIP!
                    |--------------------------------------------------------------------------
                    */
                    $tanggalCair = Carbon::parse($pinjaman->tanggal_cair)->startOfDay();
                    $jatuhTempoPertama = $tanggalCair->copy()->addDays(7);

                    if ($jatuhTempoPertama->gt($periodeSelesai)) {
                        // Pinjaman baru cair di minggu ini, belum jatuh tempo angsuran 1
                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | 🔥 CARI ANGSURAN YANG JATUH TEMPO DI MINGGU INI
                    |--------------------------------------------------------------------------
                    */
                    $angsuranMingguIni = $pinjaman->angsuran
                        ->where('tanggal_jatuh_tempo', '>=', $periodeMulai->format('Y-m-d'))
                        ->where('tanggal_jatuh_tempo', '<=', $periodeSelesai->format('Y-m-d'))
                        ->first();

                    // Fallback jika tanggal jatuh tempo bergeser: cari berdasarkan selisih minggu dari tanggal cair
                    if (!$angsuranMingguIni) {
                        $selisihMinggu = $tanggalCair->diffInWeeks($periodeSelesai);
                        if ($selisihMinggu >= 1 && $selisihMinggu <= $pinjaman->tenor) {
                            $angsuranMingguIni = $pinjaman->angsuran
                                ->where('minggu_ke', $selisihMinggu)
                                ->first();
                        }
                    }

                    // Fallback lanjutan: ambil angsuran tertua yang belum lunas jika seluruh jadwal normal sudah lewat
                    if (!$angsuranMingguIni) {
                        $angsuranMingguIni = $pinjaman->angsuran
                            ->where('status', '!=', 'lunas')
                            ->where('sisa_tagihan', '>', 0)
                            ->sortBy('minggu_ke')
                            ->first();
                    }

                    if (!$angsuranMingguIni) {
                        continue; // Semua angsuran pinjaman sudah lunas
                    }

                    // Jika angsuran minggu ini sudah lunas lebih awal (misal bayar ganda / prepaid)
                    // Cek apakah ada tunggakan JB sebelumnya
                    $jb = $this->hitungJB($pinjaman->id, $angsuranMingguIni->minggu_ke, $periodeMulai);

                    if ($angsuranMingguIni->status == 'lunas') {
                        if ($jb <= 0) {
                            // Angsuran minggu ini sudah lunas dan tidak ada tunggakan -> SKIP
                            continue;
                        }
                        // Ada tunggakan JB tapi angsuran minggu ini lunas
                        $nominalAngsuran = 0;
                    } else {
                        $nominalAngsuran = $angsuranMingguIni->sisa_tagihan;
                    }

                    $denda = 0;
                    $totalTagihan = $jb + $nominalAngsuran + $denda;

                    DkhDetail::create([
                        'dkh_id' => $dkh->id,
                        'pinjaman_id' => $pinjaman->id,
                        'nasabah_id' => $nasabah->id,
                        'angsuran_id' => $angsuranMingguIni->id,
                        'jb' => $jb,
                        'angsuran' => $nominalAngsuran,
                        'bayar_ke' => $angsuranMingguIni->minggu_ke,
                        'jumlah_minggu' => $pinjaman->tenor,
                        'denda' => $denda,
                        'total_tagihan' => $totalTagihan,
                        'jumlah_bayar' => 0,
                        'sisa_bayar' => $totalTagihan,
                        'status' => 'belum_bayar',
                    ]);
                }

                // Hitung ulang total di header DKH berdasarkan seluruh detail yang ada
                $detailList = DkhDetail::where('dkh_id', $dkh->id)->get();
                $totalNasabah = $detailList->pluck('nasabah_id')->unique()->count();
                $totalJB = $detailList->sum('jb');
                $totalAngsuran = $detailList->sum('angsuran');
                $totalDenda = $detailList->sum('denda');
                $totalTagihan = $detailList->sum('total_tagihan');
                $totalSisa = $detailList->sum('sisa_bayar');

                // Status 'selesai' HANYA jika ada nasabah dan seluruh tagihan telah lunas
                $statusBaru = ($totalNasabah > 0 && $totalSisa <= 0)
                    ? 'selesai'
                    : ($dkh->status == 'dicetak' ? 'dicetak' : 'draft');

                $dkh->update([
                    'total_nasabah' => $totalNasabah,
                    'total_jb' => $totalJB,
                    'total_angsuran' => $totalAngsuran,
                    'total_denda' => $totalDenda,
                    'total_tagihan' => $totalTagihan,
                    'status' => $statusBaru,
                ]);

                if ($isNew) {
                    $createdCount++;
                } else {
                    $updatedCount++;
                }
            }

            DB::commit();

            return [
                'status' => true,
                'created' => $createdCount,
                'updated' => $updatedCount,
                'skipped' => $skippedCount,
                'total_dc' => $dcList->count(),
                'minggu' => $minggu,
                'tahun' => $tahun,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('DkhService::generateUntukMinggu Error: ' . $e->getMessage(), [
                'minggu' => $minggu,
                'tahun' => $tahun,
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'status' => false,
                'error' => $e->getMessage(),
                'minggu' => $minggu,
                'tahun' => $tahun,
            ];
        }
    }

    /**
     * Hitung JB (Jumlah Belum Bayar) dari angsuran-angsuran sebelum minggu berjalan
     */
    private function hitungJB(int $pinjamanId, int $mingguKe, Carbon $periodeMulai): int
    {
        return (int) Angsuran::query()
            ->where('pinjaman_id', $pinjamanId)
            ->where('minggu_ke', '<', $mingguKe)
            ->where('status', '!=', 'lunas')
            ->sum('sisa_tagihan');
    }

    /**
     * Generate Nomor DKH unik
     */
    private function generateNomorDkh(int $dcId, int $minggu, int $tahun): string
    {
        $prefix = sprintf('DKH-%d%02d-DC%03d', $tahun, $minggu, $dcId);
        $count = Dkh::query()
            ->where('nomor_dkh', 'like', $prefix . '-%')
            ->count();
        $urut = str_pad($count + 1, 4, '0', STR_PAD_LEFT);

        return sprintf('%s-%s', $prefix, $urut);
    }
}
