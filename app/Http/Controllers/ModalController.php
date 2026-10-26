<?php

namespace App\Http\Controllers;

use App\Models\Modal;
use App\Models\KasMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModalController extends Controller
{
    public function index()
    {
        $modal = Modal::query()
            ->latest()
            ->get();
        $totalModal = Modal::sum('nominal');

        return view('modal.index', compact('modal', 'totalModal'));
    }

    public function create()
    {
        return view('modal.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'nominal' => 'required|numeric|min:1',
            'jenis' => 'required|string',
            'keterangan' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            // Ambil kas modal utama yang aktif
            $modalUtama = Modal::where('nama_modal', 'Kas Koperasi')
                ->orWhere('status', 'aktif')
                ->first();

            // Saldo baru setelah penambahan modal
            $saldoBaru = ($modalUtama ? $modalUtama->saldo : 0) + $request->nominal;

            // Simpan data modal baru
            $modal = Modal::create([
                'tanggal' => $request->tanggal,
                'nominal' => $request->nominal,
                'nama_modal' => $request->jenis ?? 'tambahan_modal',
                'saldo' => $saldoBaru,
                'status' => 'aktif',
                'keterangan' => $request->keterangan
            ]);

            // Tambahkan saldo kas pada modal utama
            if ($modalUtama) {
                $modalUtama->saldo = $saldoBaru;
                $modalUtama->save();
            }

            // Catat kas masuk agar mutasi kas, dashboard kas masuk & selisih kas sinkron
            KasMasuk::create([
                'modal_id' => $modalUtama ? $modalUtama->id : $modal->id,
                'tanggal' => $request->tanggal,
                'jenis' => 'Tambahan Modal',
                'nominal' => $request->nominal,
                'keterangan' => $request->keterangan ?: 'Tambahan modal koperasi',
                'referensi_id' => $modal->id,
                'referensi_type' => Modal::class,
            ]);

            DB::commit();

            return redirect()
                ->route('modal.index')
                ->with('success', 'Modal berhasil ditambahkan dan saldo kas otomatis bertambah');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Gagal menambahkan modal: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(int $id)
    {
        $modal = Modal::findOrFail($id);

        return view('modal.edit', compact('modal'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'nominal' => 'required|numeric|min:1',
            'jenis' => 'required|string',
            'keterangan' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $modal = Modal::findOrFail($id);
            $modalUtama = Modal::where('nama_modal', 'Kas Koperasi')
                ->orWhere('status', 'aktif')
                ->first();

            $selisihNominal = $request->nominal - $modal->nominal;

            $modal->update([
                'tanggal' => $request->tanggal,
                'nominal' => $request->nominal,
                'nama_modal' => $request->jenis,
                'keterangan' => $request->keterangan
            ]);

            // Update saldo kas utama jika ada perubahan nominal
            if ($modalUtama && $selisihNominal != 0) {
                $modalUtama->saldo += $selisihNominal;
                $modalUtama->save();
            }

            // Update kas_masuk terkait jika ada
            $kasMasuk = KasMasuk::where('referensi_type', Modal::class)
                ->where('referensi_id', $modal->id)
                ->first();

            if ($kasMasuk) {
                $kasMasuk->update([
                    'tanggal' => $request->tanggal,
                    'nominal' => $request->nominal,
                    'keterangan' => $request->keterangan ?: 'Tambahan modal koperasi',
                ]);
            }

            DB::commit();

            return redirect()
                ->route('modal.index')
                ->with('success', 'Modal berhasil diperbarui dan saldo kas telah disesuaikan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Gagal memperbarui modal: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();
        try {
            $modal = Modal::findOrFail($id);
            $modalUtama = Modal::where('nama_modal', 'Kas Koperasi')
                ->orWhere('status', 'aktif')
                ->first();

            // Modal awal utama tidak boleh dihapus karena merupakan rekening induk kas
            if ($modalUtama && $modal->id == $modalUtama->id) {
                return redirect()
                    ->route('modal.index')
                    ->with('error', 'Modal awal utama tidak boleh dihapus karena merupakan master kas koperasi!');
            }

            // Kurangi saldo kas utama sebesar nominal yang dihapus
            if ($modalUtama) {
                $modalUtama->saldo -= $modal->nominal;
                $modalUtama->save();
            }

            // Hapus kas masuk terkait
            KasMasuk::where('referensi_type', Modal::class)
                ->where('referensi_id', $modal->id)
                ->delete();

            $modal->delete();

            DB::commit();

            return redirect()
                ->route('modal.index')
                ->with('success', 'Data modal berhasil dihapus dan saldo kas telah disesuaikan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('modal.index')
                ->with('error', 'Gagal menghapus modal: ' . $e->getMessage());
        }
    }
}