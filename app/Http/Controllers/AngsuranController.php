<?php

namespace App\Http\Controllers;

use App\Models\Angsuran;
use App\Models\KasMasuk;
use App\Models\Modal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AngsuranController extends Controller
{
    /**
     * Form Pembayaran Angsuran
     */
    public function bayarForm($id)
    {
        $angsuran = Angsuran::with('pinjaman.nasabah')->findOrFail($id);
        
        $modal = Modal::query()->where('status', 'aktif')->first();
        $saldoModal = $modal ? $modal->saldo : 0;

        return view('angsuran.bayar', compact('angsuran', 'saldoModal'));
    }

    /**
     * Proses Pembayaran Angsuran
     */
    public function bayar(Request $request, $id)
    {
        $request->validate([
            'jumlah_bayar' => 'required|numeric|min:1',
            'tanggal_bayar' => 'required|date',
        ]);

        $angsuran = Angsuran::with(['pinjaman.nasabah'])->findOrFail($id);

        if ($angsuran->status == 'lunas') {
            return back()->with('error', 'Angsuran ini sudah lunas!');
        }

        if ($request->jumlah_bayar > $angsuran->sisa_tagihan) {
            return back()->with('error', 'Jumlah bayar melebihi sisa tagihan!');
        }

        $modal = Modal::query()->where('status', 'aktif')->first();

        if (!$modal) {
            return back()->with('error', 'Data modal tidak ditemukan!');
        }

        DB::transaction(function () use ($request, $angsuran, $modal) {

            // 1. UPDATE ANGSURAN
            $angsuran->update([
                'jumlah_bayar' => $angsuran->jumlah_bayar + $request->jumlah_bayar,
                'sisa_tagihan' => $angsuran->sisa_tagihan - $request->jumlah_bayar,
                'status' => $angsuran->sisa_tagihan - $request->jumlah_bayar <= 0 ? 'lunas' : 'belum_bayar',
                'tanggal_bayar' => $request->tanggal_bayar,
            ]);

            // 2. CATAT KAS MASUK
            KasMasuk::create([
                'modal_id' => $modal->id,
                'tanggal' => $request->tanggal_bayar,
                'jenis' => 'Pembayaran Angsuran',
                'nominal' => $request->jumlah_bayar,
                'keterangan' => 'Pembayaran angsuran ke-' . $angsuran->minggu_ke . ' - ' . $angsuran->pinjaman->nasabah->nama,
                'referensi_id' => $angsuran->id,
                'referensi_type' => Angsuran::class,
            ]);

            // 3. UPDATE SALDO MODAL
            $modal->updateSaldo($request->jumlah_bayar, 'masuk');

            // 4. UPDATE SISA TAGIHAN PINJAMAN
            $pinjaman = $angsuran->pinjaman;
            $pinjaman->sisa_tagihan -= $request->jumlah_bayar;

            if ($pinjaman->sisa_tagihan <= 0) {
                $pinjaman->status = 'lunas';
                $pinjaman->tanggal_lunas = $request->tanggal_bayar;
            }
            $pinjaman->save();

        });

        return redirect()
            ->route('pinjaman.show', $angsuran->pinjaman_id)
            ->with('success', '✅ Pembayaran angsuran berhasil!');
    }
}