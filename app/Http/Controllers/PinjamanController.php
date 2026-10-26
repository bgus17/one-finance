<?php

namespace App\Http\Controllers;

use App\Models\Angsuran;
use App\Models\KasKeluar;
use App\Models\KasMasuk;
use App\Models\Modal;
use App\Models\Nasabah;
use App\Models\PaketPinjaman;
use App\Models\Pembayaran;
use App\Models\Pinjaman;
use App\Models\DkhDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class PinjamanController extends Controller
{
    /**
     * Daftar Pinjaman
     */
    public function index()
    {
        $pinjaman = Pinjaman::with([
            'nasabah',
            'paketPinjaman',
        ])
            ->latest()
            ->get();

        return view(
            'pinjaman.index',
            compact('pinjaman')
        );
    }

    /**
     * Form Tambah Pinjaman
     */
    public function create()
    {
        $nasabah = Nasabah::query()
            ->where('status', 'aktif')
            ->get();

        $paketPinjaman = PaketPinjaman::query()
            ->where('status', 'aktif')
            ->orderBy('nominal_pinjaman')
            ->get();

        // Ambil saldo modal
        $modal = Modal::query()->where('status', 'aktif')->first();
        $saldoModal = $modal ? $modal->saldo : 0;

        return view(
            'pinjaman.create',
            compact(
                'nasabah',
                'paketPinjaman',
                'saldoModal'
            )
        );
    }

    /**
     * Simpan Pinjaman
     */
    public function store(Request $request)
    {
        $request->validate([
            'nasabah_id' => 'required|exists:nasabah,id',
            'paket_pinjaman_id' => 'required|exists:paket_pinjaman,id',
            'tanggal_pengajuan' => 'required|date',
            'tanggal_cair' => 'required|date',
        ]);

        /*
        |--------------------------------------------------------------------------
        | DATA NASABAH & PAKET
        |--------------------------------------------------------------------------
        */

        $nasabah = Nasabah::findOrFail($request->nasabah_id);
        $paket = PaketPinjaman::findOrFail($request->paket_pinjaman_id);

        /*
        |--------------------------------------------------------------------------
        | CEK SALDO MODAL
        |--------------------------------------------------------------------------
        */

        $modal = Modal::query()->where('status', 'aktif')->first();

        if (!$modal) {
            return back()->with('error', 'Data modal tidak ditemukan! Hubungi admin.');
        }

        if (!$modal->saldoMencukupi($paket->nominal_dicairkan)) {
            return back()->with('error', 'Saldo kas tidak mencukupi! Saldo saat ini: Rp ' . number_format($modal->saldo, 0, ',', '.'));
        }

        /*
        |--------------------------------------------------------------------------
        | PROSES TRANSAKSI (Gunakan Database Transaction)
        |--------------------------------------------------------------------------
        */

        $pinjaman = null;

        DB::transaction(function () use ($request, $nasabah, $paket, $modal, &$pinjaman) {

            /*
            |--------------------------------------------------------------------------
            | TANGGAL JATUH TEMPO
            |--------------------------------------------------------------------------
            */

            $jatuhTempo = Carbon::parse(
                $request->tanggal_cair
            )->addWeeks($paket->tenor_minggu);

            /*
            |--------------------------------------------------------------------------
            | 1. SIMPAN PINJAMAN
            |--------------------------------------------------------------------------
            */

            $pinjaman = Pinjaman::create([
                'nasabah_id' => $nasabah->id,
                'dc_id' => $nasabah->dc_id,
                'paket_pinjaman_id' => $paket->id,
                'jumlah_pinjam' => $paket->nominal_pinjaman,
                'nominal_adm' => $paket->nominal_adm,
                'nominal_dicairkan' => $paket->nominal_dicairkan,
                'total_pinjaman' => $paket->total_tagihan,
                'sisa_tagihan' => $paket->total_tagihan,
                'tenor' => $paket->tenor_minggu,
                'angsuran_per_minggu' => $paket->angsuran_mingguan,
                'tanggal_pengajuan' => $request->tanggal_pengajuan,
                'tanggal_cair' => $request->tanggal_cair,
                'jatuh_tempo' => $jatuhTempo,
                'minggu_berjalan' => 1,
                'status' => 'aktif',
            ]);

            /*
            |--------------------------------------------------------------------------
            | 2. GENERATE ANGSURAN OTOMATIS
            |--------------------------------------------------------------------------
            */

            for ($i = 1; $i <= $paket->tenor_minggu; $i++) {
                Angsuran::create([
                    'pinjaman_id' => $pinjaman->id,
                    'minggu_ke' => $i,
                    'tanggal_jatuh_tempo' => Carbon::parse(
                        $request->tanggal_cair
                    )->addWeeks($i),
                    'jumlah_tagihan' => $paket->angsuran_mingguan,
                    'jumlah_bayar' => 0,
                    'sisa_tagihan' => $paket->angsuran_mingguan,
                    'status' => 'belum_bayar',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | 3. CATAT KAS KELUAR (PENGURANGAN MODAL)
            |--------------------------------------------------------------------------
            */

            KasKeluar::create([
                'modal_id' => $modal->id,
                'dc_id' => $nasabah->dc_id,
                'tanggal' => $request->tanggal_cair,
                'jenis' => 'Pencairan Pinjaman',
                'nominal' => $paket->nominal_dicairkan,
                'referensi' => 'Pinjaman: ' . $pinjaman->id,
                'referensi_id' => $pinjaman->id,
                'referensi_type' => Pinjaman::class,
                'keterangan' => 'Pencairan pinjaman untuk ' . $nasabah->nama . ' (' . $nasabah->nik . ')',
            ]);

            /*
            |--------------------------------------------------------------------------
            | 4. CATAT KAS MASUK BIAYA ADMINISTRASI
            |--------------------------------------------------------------------------
            */

            if ($paket->nominal_adm > 0) {
                KasMasuk::create([
                    'modal_id' => $modal->id,
                    'dc_id' => $nasabah->dc_id,
                    'tanggal' => $request->tanggal_cair,
                    'jenis' => 'Biaya Administrasi',
                    'nominal' => $paket->nominal_adm,
                    'referensi' => 'Pinjaman: ' . $pinjaman->id,
                    'referensi_id' => $pinjaman->id,
                    'referensi_type' => Pinjaman::class,
                    'keterangan' => 'Biaya administrasi pinjaman - ' . $nasabah->nama . ' (' . $nasabah->nik . ')',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | 5. UPDATE SALDO MODAL
            |--------------------------------------------------------------------------
            */

            $modal->updateSaldo($paket->nominal_dicairkan, 'keluar');

        }); // Akhir DB::transaction

        // Sinkronisasi otomatis DKH aktif jika jadwal angsuran pinjaman relevan
        try {
            $dkhService = app(\App\Services\DkhService::class);
            $aktif = $dkhService->getMingguDanTahunAktif();
            $dkhService->generateUntukMinggu($aktif['minggu'], $aktif['tahun'], true);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Auto-sync DKH on loan creation notice: ' . $e->getMessage());
        }

        return redirect()
            ->route('pinjaman.index')
            ->with('success', 'Pinjaman berhasil dibuat! Saldo modal otomatis berkurang.');
    }

    /**
     * Detail Pinjaman
     */
    public function show($id)
    {
        $pinjaman = Pinjaman::with([
            'nasabah',
            'paketPinjaman',
            'angsuran',
            'pembayaran',
            'kasKeluar',
        ])->findOrFail($id);

        return view(
            'pinjaman.show',
            compact('pinjaman')
        );
    }

    /**
     * Form Edit
     */
    public function edit(int $id)
    {
        $pinjaman = Pinjaman::findOrFail($id);

        $nasabah = Nasabah::query()
            ->where('status', 'aktif')
            ->get();

        $paketPinjaman = PaketPinjaman::query()
            ->where('status', 'aktif')
            ->get();

        $modal = Modal::query()->where('status', 'aktif')->first();
        $saldoModal = $modal ? $modal->saldo : 0;

        return view(
            'pinjaman.edit',
            compact(
                'pinjaman',
                'nasabah',
                'paketPinjaman',
                'saldoModal'
            )
        );
    }

    /**
     * Update Pinjaman
     */
    public function update(Request $request, int $id)
{
    $pinjaman = Pinjaman::findOrFail($id);

    $request->validate([
        'nasabah_id' => 'required|exists:nasabah,id',
        'paket_pinjaman_id' => 'required|exists:paket_pinjaman,id',
        'tanggal_pengajuan' => 'required|date',
        'tanggal_cair' => 'required|date',
        'status' => 'required|in:aktif,lunas,ditolak,menunggu',
        'jumlah_pinjam' => 'nullable|numeric|min:0',      
        'nominal_adm' => 'nullable|numeric|min:0',       
        'tenor' => 'nullable|integer|min:1',             
        'angsuran_per_minggu' => 'nullable|numeric|min:0', 
    ]);

    $oldStatus = $pinjaman->status;
    $newStatus = $request->status;

    // 🔥 UPDATE DATA PINJAMAN
    $pinjaman->update([
        'nasabah_id' => $request->nasabah_id,
        'paket_pinjaman_id' => $request->paket_pinjaman_id,
        'tanggal_pengajuan' => $request->tanggal_pengajuan,
        'tanggal_cair' => $request->tanggal_cair,
        'status' => $request->status,
    ]);

    // 🔥 Jika ada perubahan nominal (HANYA jika status masih menunggu)
    if ($pinjaman->status == 'menunggu') {
        $pinjaman->update([
            'jumlah_pinjam' => $request->jumlah_pinjam ?? $pinjaman->jumlah_pinjam,
            'nominal_adm' => $request->nominal_adm ?? $pinjaman->nominal_adm,
            'tenor' => $request->tenor ?? $pinjaman->tenor,
            'angsuran_per_minggu' => $request->angsuran_per_minggu ?? $pinjaman->angsuran_per_minggu,
            'nominal_dicairkan' => ($request->jumlah_pinjam ?? $pinjaman->jumlah_pinjam) - ($request->nominal_adm ?? $pinjaman->nominal_adm),
            'total_pinjaman' => ($request->angsuran_per_minggu ?? $pinjaman->angsuran_per_minggu) * ($request->tenor ?? $pinjaman->tenor),
            'sisa_tagihan' => ($request->angsuran_per_minggu ?? $pinjaman->angsuran_per_minggu) * ($request->tenor ?? $pinjaman->tenor),
        ]);
    }

    // Jika status diubah menjadi 'ditolak' dan status sebelumnya 'aktif'
    if ($oldStatus == 'aktif' && $newStatus == 'ditolak') {
        DB::transaction(function () use ($pinjaman) {
            $modal = Modal::query()->where('status', 'aktif')->first();
            
            if ($modal && $pinjaman->kasKeluar) {
                $modal->updateSaldo($pinjaman->nominal_dicairkan, 'masuk');
                $pinjaman->kasKeluar->delete();
            }

            // Hapus kas masuk biaya administrasi jika ada
            KasMasuk::where('referensi_id', $pinjaman->id)
                ->where('referensi_type', Pinjaman::class)
                ->delete();
            
            $pinjaman->update(['status' => 'ditolak']);
        });
        
        return redirect()
            ->route('pinjaman.index')
            ->with('success', 'Pinjaman dibatalkan! Saldo modal otomatis dikembalikan.');
    }

    return redirect()
        ->route('pinjaman.index')
        ->with('success', 'Data pinjaman berhasil diperbarui.');
}

/**
 * Hapus Pinjaman (LENGKAP DENGAN KEMBALIKAN SALDO)
 */
public function destroy($id)
{
    $pinjaman = Pinjaman::with(['kasKeluar', 'angsuran'])->findOrFail($id);

    DB::beginTransaction();

    try {
        // ============================================
        // 1. KEMBALIKAN SALDO MODAL (dari kas keluar)
        // ============================================
        $modal = Modal::where('status', 'aktif')->first();

        if ($modal) {
            // Cari kas keluar untuk pinjaman ini
            $kasKeluar = KasKeluar::where('referensi_id', $pinjaman->id)
                ->where('referensi_type', Pinjaman::class)
                ->first();

            if ($kasKeluar) {
                // Kembalikan saldo
                $modal->saldo += $kasKeluar->nominal;
                $modal->save();

                // Hapus kas keluar
                $kasKeluar->delete();
            }
        }

        // ============================================
        // 2. HAPUS KAS MASUK (pembayaran yang sudah dilakukan)
        // ============================================
        // Hapus kas masuk yang terkait dengan pinjaman ini
        KasMasuk::where('referensi_id', $pinjaman->id)
            ->where('referensi_type', Pinjaman::class)
            ->delete();

        // ============================================
        // 3. HAPUS DKH DETAIL & PEMBAYARAN TERKAIT
        // ============================================
        $dkhDetails = DkhDetail::where('pinjaman_id', $pinjaman->id)->get();
        foreach ($dkhDetails as $dkhDetail) {
            // Hapus pembayaran yang terkait
            Pembayaran::where('dkh_detail_id', $dkhDetail->id)->delete();
            $dkhDetail->delete();
        }

        // ============================================
        // 4. HAPUS PEMBAYARAN (LANGSUNG)
        // ============================================
        Pembayaran::where('pinjaman_id', $pinjaman->id)->delete();

        // ============================================
        // 5. HAPUS ANGSURAN
        // ============================================
        $pinjaman->angsuran()->delete();

        // ============================================
        // 6. HAPUS PINJAMAN
        // ============================================
        $pinjaman->delete();

        DB::commit();

        return redirect()
            ->route('pinjaman.index')
            ->with('success', 'Data pinjaman berhasil dihapus! Saldo modal dikembalikan.');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()
            ->back()
            ->with('error', 'Gagal menghapus: ' . $e->getMessage());
    }
}
    /**
     * Laporan Pinjaman
     */
    public function laporan(Request $request)
    {
        $query = Pinjaman::with(['nasabah', 'paketPinjaman']);

        // Filter tanggal
        if ($request->filled('dari_tanggal')) {
            $query->whereDate('created_at', '>=', $request->dari_tanggal);
        }

        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('created_at', '<=', $request->sampai_tanggal);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pinjaman = $query->latest()->get();

        // Total
        $totalPinjaman = $pinjaman->sum('jumlah_pinjam');
        $totalDicairkan = $pinjaman->sum('nominal_dicairkan');
        $totalTagihan = $pinjaman->sum('total_pinjaman');
        $totalSisaTagihan = $pinjaman->sum('sisa_tagihan');

        // Ambil saldo modal
        $modal = Modal::query()->where('status', 'aktif')->first();
        $saldoModal = $modal ? $modal->saldo : 0;

        return view(
            'pinjaman.laporan',
            compact(
                'pinjaman',
                'totalPinjaman',
                'totalDicairkan',
                'totalTagihan',
                'totalSisaTagihan',
                'saldoModal'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FITUR CETAK BERKAS & SURAT KONTRAK
    |--------------------------------------------------------------------------
    */

    /**
     * Generate Berkas Pencairan Pinjaman (PDF)
     */
    public function generateBerkasPencairan($id)
    {
        $pinjaman = Pinjaman::with([
            'nasabah',
            'paketPinjaman',
        ])->findOrFail($id);

        $data = [
            'pinjaman' => $pinjaman,
            'nasabah' => $pinjaman->nasabah,
            'paket' => $pinjaman->paketPinjaman,
            'tanggal_cetak' => now()->format('d F Y'),
            'nomor_surat' => $this->generateNomorSuratKontrak($pinjaman),
            'terbilang' => $this->terbilang($pinjaman->nominal_dicairkan),
        ];

        $pdf = Pdf::loadView('pdf.berkas-pencairan', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('Berkas_Pencairan_' . $pinjaman->nasabah->nama . '.pdf');
    }

    /**
     * Generate Surat Kontrak / Perjanjian (PDF)
     */
    public function generateSuratKontrak($id)
    {
        $pinjaman = Pinjaman::with([
            'nasabah',
            'paketPinjaman',
        ])->findOrFail($id);

        $data = [
            'pinjaman' => $pinjaman,
            'nasabah' => $pinjaman->nasabah,
            'paket' => $pinjaman->paketPinjaman,
            'nomor_surat' => $this->generateNomorSuratKontrak($pinjaman),
            'tanggal_surat' => now()->format('d F Y'),
            'terbilang' => $this->terbilang($pinjaman->nominal_dicairkan),
            'hari_jatuh_tempo' => $this->getHariJatuhTempo($pinjaman->tanggal_cair),
            'agunan1' => $pinjaman->nasabah->agunan1 ?? '____________________',
            'agunan2' => $pinjaman->nasabah->agunan2 ?? '____________________',
            'agunan3' => $pinjaman->nasabah->agunan3 ?? '____________________',
            'denda' => $pinjaman->paketPinjaman->denda ?? 15000,
            'kekurangan_angsuran' => 0,
        ];

        $pdf = Pdf::loadView('pdf.surat-kontrak', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('Surat_Kontrak_' . $pinjaman->nasabah->nama . '.pdf');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER / BANTUAN
    |--------------------------------------------------------------------------
    */

    /**
     * Generate Nomor Surat Kontrak
     */
    private function generateNomorSuratKontrak($pinjaman)
    {
        $tahun = now()->format('Y');
        $nomorUrut = str_pad($pinjaman->id, 4, '0', STR_PAD_LEFT);
        return "AHU-{$nomorUrut}-AH.01.14 TAHUN {$tahun}";
    }

    /**
     * Get Hari Jatuh Tempo
     */
    private function getHariJatuhTempo($tanggalCair)
    {
        $hari = Carbon::parse($tanggalCair)->dayOfWeek;
        $hariList = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        return $hariList[$hari] ?? '________';
    }

  /**
 * Konversi Angka ke Terbilang (Rupiah)
 */
private function terbilang($angka)
{
    // 🔥 PASTIKAN ANGKA DIKONVERSI KE INTEGER
    $angka = (int) $angka;
    
    if ($angka < 0) {
        return 'minus ' . $this->terbilang(abs($angka));
    }
    
    $huruf = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan'];
    
    // 0 - 9
    if ($angka < 10) return $huruf[$angka];
    
    // 10 - 19
    if ($angka < 20) {
        if ($angka == 10) return 'sepuluh';
        if ($angka == 11) return 'sebelas';
        return $huruf[$angka - 10] . ' belas';
    }
    
    // 20 - 99
    if ($angka < 100) {
        $puluh = (int) floor($angka / 10);
        $sisa = (int) $angka % 10;
        if ($sisa == 0) return $huruf[$puluh] . ' puluh';
        return $huruf[$puluh] . ' puluh ' . $huruf[$sisa];
    }
    
    // 100 - 999
    if ($angka < 1000) {
        $ratus = (int) floor($angka / 100);
        $sisa = (int) $angka % 100;
        $ratusText = ($ratus == 1) ? 'seratus' : $huruf[$ratus] . ' ratus';
        if ($sisa == 0) return $ratusText;
        return $ratusText . ' ' . $this->terbilang($sisa);
    }
    
    // 1000 - 999999
    if ($angka < 1000000) {
        $ribu = (int) floor($angka / 1000);
        $sisa = (int) $angka % 1000;
        $ribuText = ($ribu == 1) ? 'seribu' : $this->terbilang($ribu) . ' ribu';
        if ($sisa == 0) return $ribuText;
        return $ribuText . ' ' . $this->terbilang($sisa);
    }
    
    // 1000000 - 999999999
    if ($angka < 1000000000) {
        $juta = (int) floor($angka / 1000000);
        $sisa = (int) $angka % 1000000;
        $jutaText = $this->terbilang($juta) . ' juta';
        if ($sisa == 0) return $jutaText;
        return $jutaText . ' ' . $this->terbilang($sisa);
    }
    
    // 1000000000 - 999999999999
    if ($angka < 1000000000000) {
        $milyar = (int) floor($angka / 1000000000);
        $sisa = (int) $angka % 1000000000;
        $milyarText = $this->terbilang($milyar) . ' milyar';
        if ($sisa == 0) return $milyarText;
        return $milyarText . ' ' . $this->terbilang($sisa);
    }
    
    return 'angka terlalu besar';
}

    /**
     * Generate Berkas Pencairan Otomatis (Simpan ke Server) - Opsional
     */
    public function generateBerkasOtomatis($id)
    {
        $pinjaman = Pinjaman::with([
            'nasabah',
            'paketPinjaman',
        ])->findOrFail($id);

        $data = [
            'pinjaman' => $pinjaman,
            'nasabah' => $pinjaman->nasabah,
            'paket' => $pinjaman->paketPinjaman,
            'tanggal_cetak' => now()->format('d F Y'),
            'nomor_surat' => $this->generateNomorSuratKontrak($pinjaman),
            'terbilang' => $this->terbilang($pinjaman->nominal_dicairkan),
        ];

        $pdf = Pdf::loadView('pdf.berkas-pencairan', $data);
        
        // Simpan ke storage
        $path = storage_path('app/public/berkas-pencairan/');
        
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        
        $filename = 'Berkas_Pencairan_' . $pinjaman->nasabah->nama . '_' . date('Ymd_His') . '.pdf';
        $pdf->save($path . $filename);
        
        // Simpan path ke database (jika ada kolom berkas_pencairan)
        $pinjaman->update([
            'berkas_pencairan' => 'berkas-pencairan/' . $filename
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Berkas berhasil disimpan!',
            'file' => $filename
        ]);
    }
}