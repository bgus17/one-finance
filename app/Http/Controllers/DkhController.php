<?php

namespace App\Http\Controllers;

use App\Models\Dkh;
use App\Services\DkhService;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DkhController extends Controller
{
    protected DkhService $dkhService;

    public function __construct(DkhService $dkhService)
    {
        $this->dkhService = $dkhService;
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX - DAFTAR DKH
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        if ($request->has('minggu') && $request->has('tahun')) {
            $minggu = (int) $request->minggu;
            $tahun = (int) $request->tahun;
        } else {
            $aktif = $this->dkhService->getMingguDanTahunAktif();
            $minggu = $aktif['minggu'];
            $tahun = $aktif['tahun'];

            // 🔥 OTOMATISASI: Jika DKH untuk minggu aktif belum ada, langsung generate otomatis!
            $adaDkh = Dkh::query()
                ->where('minggu_ke', $minggu)
                ->where('tahun', $tahun)
                ->exists();

            if (!$adaDkh) {
                $this->dkhService->generateUntukMinggu($minggu, $tahun);
            }
        }

        $periode = $this->dkhService->getPeriodeMinggu($tahun, $minggu);

        $dkh = Dkh::with('dc')
            ->where('minggu_ke', $minggu)
            ->where('tahun', $tahun)
            ->orderBy('dc_id')
            ->get();

        $listMinggu = range(1, 52);
        $listTahun = range(date('Y') - 2, date('Y') + 1);

        return view('dkh.index', compact('dkh', 'minggu', 'tahun', 'listMinggu', 'listTahun', 'periode'));
    }

    /*
    |--------------------------------------------------------------------------
    | SINKRONISASI / GENERATE DKH
    |--------------------------------------------------------------------------
    */
    public function generate(Request $request)
    {
        if ($request->has('minggu') && $request->has('tahun')) {
            $minggu = (int) $request->minggu;
            $tahun = (int) $request->tahun;
        } else {
            $aktif = $this->dkhService->getMingguDanTahunAktif();
            $minggu = $aktif['minggu'];
            $tahun = $aktif['tahun'];
        }

        // Jalankan sinkronisasi dengan forceResync = true
        $hasil = $this->dkhService->generateUntukMinggu($minggu, $tahun, true);

        if (!$hasil['status']) {
            return redirect()
                ->route('dkh.index', ['minggu' => $minggu, 'tahun' => $tahun])
                ->with('error', '❌ Gagal memproses DKH: ' . ($hasil['error'] ?? 'Terjadi kesalahan sistem'));
        }

        if ($hasil['created'] > 0) {
            $msg = "✅ Berhasil men-generate {$hasil['created']} DKH baru untuk minggu ke-{$minggu} ({$tahun})";
            $type = 'success';
        } elseif ($hasil['updated'] > 0) {
            $msg = "✅ Berhasil menyinkronkan data DKH untuk minggu ke-{$minggu} ({$tahun})";
            $type = 'success';
        } else {
            $msg = "ℹ️ Seluruh DKH untuk minggu ke-{$minggu} ({$tahun}) sudah mutakhir.";
            $type = 'info';
        }

        return redirect()
            ->route('dkh.index', ['minggu' => $minggu, 'tahun' => $tahun])
            ->with($type, $msg);
    }

    /*
    |--------------------------------------------------------------------------
    | DETAIL DKH
    |--------------------------------------------------------------------------
    */
    public function show(int $id)
    {
        $dkh = Dkh::with([
            'dc',
            'detail.nasabah',
            'detail.pinjaman',
            'detail.angsuran',
        ])->findOrFail($id);

        return view('dkh.show', compact('dkh'));
    }

    /*
    |--------------------------------------------------------------------------
    | HAPUS DKH
    |--------------------------------------------------------------------------
    */
    public function destroy(int $id)
    {
        try {
            $dkh = Dkh::findOrFail($id);
            $minggu = $dkh->minggu_ke;
            $tahun = $dkh->tahun;
            $dkh->delete();

            return redirect()
                ->route('dkh.index', ['minggu' => $minggu, 'tahun' => $tahun])
                ->with('success', '✅ DKH berhasil dihapus!');

        } catch (\Exception $e) {
            return redirect()
                ->route('dkh.index')
                ->with('error', '❌ Gagal menghapus DKH: ' . $e->getMessage());
        }
    }


    /*
    |--------------------------------------------------------------------------
    | CETAK DKH (PDF)
    |--------------------------------------------------------------------------
    */
    public function cetak($id)
    {
        $dkh = Dkh::with([
            'dc',
            'detail.nasabah',
            'detail.pinjaman',
            'detail.angsuran',
        ])->findOrFail($id);

        $data = [
            'dkh' => $dkh,
            'tanggal_cetak' => now()->format('d F Y H:i'),
            'logo' => public_path('assets/img/one-finance.png'),
        ];

        $pdf = Pdf::loadView('pdf.dkh', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('DKH_' . $dkh->nomor_dkh . '.pdf');
    }

    /*
    |--------------------------------------------------------------------------
    | CETAK DKH PER TANGGAL (PDF)
    |--------------------------------------------------------------------------
    */
    public function cetakPerTanggal(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
        ]);

        $tanggal = Carbon::parse($request->tanggal);
        
        $dkh = Dkh::with(['dc', 'detail.nasabah'])
            ->whereDate('tanggal_dkh', $tanggal->format('Y-m-d'))
            ->get();

        if ($dkh->isEmpty()) {
            return back()->with('error', '❌ Tidak ada DKH pada tanggal tersebut.');
        }

        $data = [
            'dkh' => $dkh,
            'tanggal' => $tanggal->format('d F Y'),
            'tanggal_cetak' => now()->format('d F Y H:i'),
            'logo' => public_path('assets/img/one-finance.png'),
        ];

        $pdf = Pdf::loadView('pdf.dkh-semua', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('DKH_' . $tanggal->format('Y-m-d') . '.pdf');
    }
}