<?php

namespace App\Http\Controllers;

use App\Models\Wilayah;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    public function index()
    {
        $wilayah = Wilayah::orderBy('id', 'desc')->get();

        return view('wilayah.index', compact('wilayah'));
    }

    public function create()
    {
        return view('wilayah.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_wilayah' => 'required'
        ]);

        Wilayah::create([
            'nama_wilayah' => $request->nama_wilayah,
            'keterangan'   => $request->keterangan
        ]);

        return redirect('/wilayah')
            ->with('success', 'Data wilayah berhasil ditambahkan');
    }

    public function edit(int $id)
    {
        $wilayah = Wilayah::findOrFail($id);

        return view('wilayah.edit', compact('wilayah'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'nama_wilayah' => 'required'
        ]);

        $wilayah = Wilayah::findOrFail($id);

        $wilayah->update([
            'nama_wilayah' => $request->nama_wilayah,
            'keterangan'   => $request->keterangan
        ]);

        return redirect('/wilayah')
            ->with('success', 'Data wilayah berhasil diupdate');
    }

    public function destroy(int $id)
    {
        $wilayah = Wilayah::findOrFail($id);

        $wilayah->delete();

        return redirect('/wilayah')
            ->with('success', 'Data wilayah berhasil dihapus');
    }
}