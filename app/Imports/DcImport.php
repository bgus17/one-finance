<?php

namespace App\Imports;

use App\Models\Nasabah;
use App\Models\Pinjaman;
use App\Models\PaketPinjaman;
use App\Models\Angsuran;
use App\Models\Pembayaran;
use App\Models\KasMasuk;
use App\Models\KasKeluar;
use App\Models\Modal;
use App\Models\Desa;
use App\Models\Dc;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DcImport implements ToCollection, WithHeadingRow
{
    protected $dcId;
    protected $desaMapping;
    protected $errors = [];
    protected $warnings = [];
    protected $successCount = 0;
    protected $importedPinjamanIds = [];

    public function __construct($dcId)
    {
        $this->dcId = $dcId;
        $this->desaMapping = [
            1 => 'KARANG MANIK', 2 => 'MARGOMULYO', 3 => 'SUMBER RAHAYU',
            4 => 'SRIBANTOLO', 5 => 'NUSA AGUNG', 6 => 'SINUMARGA',
            7 => 'NUSA JAYA', 8 => 'NUSA BAKTI', 12 => 'NUSA TENGGARA',
            13 => 'KARANG JADI', 14 => 'KARYA MAJU', 15 => 'CAHYA MAS',
            16 => 'SUMBER REJO', 17 => 'KARYA MAKMUR', 18 => 'SUKA JAYA',
            19 => 'NUSA MAJU', 20 => 'NUSA BALI', 21 => 'SINAR BALI',
            22 => 'SUMBER JAYA', 23 => 'ULAK BUNTAR', 24 => 'KARYA USAHA',
            25 => 'SRI WANGI', 28 => 'KARANG JADI', 29 => 'NUSA TUNGGAL',
            30 => 'SRI WANGI', 31 => 'SUMBER JAYA', 32 => 'NUSA MAJU',
            11 => 'PURWOSARI',
        ];
    }

    public function collection(Collection $rows)
    {
        DB::beginTransaction();
        try {
            $dc = Dc::find($this->dcId);
            if (!$dc) throw new \Exception("DC tidak ditemukan");
            $modal = Modal::where('status', 'aktif')->first();
            if (!$modal) throw new \Exception("Modal aktif tidak ditemukan");

            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            foreach ($rows as $index => $row) {
                try {
                    $this->importRow($row, $index + 2, $dc, $modal);
                    $this->successCount++;
                } catch (\Exception $e) {
                    $this->errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            if (empty($this->errors)) {
                DB::commit();
                return [
                    'success' => true,
                    'count' => $this->successCount,
                    'message' => "✅ Berhasil import {$this->successCount} data!",
                    'dc_id' => $this->dcId,
                    'dc_nama' => $dc->nama_dc,
                    'warnings' => $this->warnings,
                ];
            } else {
                DB::rollBack();
                return [
                    'success' => false,
                    'errors' => $this->errors,
                    'warnings' => $this->warnings,
                    'count' => $this->successCount,
                ];
            }
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            DB::rollBack();
            Log::error('DcImport Error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function importRow($row, $rowNumber, $dc, $modal)
    {
        if (empty($row['nama'])) throw new \Exception("Nama tidak boleh kosong");

        $nasabah = $this->prosesNasabah($row);
        if (!$nasabah) throw new \Exception("Gagal memproses nasabah");

        if (!empty($row['pinjaman']) && !empty($row['jangka_waktu'])) {
            $pinjaman = $this->prosesPinjaman($nasabah, $row, $modal);
            if ($pinjaman) $this->importedPinjamanIds[] = $pinjaman->id;
        }
    }

    private function prosesNasabah($row)
    {
        $phoneRaw = $this->getColumnValue($row, ['no_telp', 'no_telpon', 'no_tlpn', 'no_hp', 'telepon', 'no telp', 'no telpon', 'no tlpn']);

        $data = [
            'nama' => trim($row['nama'] ?? ''),
            'nik' => $this->cleanNik($row['nik'] ?? ''),
            'alamat' => trim($row['alamat'] ?? ''),
            'dc_id' => $this->dcId,
            'no_hp' => $this->cleanPhone($phoneRaw ?? '') ?? '-',
            'pekerjaan' => trim($row['pekerjaan'] ?? '') ?: 'WIRASWASTA',
            'status' => 'aktif',
        ];
        if (!empty($row['ttl'])) {
            $cleanTtl = str_replace('`', '', $row['ttl']);
            $ttlParts = explode(',', $cleanTtl);
            if (count($ttlParts) >= 2) $data['tanggal_lahir'] = $this->parseDate(trim($ttlParts[1]));
        }
        if (!empty($row['kode_desa'])) {
            $kodeDesa = (int) $row['kode_desa'];
            if (isset($this->desaMapping[$kodeDesa])) {
                $desa = Desa::where('nama_desa', $this->desaMapping[$kodeDesa])->first();
                if ($desa) $data['desa_id'] = $desa->id;
            }
        }
        // Fallback pencocokan desa berdasarkan nama alamat
        if (empty($data['desa_id']) && !empty($row['alamat'])) {
            $alamatClean = strtoupper(trim($row['alamat']));
            if ($alamatClean === 'SENUMARGA' || $alamatClean === 'SINOMARGA') $alamatClean = 'SINUMARGA';
            if ($alamatClean === 'NUSAJAYA') $alamatClean = 'NUSA JAYA';
            if ($alamatClean === 'SUMBER RAHYAU') $alamatClean = 'SUMBER RAHAYU';
            if ($alamatClean === 'SRIWANGI') $alamatClean = 'SRI WANGI';
            
            $desa = Desa::where('nama_desa', $alamatClean)->first();
            if ($desa) {
                $data['desa_id'] = $desa->id;
            }
        }

        if (!empty($data['nik'])) {
            $nasabah = Nasabah::where('nik', $data['nik'])->first();
            if ($nasabah) { $nasabah->update($data); return $nasabah; }
        }
        if (!empty($data['nama']) && !empty($data['alamat'])) {
            $nasabah = Nasabah::where('nama', $data['nama'])->where('alamat', $data['alamat'])->where('dc_id', $data['dc_id'])->first();
            if ($nasabah) return $nasabah;
        }

        $data['kode_nasabah'] = $this->generateKodeNasabah();
        return Nasabah::create($data);
    }

    private function prosesPinjaman($nasabah, $row, $modal)
    {
        $nominal = $this->parseNominal($row['pinjaman']);
        $tenor = (int) $row['jangka_waktu'];
        if ($nominal <= 0 || $tenor <= 0) throw new \Exception("Nominal atau tenor tidak valid");

        // Deteksi sudah bayar vs angsuran ke
        $sudahBayarRaw = $this->getColumnValue($row, ['sudah_bayar', 'sudah bayar', 'sudah_baya', 'sudah baya']);
        if ($sudahBayarRaw !== null && $sudahBayarRaw !== '') {
            $sudahBayar = (int) $sudahBayarRaw;
            if ($sudahBayar > $tenor) {
                $this->warnings[] = "Sudah bayar ({$sudahBayar}) melebihi tenor ({$tenor}), disesuaikan menjadi lunas";
                $sudahBayar = $tenor;
            }
            if ($sudahBayar < 0) $sudahBayar = 0;
            $mingguBerjalan = min($tenor, $sudahBayar + 1);
        } else {
            $angsuranKe = $this->getColumnValue($row, ['angsuran_ke', 'angsuran ke', 'ANGSURAN KE', 'Angsuran Ke']);
            $mingguBerjalan = (int) ($angsuranKe ?? 1);
            if ($mingguBerjalan > $tenor) { 
                $this->warnings[] = "Angsuran ke melebihi tenor, dianggap lunas"; 
                $mingguBerjalan = $tenor; 
            }
            if ($mingguBerjalan < 1) $mingguBerjalan = 1;
            $sudahBayar = $mingguBerjalan - 1;
        }
        if ($sudahBayar < 0) $sudahBayar = 0;

        $tanggalCair = Carbon::now()->subWeeks($mingguBerjalan);

        $existing = Pinjaman::where('nasabah_id', $nasabah->id)->where('status', 'aktif')->first();
        if ($existing) { $this->warnings[] = "Nasabah sudah punya pinjaman aktif"; return null; }

        $paket = PaketPinjaman::where('nominal_pinjaman', $nominal)
            ->where('tenor_minggu', $tenor)
            ->where('status', 'aktif')
            ->first();

        if (!$paket) {
            throw new \Exception("Paket pinjaman untuk nominal Rp" . number_format($nominal,0,',','.') . 
                               " dengan tenor {$tenor} minggu tidak ditemukan. Silakan buat paket terlebih dahulu.");
        }

        $angsuranPerMinggu = $paket->angsuran_mingguan;
        $totalTagihan = $paket->total_tagihan;
        $admin = $paket->nominal_adm ?? 0;
        $nominalDicairkan = $paket->nominal_dicairkan ?? $nominal;

        $sisaTagihan = $totalTagihan - ($sudahBayar * $angsuranPerMinggu);
        if ($sisaTagihan < 0) $sisaTagihan = 0;

        $pinjaman = Pinjaman::create([
            'nasabah_id' => $nasabah->id,
            'dc_id' => $this->dcId,
            'paket_pinjaman_id' => $paket->id,
            'jumlah_pinjam' => $nominal,
            'nominal_adm' => $admin,
            'nominal_dicairkan' => $nominalDicairkan,
            'total_pinjaman' => $totalTagihan,
            'sisa_tagihan' => $sisaTagihan,
            'tenor' => $tenor,
            'angsuran_per_minggu' => $angsuranPerMinggu,
            'saldo' => 0,
            'tanggal_pengajuan' => $tanggalCair,
            'tanggal_cair' => $tanggalCair,
            'jatuh_tempo' => $tanggalCair->copy()->addWeeks($tenor),
            'minggu_berjalan' => $mingguBerjalan,
            'status' => $sisaTagihan > 0 ? 'aktif' : 'lunas',
        ]);

        for ($i = 1; $i <= $tenor; $i++) {
            $jatuhTempo = $tanggalCair->copy()->addWeeks($i);
            if ($i <= $sudahBayar) {
                $angsuran = Angsuran::create([
                    'pinjaman_id' => $pinjaman->id,
                    'minggu_ke' => $i,
                    'tanggal_jatuh_tempo' => $jatuhTempo,
                    'jumlah_tagihan' => $angsuranPerMinggu,
                    'jumlah_bayar' => $angsuranPerMinggu,
                    'sisa_tagihan' => 0,
                    'tanggal_lunas' => $jatuhTempo,
                    'status' => 'lunas',
                ]);
                $this->buatPembayaran($pinjaman, $angsuran, $jatuhTempo, $angsuranPerMinggu, $modal);
            } else {
                Angsuran::create([
                    'pinjaman_id' => $pinjaman->id,
                    'minggu_ke' => $i,
                    'tanggal_jatuh_tempo' => $jatuhTempo,
                    'jumlah_tagihan' => $angsuranPerMinggu,
                    'jumlah_bayar' => 0,
                    'sisa_tagihan' => $angsuranPerMinggu,
                    'tanggal_lunas' => null,
                    'status' => 'belum_bayar',
                ]);
            }
        }

        $this->catatKasKeluar($pinjaman, $tanggalCair, $modal);
        $this->catatKasMasukAdmin($pinjaman, $tanggalCair, $modal);

        $this->warnings[] = "✅ {$nasabah->nama}: Pinjaman Rp" . number_format($nominal,0,',','.') . 
                            ", tenor {$tenor} minggu, angsuran ke-{$mingguBerjalan}";

        return $pinjaman;
    }

    private function buatPembayaran($pinjaman, $angsuran, $tanggalBayar, $jumlahBayar, $modal)
    {
        try {
            $totalTagihan = $pinjaman->total_pinjaman ?? 0;
            $pokokPinjaman = $pinjaman->jumlah_pinjam ?? 0;

            // Fallback jika total_pinjaman di DB belum terisi/0
            if ($totalTagihan <= 0) {
                if (!$pinjaman->relationLoaded('paketPinjaman')) {
                    $pinjaman->load('paketPinjaman');
                }
                $paket = $pinjaman->paketPinjaman;

                if ($paket && $paket->bunga > 0) {
                    $totalBunga = ($pokokPinjaman * $paket->bunga / 100);
                    $totalTagihan = $pokokPinjaman + $totalBunga;
                }
            }

            $pokokBayar = $jumlahBayar;
            $bungaBayar = 0;

            // Hitung Pokok & Margin secara proporsional
            if ($totalTagihan > 0 && $pokokPinjaman > 0) {
                $totalBunga = $totalTagihan - $pokokPinjaman;
                if ($totalBunga > 0) {
                    $proporsiMargin = $totalBunga / $totalTagihan;
                    $bungaBayar = round($jumlahBayar * $proporsiMargin, 2);
                    $pokokBayar = round($jumlahBayar - $bungaBayar, 2);
                }
            }

            $pembayaran = Pembayaran::create([
                'angsuran_id'   => $angsuran->id,
                'dc_id'         => $this->dcId,
                'dkh_detail_id' => null,
                'jumlah_tagihan'=> $jumlahBayar,
                'jumlah_bayar'  => $jumlahBayar,
                'sisa_tagihan'  => 0,
                'tanggal_bayar' => $tanggalBayar,
                'metode_bayar'  => 'kas',
                'keterangan'    => 'Pembayaran angsuran ke-' . $angsuran->minggu_ke . ' (import)',
                'pokok'         => $pokokBayar,
                'margin'        => $bungaBayar,
            ]);

            $angsuran->update([
                'jumlah_bayar'  => $jumlahBayar,
                'sisa_tagihan'  => 0,
                'tanggal_lunas' => $tanggalBayar,
                'status'        => 'lunas',
                'updated_at'    => $tanggalBayar,
            ]);

            $modal->saldo += $jumlahBayar;
            $modal->save();

            KasMasuk::create([
                'modal_id'       => $modal->id,
                'dc_id'          => $this->dcId,
                'tanggal'        => $tanggalBayar,
                'jenis'          => 'Pembayaran Angsuran',
                'nominal'        => $jumlahBayar,
                'referensi'      => 'Pembayaran: ' . $pembayaran->id,
                'referensi_id'   => $pembayaran->id,
                'referensi_type' => Pembayaran::class,
                'keterangan'     => 'Pembayaran angsuran ke-' . $angsuran->minggu_ke . ' (import)',
            ]);

        } catch (\Exception $e) {
            $this->warnings[] = "Gagal buat pembayaran: " . $e->getMessage();
        }
    }

    private function catatKasKeluar($pinjaman, $tanggalCair, $modal)
    {
        try {
            $nominal = $pinjaman->nominal_dicairkan;
            if ($nominal <= 0) return;
            $modal->saldo -= $nominal;
            $modal->save();
            KasKeluar::create([
                'modal_id' => $modal->id,
                'dc_id' => $this->dcId,
                'tanggal' => $tanggalCair,
                'jenis' => 'Pencairan Pinjaman',
                'nominal' => $nominal,
                'referensi' => 'Pinjaman: ' . $pinjaman->id,
                'referensi_id' => $pinjaman->id,
                'referensi_type' => Pinjaman::class,
                'keterangan' => 'Pencairan pinjaman',
            ]);
        } catch (\Exception $e) {
            $this->warnings[] = "Gagal catat kas keluar: " . $e->getMessage();
        }
    }

    private function catatKasMasukAdmin($pinjaman, $tanggalCair, $modal)
    {
        try {
            $nominal = $pinjaman->nominal_adm;
            if ($nominal <= 0) return;
            $modal->saldo += $nominal;
            $modal->save();
            KasMasuk::create([
                'modal_id' => $modal->id,
                'dc_id' => $this->dcId,
                'tanggal' => $tanggalCair,
                'jenis' => 'Biaya Administrasi',
                'nominal' => $nominal,
                'referensi' => 'Pinjaman: ' . $pinjaman->id,
                'referensi_id' => $pinjaman->id,
                'referensi_type' => Pinjaman::class,
                'keterangan' => 'Biaya admin pinjaman',
            ]);
        } catch (\Exception $e) {
            $this->warnings[] = "Gagal catat kas masuk admin: " . $e->getMessage();
        }
    }

    private function getColumnValue($row, array $possibleNames)
    {
        foreach ($possibleNames as $name) {
            if (isset($row[$name])) return $row[$name];
            $underscore = str_replace(' ', '_', $name);
            if (isset($row[$underscore])) return $row[$underscore];
            $lowercase = strtolower($name);
            if (isset($row[$lowercase])) return $row[$lowercase];
            $uppercase = strtoupper($name);
            if (isset($row[$uppercase])) return $row[$uppercase];
        }
        return null;
    }

    private function parseNominal($value)
    {
        if (empty($value)) return 0;
        if (is_string($value)) {
            $value = str_replace(['Rp', 'rp', ' ', '.', ',', '"', "'"], '', $value);
        }
        return (int) $value;
    }

    private function parseDate($value)
    {
        if (empty($value)) return null;
        try {
            if (is_numeric($value) && $value > 0) {
                $unixTimestamp = ($value - 25569) * 86400;
                return Carbon::createFromTimestamp($unixTimestamp)->startOfDay();
            }
            $value = trim($value);
            if (preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
                return Carbon::createFromFormat('Y-m-d', substr($value, 0, 10));
            }
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                return Carbon::createFromFormat('Y-m-d', $value);
            }
            if (preg_match('/^\d{2}-\d{2}-\d{4}/', $value)) {
                return Carbon::createFromFormat('d-m-Y', substr($value, 0, 10));
            }
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}/', $value)) {
                return Carbon::createFromFormat('d/m/Y', substr($value, 0, 10));
            }
            if (preg_match('/^\d{4}\/\d{2}\/\d{2}/', $value)) {
                return Carbon::createFromFormat('Y/m/d', substr($value, 0, 10));
            }
            $timestamp = strtotime($value);
            if ($timestamp && $timestamp > 0) {
                return Carbon::createFromTimestamp($timestamp);
            }
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function cleanNik($nik)
    {
        if (empty($nik)) return null;
        $nik = trim(str_replace('*', '', $nik));
        $nik = preg_replace('/[^0-9]/', '', $nik);
        if (strlen($nik) < 10) return null;
        return $nik;
    }

    private function cleanPhone($phone)
    {
        if (empty($phone)) return null;
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (empty($phone)) return '-';
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (str_starts_with($phone, '8')) {
            $phone = '62' . $phone;
        } elseif (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }
        return $phone;
    }

    private function generateKodeNasabah()
    {
        $tahun = date('Y');
        $bulan = date('m');
        $last = Nasabah::where('dc_id', $this->dcId)
            ->whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bulan)
            ->orderBy('id', 'desc')
            ->first();

        if ($last && $last->kode_nasabah) {
            $angka = (int) substr($last->kode_nasabah, -4);
            $urutan = str_pad($angka + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $urutan = '0001';
        }
        return 'DC' . str_pad($this->dcId, 2, '0', STR_PAD_LEFT) . $tahun . $bulan . $urutan;
    }

    public function getErrors() { return $this->errors; }
    public function getWarnings() { return $this->warnings; }
    public function getCount() { return $this->successCount; }
}