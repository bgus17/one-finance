<?php

namespace App\Http\Controllers;

use App\Models\KasKeluar;
use App\Models\Modal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasKeluarController extends Controller
{
    /**
     * Daftar Kas Keluar
     */
    public function index()
    {
        $kasKeluar = KasKeluar::with('modal')
            ->orderByDesc('tanggal')
            ->get();

        $total = $kasKeluar->sum('nominal');

        return view('kas-keluar.index', compact('kasKeluar', 'total'));
    }

    /**
     * Form Tambah Kas Keluar
     */
    public function create()
    {
        return view('kas-keluar.create');
    }

    /**
     * Simpan Kas Keluar
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jenis' => 'required|in:Biaya Operasional,Biaya Admin,Gaji,Lain-lain',
            'nominal' => 'required|numeric|min:1',
            'keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $modal = Modal::where('status', 'aktif')->first();

            if (!$modal) {
                throw new \Exception('Data modal tidak ditemukan!');
            }

            if ($modal->saldo < $request->nominal) {
                throw new \Exception('Saldo modal tidak mencukupi! Saldo: Rp ' . number_format($modal->saldo, 0, ',', '.'));
            }

            // Catat kas keluar
            KasKeluar::create([
                'modal_id' => $modal->id,
                'tanggal' => $request->tanggal,
                'jenis' => $request->jenis,
                'nominal' => $request->nominal,
                'keterangan' => $request->keterangan,
                'referensi_id' => null,
                'referensi_type' => null,
            ]);

            // Update saldo modal
            $modal->saldo -= $request->nominal;
            $modal->save();

            DB::commit();

            return redirect()
                ->route('kas-keluar.index')
                ->with('success', '✅ Biaya berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', '❌ Gagal: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Hapus Kas Keluar
     */
    public function destroy($id)
    {
        $kasKeluar = KasKeluar::findOrFail($id);

        DB::transaction(function () use ($kasKeluar) {
            // Kembalikan saldo modal
            $modal = Modal::where('status', 'aktif')->first();
            if ($modal) {
                $modal->saldo += $kasKeluar->nominal;
                $modal->save();
            }

            $kasKeluar->delete();
        });

        return redirect()
            ->route('kas-keluar.index')
            ->with('success', '✅ Data berhasil dihapus!');
    }
}