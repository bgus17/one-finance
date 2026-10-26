<?php

namespace App\Http\Controllers;

use App\Models\KasMasuk;
use App\Models\KasKeluar;
use App\Models\Pinjaman;
use App\Models\Dkh;
use App\Models\DkhDetail;
use App\Models\Modal;
use App\Models\Dc;
use App\Models\Angsuran;
use App\Models\Pembayaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    /**
     * DASHBOARD LAPORAN
     */
    public function index()
    {
        $totalKasMasuk = KasMasuk::sum('nominal');
        $totalKasKeluar = KasKeluar::sum('nominal');
        $saldoAkhir = $totalKasMasuk - $totalKasKeluar;

        $totalPinjaman = Pinjaman::count();
        $pinjamanAktif = Pinjaman::where('status', 'aktif')->count();
        $pinjamanLunas = Pinjaman::where('status', 'lunas')->count();
        $pinjamanDitolak = Pinjaman::where('status', 'ditolak')->count();

        $modal = Modal::where('status', 'aktif')->first();
        $saldoModal = $modal ? $modal->saldo : 0;

        return view('laporan.index', compact(
            'totalKasMasuk',
            'totalKasKeluar',
            'saldoAkhir',
            'saldoModal',
            'totalPinjaman',
            'pinjamanAktif',
            'pinjamanLunas',
            'pinjamanDitolak'
        ));
    }

    /**
     * HITUNG RENTANG TANGGAL (FIXED PRECISI JAM)
     * 🔥 PERIODE MINGGUAN = SELASA - SENIN
     */
    private function getRangeTanggal($periode, $tahun, $bulan, $minggu, $tanggal)
    {
        $dari = null;
        $sampai = null;
        $label = '';

        try {
            switch ($periode) {
                case 'harian':
                    $dari = Carbon::parse($tanggal)->startOfDay();
                    $sampai = Carbon::parse($tanggal)->endOfDay();
                    $label = 'Harian - ' . $dari->translatedFormat('d F Y');
                    break;

                case 'mingguan':
                    // setISODate(tahun, minggu, 2) => 2 adalah Selasa
                    $dari = Carbon::now()->setISODate((int)$tahun, (int)$minggu, 2)->startOfDay();
                    $sampai = (clone $dari)->addDays(6)->endOfDay(); // Selasa + 6 hari = Senin
                    $label = 'Mingguan - Minggu ke-' . $minggu . ' (' . $dari->format('d M') . ' - ' . $sampai->format('d M Y') . ')';
                    break;

                case 'bulanan':
                    $dari = Carbon::createFromDate((int)$tahun, (int)$bulan, 1)->startOfMonth();
                    $sampai = Carbon::createFromDate((int)$tahun, (int)$bulan, 1)->endOfMonth()->endOfDay();
                    $label = 'Bulanan - ' . $dari->translatedFormat('F Y');
                    break;

                case 'tahunan':
                    $dari = Carbon::createFromDate((int)$tahun, 1, 1)->startOfYear();
                    $sampai = Carbon::createFromDate((int)$tahun, 12, 31)->endOfYear();
                    $label = 'Tahunan - ' . $tahun;
                    break;

                default:
                    $dari = Carbon::now()->startOfMonth();
                    $sampai = Carbon::now()->endOfDay();
                    $label = 'Bulan Ini';
            }
        } catch (\Exception $e) {
            $dari = Carbon::now()->startOfMonth();
            $sampai = Carbon::now()->endOfDay();
            $label = 'Bulan Ini';
        }

        return [
            'dari' => $dari->format('Y-m-d H:i:s'),
            'sampai' => $sampai->format('Y-m-d H:i:s'),
            'label' => $label,
            'dari_obj' => $dari,
            'sampai_obj' => $sampai,
        ];
    }

    /**
     * LAPORAN KAS
     */
    public function kas(Request $request)
    {
        $periode = $request->periode ?? 'bulanan';
        $tahun = $request->tahun ?? date('Y');
        $bulan = $request->bulan ?? date('m');
        $minggu = $request->minggu ?? date('W');
        $tanggal = $request->tanggal ?? date('Y-m-d');

        $range = $this->getRangeTanggal($periode, $tahun, $bulan, $minggu, $tanggal);
        $dari = $range['dari'];
        $sampai = $range['sampai'];
        $label = $range['label'];

        $kasMasuk = KasMasuk::whereBetween('tanggal', [$dari, $sampai])->get();
        $kasKeluar = KasKeluar::whereBetween('tanggal', [$dari, $sampai])->get();

        $totalMasuk = $kasMasuk->sum('nominal');
        $totalKeluar = $kasKeluar->sum('nominal');
        $selisih = $totalMasuk - $totalKeluar;

        $modal = Modal::where('status', 'aktif')->first();
        $saldoAkhir = $modal ? $modal->saldo : 0;

        $listTahun = KasMasuk::selectRaw('YEAR(tanggal) as tahun')->distinct()->pluck('tahun');
        if ($listTahun->isEmpty()) {
            $listTahun = collect([date('Y')]);
        }

        $listBulan = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
        $listMinggu = range(1, 52);

        return view('laporan.kas', compact(
            'kasMasuk', 'kasKeluar', 'totalMasuk', 'totalKeluar', 'selisih',
            'saldoAkhir', 'label', 'periode', 'tahun', 'bulan', 'minggu', 'tanggal',
            'listTahun', 'listBulan', 'listMinggu'
        ));
    }

    /**
     * LAPORAN PINJAMAN
     */
    public function pinjaman(Request $request)
    {
        $periode = $request->periode ?? 'bulanan';
        $tahun = $request->tahun ?? date('Y');
        $bulan = $request->bulan ?? date('m');
        $minggu = $request->minggu ?? date('W');
        $tanggal = $request->tanggal ?? date('Y-m-d');
        $dcId = $request->dc_id;
        $status = $request->status;

        $range = $this->getRangeTanggal($periode, $tahun, $bulan, $minggu, $tanggal);
        $dari = $range['dari'];
        $sampai = $range['sampai'];
        $label = $range['label'];

        $query = Pinjaman::with(['nasabah', 'paketPinjaman']);

        if ($dcId) {
            $query->where('dc_id', $dcId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($periode == 'mingguan') {
            $dariMinggu = Carbon::now()->setISODate((int)$tahun, (int)$minggu, 2)->startOfDay();
            $sampaiMinggu = (clone $dariMinggu)->addDays(6)->endOfDay();
            $query->whereBetween('tanggal_cair', [$dariMinggu, $sampaiMinggu]);
        } else {
            $query->whereBetween('tanggal_cair', [$dari, $sampai]);
        }

        $pinjaman = $query->orderByDesc('tanggal_cair')->get();

        if ($pinjaman->isEmpty() && $dcId) {
            $query = Pinjaman::with(['nasabah', 'paketPinjaman'])
                ->where('dc_id', $dcId);
            
            if ($status) {
                $query->where('status', $status);
            }
            
            $pinjaman = $query->orderByDesc('tanggal_cair')->get();
        }

        $totalPinjaman = $pinjaman->sum('jumlah_pinjam');
        $totalDicairkan = $pinjaman->sum('nominal_dicairkan');
        $totalSisa = $pinjaman->sum('sisa_tagihan');
        $totalAdmin = $pinjaman->sum('nominal_adm');

        $dcList = Dc::where('status', 'aktif')->get();

        $listTahun = Pinjaman::selectRaw('YEAR(tanggal_cair) as tahun')->distinct()->pluck('tahun');
        if ($listTahun->isEmpty()) {
            $listTahun = collect([date('Y')]);
        }
        $listBulan = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
        $listMinggu = range(1, 52);

        return view('laporan.pinjaman', compact(
            'pinjaman', 'totalPinjaman', 'totalDicairkan', 'totalSisa', 'totalAdmin',
            'label', 'periode', 'tahun', 'bulan', 'minggu', 'tanggal',
            'listTahun', 'listBulan', 'listMinggu', 'dcId', 'dcList', 'status'
        ));
    }

    /**
     * LAPORAN DKH
     */
    public function dkh(Request $request)
    {
        $periode = $request->periode ?? 'bulanan';
        $tahun = $request->tahun ?? date('Y');
        $bulan = $request->bulan ?? date('m');
        $minggu = $request->minggu ?? date('W');
        $tanggal = $request->tanggal ?? date('Y-m-d');
        $dcId = $request->dc_id;

        $range = $this->getRangeTanggal($periode, $tahun, $bulan, $minggu, $tanggal);
        $dari = $range['dari'];
        $sampai = $range['sampai'];
        $label = $range['label'];

        $query = Dkh::with(['dc', 'detail.nasabah']);

        if ($dcId) {
            $query->where('dc_id', $dcId);
        }

        if ($periode == 'mingguan') {
            $query->where('minggu_ke', $minggu)
                  ->where('tahun', $tahun);
        } else {
            $query->whereBetween('tanggal_dkh', [$dari, $sampai]);
        }

        $dkh = $query->orderByDesc('tanggal_dkh')->get();

        if ($dkh->isEmpty() && $dcId) {
            $this->generateDkhManual($dcId);
            $dkh = $query->orderByDesc('tanggal_dkh')->get();
        }

        $totalTagihan = $dkh->sum('total_tagihan');
        $totalNasabah = $dkh->sum('total_nasabah');
        $totalJb = $dkh->sum('total_jb');
        $totalAngsuran = $dkh->sum('total_angsuran');
        $totalDenda = $dkh->sum('total_denda');

        $dcList = Dc::where('status', 'aktif')->get();

        $listTahun = Dkh::selectRaw('YEAR(tanggal_dkh) as tahun')->distinct()->pluck('tahun');
        if ($listTahun->isEmpty()) {
            $listTahun = collect([date('Y')]);
        }
        $listBulan = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
        $listMinggu = range(1, 52);

        return view('laporan.dkh', compact(
            'dkh', 'totalTagihan', 'totalNasabah', 'totalJb', 'totalAngsuran', 'totalDenda',
            'dcList', 'label', 'periode', 'tahun', 'bulan', 'minggu', 'tanggal',
            'listTahun', 'listBulan', 'listMinggu', 'dcId'
        ));
    }

    /**
     * GENERATE DKH MANUAL
     */
    private function generateDkhManual($dcId)
    {
        try {
            $now = Carbon::now();
            $mingguKe = $now->weekOfYear;
            $tahun = $now->year;

            $pinjamanAktif = Pinjaman::where('dc_id', $dcId)
                ->where('status', 'aktif')
                ->with(['nasabah', 'angsuran'])
                ->get();

            if ($pinjamanAktif->isEmpty()) {
                return;
            }

            $dkh = Dkh::where('dc_id', $dcId)
                ->where('minggu_ke', $mingguKe)
                ->where('tahun', $tahun)
                ->first();

            if (!$dkh) {
                $startOfWeek = Carbon::now()->startOfWeek(Carbon::TUESDAY);
                $endOfWeek = Carbon::now()->endOfWeek(Carbon::MONDAY);

                $dkh = Dkh::create([
                    'dc_id' => $dcId,
                    'kode_dkh' => 'DKH-' . $dcId . '-' . $tahun . '-' . $mingguKe,
                    'minggu_ke' => $mingguKe,
                    'tahun' => $tahun,
                    'tanggal_dkh' => $now,
                    'tanggal_mulai' => $startOfWeek,
                    'tanggal_selesai' => $endOfWeek,
                    'total_nasabah' => 0,
                    'total_tagihan' => 0,
                    'total_jb' => 0,
                    'total_angsuran' => 0,
                    'total_denda' => 0,
                    'status' => 'aktif',
                ]);
            }

            DkhDetail::where('dkh_id', $dkh->id)->delete();

            $totalNasabah = 0;
            $totalTagihan = 0;
            $totalJb = 0;

            foreach ($pinjamanAktif as $pinjaman) {
                $angsuranMingguIni = $pinjaman->angsuran()
                    ->where('minggu_ke', $pinjaman->minggu_berjalan)
                    ->first();

                if (!$angsuranMingguIni) continue;

                $isLunas = $angsuranMingguIni->status == 'lunas';
                $tagihan = $angsuranMingguIni->jumlah_tagihan;

                DkhDetail::create([
                    'dkh_id' => $dkh->id,
                    'pinjaman_id' => $pinjaman->id,
                    'nasabah_id' => $pinjaman->nasabah_id,
                    'tagihan' => $tagihan,
                    'jb' => $isLunas ? $tagihan : 0,
                    'denda' => 0,
                    'keterangan' => 'DKH minggu ke-' . $mingguKe,
                    'status' => $angsuranMingguIni->status,
                ]);

                $totalNasabah++;
                $totalTagihan += $tagihan;
                if ($isLunas) $totalJb += $tagihan;
            }

            $dkh->update([
                'total_nasabah' => $totalNasabah,
                'total_tagihan' => $totalTagihan,
                'total_jb' => $totalJb,
                'total_angsuran' => $totalTagihan,
            ]);

        } catch (\Exception $e) {
            \Log::error('Generate DKH manual error: ' . $e->getMessage());
        }
    }

    /**
     * 🔥 LAPORAN LABA RUGI - FINAL VERSION
     */
    public function labarugi(Request $request)
    {
        $periode = $request->periode ?? 'bulanan';
        $tahun = $request->tahun ?? date('Y');
        $bulan = $request->bulan ?? date('m');
        $minggu = $request->minggu ?? date('W');
        $tanggal = $request->tanggal ?? date('Y-m-d');

        $range = $this->getRangeTanggal($periode, $tahun, $bulan, $minggu, $tanggal);
        $dari = $range['dari'];
        $sampai = $range['sampai'];
        $label = $range['label'];

        // ============================================
        // 🔥 PENDAPATAN BUNGA (DARI TABEL PEMBAYARAN)
        // ============================================
        $pendapatanBunga = Pembayaran::whereBetween('tanggal_bayar', [$dari, $sampai])
            ->sum('margin');

        // ============================================
        // 🔥 PENDAPATAN ADMINISTRASI (DARI KAS MASUK)
        // ============================================
        $pendapatanAdmin = KasMasuk::where('jenis', 'Biaya Administrasi')
            ->whereBetween('tanggal', [$dari, $sampai])
            ->sum('nominal');

        // ============================================
        // 🔥 PENDAPATAN DENDA (DARI DKH DETAIL)
        // ============================================
        $pendapatanDenda = DkhDetail::whereHas('dkh', function ($q) use ($dari, $sampai) {
                $q->whereBetween('tanggal_dkh', [$dari, $sampai]);
            })
            ->sum('denda');

        $totalPendapatan = $pendapatanBunga + $pendapatanAdmin + $pendapatanDenda;

        // ============================================
        // 🔥 BIAYA
        // ============================================
        $biayaOperasional = KasKeluar::whereBetween('tanggal', [$dari, $sampai])
            ->where('jenis', 'Biaya Operasional')
            ->sum('nominal');

        $biayaGaji = KasKeluar::whereBetween('tanggal', [$dari, $sampai])
            ->where('jenis', 'Gaji')
            ->sum('nominal');

        $biayaLain = KasKeluar::whereBetween('tanggal', [$dari, $sampai])
            ->where('jenis', 'Lain-lain')
            ->sum('nominal');

        $totalBiaya = $biayaOperasional + $biayaGaji + $biayaLain;

        // ============================================
        // 🔥 LABA / RUGI
        // ============================================
        $labaRugi = $totalPendapatan - $totalBiaya;
        $status = $labaRugi >= 0 ? 'LABA' : 'RUGI';
        $statusColor = $labaRugi >= 0 ? 'success' : 'danger';

        // ============================================
        // 🔥 GRAFIK BULANAN LENGKAP
        // ============================================
        $grafikBulanan = [];
        for ($i = 1; $i <= 12; $i++) {
            $start = Carbon::create($tahun, $i, 1)->startOfMonth()->format('Y-m-d H:i:s');
            $end = Carbon::create($tahun, $i, 1)->endOfMonth()->endOfDay()->format('Y-m-d H:i:s');

            $pBunga = Pembayaran::whereBetween('tanggal_bayar', [$start, $end])->sum('margin');
            $pAdmin = KasMasuk::where('jenis', 'Biaya Administrasi')->whereBetween('tanggal', [$start, $end])->sum('nominal');
            $pDenda = DkhDetail::whereHas('dkh', function ($q) use ($start, $end) {
                $q->whereBetween('tanggal_dkh', [$start, $end]);
            })->sum('denda');

            $pTotal = $pBunga + $pAdmin + $pDenda;
            $bTotal = KasKeluar::whereBetween('tanggal', [$start, $end])
                ->whereIn('jenis', ['Biaya Operasional', 'Gaji', 'Lain-lain'])
                ->sum('nominal');

            $grafikBulanan[] = [
                'bulan' => Carbon::create($tahun, $i, 1)->translatedFormat('M'),
                'pendapatan' => (int) $pTotal,
                'biaya' => (int) $bTotal,
                'laba' => (int) ($pTotal - $bTotal),
            ];
        }

        // ============================================
        // 🔥 DATA UNTUK FILTER
        // ============================================
        $listTahun = Pinjaman::selectRaw('YEAR(tanggal_cair) as tahun')->distinct()->pluck('tahun');
        if ($listTahun->isEmpty()) {
            $listTahun = collect([date('Y')]);
        }
        $listBulan = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
        $listMinggu = range(1, 52);

        return view('laporan.labarugi', compact(
            'pendapatanBunga',
            'pendapatanAdmin',
            'pendapatanDenda',
            'totalPendapatan',
            'biayaOperasional',
            'biayaGaji',
            'biayaLain',
            'totalBiaya',
            'labaRugi',
            'status',
            'statusColor',
            'label',
            'periode',
            'tahun',
            'bulan',
            'minggu',
            'tanggal',
            'listTahun',
            'listBulan',
            'listMinggu',
            'grafikBulanan'
        ));
    }

    /**
     * CETAK PDF KAS
     */
    public function cetakKas(Request $request)
    {
        $periode = $request->periode ?? 'bulanan';
        $tahun = $request->tahun ?? date('Y');
        $bulan = $request->bulan ?? date('m');
        $minggu = $request->minggu ?? date('W');
        $tanggal = $request->tanggal ?? date('Y-m-d');

        $range = $this->getRangeTanggal($periode, $tahun, $bulan, $minggu, $tanggal);
        $dari = $range['dari'];
        $sampai = $range['sampai'];
        $label = $range['label'];

        $kasMasuk = KasMasuk::whereBetween('tanggal', [$dari, $sampai])->get();
        $kasKeluar = KasKeluar::whereBetween('tanggal', [$dari, $sampai])->get();

        $totalMasuk = $kasMasuk->sum('nominal');
        $totalKeluar = $kasKeluar->sum('nominal');
        $selisih = $totalMasuk - $totalKeluar;

        $data = compact('kasMasuk', 'kasKeluar', 'totalMasuk', 'totalKeluar', 'selisih', 'label');

        $pdf = Pdf::loadView('pdf.laporan-kas', $data);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('Laporan_Kas_' . date('Ymd') . '.pdf');
    }

    /**
     * CETAK PDF PINJAMAN
     */
    public function cetakPinjaman(Request $request)
    {
        $periode = $request->periode ?? 'bulanan';
        $tahun = $request->tahun ?? date('Y');
        $bulan = $request->bulan ?? date('m');
        $minggu = $request->minggu ?? date('W');
        $tanggal = $request->tanggal ?? date('Y-m-d');

        $range = $this->getRangeTanggal($periode, $tahun, $bulan, $minggu, $tanggal);
        $dari = $range['dari'];
        $sampai = $range['sampai'];
        $label = $range['label'];

        $pinjaman = Pinjaman::with(['nasabah', 'paketPinjaman'])
            ->whereBetween('tanggal_cair', [$dari, $sampai])
            ->where('status', 'aktif')
            ->get();

        $totalPinjaman = $pinjaman->sum('jumlah_pinjam');
        $totalDicairkan = $pinjaman->sum('nominal_dicairkan');
        $totalSisa = $pinjaman->sum('sisa_tagihan');

        $data = compact('pinjaman', 'totalPinjaman', 'totalDicairkan', 'totalSisa', 'label');

        $pdf = Pdf::loadView('pdf.laporan-pinjaman', $data);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('Laporan_Pinjaman_' . date('Ymd') . '.pdf');
    }

    /**
     * 🔥 CETAK PDF LABA RUGI - FINAL VERSION
     */
    public function cetakLabarugi(Request $request)
    {
        $periode = $request->periode ?? 'bulanan';
        $tahun = $request->tahun ?? date('Y');
        $bulan = $request->bulan ?? date('m');
        $minggu = $request->minggu ?? date('W');
        $tanggal = $request->tanggal ?? date('Y-m-d');

        $range = $this->getRangeTanggal($periode, $tahun, $bulan, $minggu, $tanggal);
        $dari = $range['dari'];
        $sampai = $range['sampai'];
        $label = $range['label'];

        $pendapatanBunga = Pembayaran::whereBetween('tanggal_bayar', [$dari, $sampai])->sum('margin');
        $pendapatanAdmin = KasMasuk::where('jenis', 'Biaya Administrasi')->whereBetween('tanggal', [$dari, $sampai])->sum('nominal');
        $pendapatanDenda = DkhDetail::whereHas('dkh', function ($q) use ($dari, $sampai) {
                $q->whereBetween('tanggal_dkh', [$dari, $sampai]);
            })->sum('denda');

        $totalPendapatan = $pendapatanBunga + $pendapatanAdmin + $pendapatanDenda;

        $biayaOperasional = KasKeluar::whereBetween('tanggal', [$dari, $sampai])->where('jenis', 'Biaya Operasional')->sum('nominal');
        $biayaGaji = KasKeluar::whereBetween('tanggal', [$dari, $sampai])->where('jenis', 'Gaji')->sum('nominal');
        $biayaLain = KasKeluar::whereBetween('tanggal', [$dari, $sampai])->where('jenis', 'Lain-lain')->sum('nominal');

        $totalBiaya = $biayaOperasional + $biayaGaji + $biayaLain;

        $labaRugi = $totalPendapatan - $totalBiaya;
        $status = $labaRugi >= 0 ? 'LABA' : 'RUGI';

        $data = compact(
            'pendapatanBunga',
            'pendapatanAdmin',
            'pendapatanDenda',
            'totalPendapatan',
            'biayaOperasional',
            'biayaGaji',
            'biayaLain',
            'totalBiaya',
            'labaRugi',
            'status',
            'label'
        );

        $pdf = Pdf::loadView('pdf.laporan-labarugi', $data);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('Laporan_LabaRugi_' . date('Ymd') . '.pdf');
    }

    /**
     * 🔥 CETAK PDF DKH
     */
    public function cetakDkh(Request $request)
    {
        $periode = $request->periode ?? 'bulanan';
        $tahun = $request->tahun ?? date('Y');
        $bulan = $request->bulan ?? date('m');
        $minggu = $request->minggu ?? date('W');
        $tanggal = $request->tanggal ?? date('Y-m-d');
        $dcId = $request->dc_id;

        $range = $this->getRangeTanggal($periode, $tahun, $bulan, $minggu, $tanggal);
        $dari = $range['dari'];
        $sampai = $range['sampai'];

        $query = Dkh::with(['dc', 'detail.nasabah']);

        if ($dcId) {
            $query->where('dc_id', $dcId);
        }

        if ($periode == 'mingguan') {
            $query->where('minggu_ke', $minggu)
                  ->where('tahun', $tahun);
        } else {
            $query->whereBetween('tanggal_dkh', [$dari, $sampai]);
        }

        $dkh = $query->orderByDesc('tanggal_dkh')->get();

        $totalTagihan = $dkh->sum('total_tagihan');
        $totalNasabah = $dkh->sum('total_nasabah');
        $totalJb = $dkh->sum('total_jb');
        $totalAngsuran = $dkh->sum('total_angsuran');
        $totalDenda = $dkh->sum('total_denda');

        $data = compact(
            'dkh', 'totalTagihan', 'totalNasabah', 'totalJb', 
            'totalAngsuran', 'totalDenda', 'label', 'dcId'
        );

        $pdf = Pdf::loadView('pdf.laporan-dkh', $data);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('Laporan_DKH_' . date('Ymd') . '.pdf');
    }
}