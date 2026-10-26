<?php

namespace App\Http\Controllers;

use App\Models\Dc;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Http\Request;

class DcController extends Controller
{
    /**
     * Daftar DC
     */
    public function index()
    {
        $dc = Dc::with([
            'wilayah',
            'user'
        ])->latest()->get();

        return view('dc.index', compact('dc'));
    }

    /**
     * Form tambah DC
     */
  public function create()
{
    $wilayah = Wilayah::all();

    $user = User::all();

    return view('dc.create', [
        'wilayah' => $wilayah,
        'user' => $user,
    ]);
}
    /**
     * Simpan DC
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'wilayah_id' => 'required',
            'nama_dc' => 'required',
            'alamat' => 'required|min:4|max:255'
        ]);

        Dc::create([

            'user_id' => $request->user_id,

            'wilayah_id' => $request->wilayah_id,

            'kode_dc' => 'DC' . time(),

            'nama_dc' => $request->nama_dc,

            'no_hp' => $request->no_hp,

            'alamat' => $request->alamat,

            'tanggal_masuk' => $request->tanggal_masuk,

            'status' => $request->status ?? 'aktif',
        ]);

        return redirect()
            ->route('dc.index')
            ->with(
                'success',
                'Data DC berhasil ditambahkan'
            );
    }

    /**
     * Form Edit DC
     */
  public function edit($id)
{
    $dc = Dc::findOrFail($id);

    $wilayah = Wilayah::all();

    $user = User::all();

    return view('dc.edit', [
        'dc' => $dc,
        'wilayah' => $wilayah,
        'user' => $user,
    ]);
}
    /**
     * Update DC
     */
    public function update(Request $request, $id)
    {
        $dc = Dc::findOrFail($id);

        $request->validate([
            'user_id' => 'required',
            'wilayah_id' => 'required',
            'nama_dc' => 'required',
        ]);

        $dc->update([

            'user_id' => $request->user_id,

            'wilayah_id' => $request->wilayah_id,

            'nama_dc' => $request->nama_dc,

            'no_hp' => $request->no_hp,

            'alamat' => $request->alamat,

            'tanggal_masuk' => $request->tanggal_masuk,

            'status' => $request->status,
        ]);

        return redirect()
            ->route('dc.index')
            ->with(
                'success',
                'Data DC berhasil diperbarui'
            );
    }

    /**
     * Hapus DC
     */
    public function destroy($id)
    {
        $dc = Dc::findOrFail($id);

        $dc->delete();

        return redirect()
            ->route('dc.index')
            ->with(
                'success',
                'Data DC berhasil dihapus'
            );
    }
}