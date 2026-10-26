<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use App\Models\Wilayah;
use App\Models\Dc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NasabahController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DAFTAR NASABAH
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $nasabah = Nasabah::with(['wilayah', 'dc'])
            ->orderBy('nama', 'asc')
            ->get();

        return view('nasabah.index', compact('nasabah'));
    }

    /*
    |--------------------------------------------------------------------------
    | FORM TAMBAH NASABAH
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $wilayah = Wilayah::query()
            ->orderBy('nama_wilayah', 'asc')
            ->get();

        $dc = Dc::query()
            ->where('status', 'aktif')
            ->orderBy('nama_dc', 'asc')
            ->get();

        return view('nasabah.create', compact('wilayah', 'dc'));
    }

    /*
    |--------------------------------------------------------------------------
    | SIMPAN NASABAH
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        // Debug: lihat data yang masuk
        // dd($request->all());

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'nik' => 'required|string|max:30|unique:nasabah,nik',
            'tanggal_lahir' => 'nullable|date|before:today',
            'alamat' => 'required|string',
            'wilayah_id' => 'required|exists:wilayah,id',
            'dc_id' => 'required|exists:dc,id',
            'no_hp' => 'required|string|max:20',
            'pekerjaan' => 'nullable|string|max:100',
        ], [
            'nama.required' => 'Nama nasabah wajib diisi.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.unique' => 'NIK sudah terdaftar, silakan gunakan NIK lain.',
            'alamat.required' => 'Alamat wajib diisi.',
            'wilayah_id.required' => 'Wilayah wajib dipilih.',
            'dc_id.required' => 'DC wajib dipilih.',
            'no_hp.required' => 'No HP wajib diisi.',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini.',
        ]);

        if ($validator->fails()) {
            // Kembalikan dengan error
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', '⚠️ Ada data yang belum lengkap. Silakan periksa kembali.');
        }

        try {
            // Generate kode nasabah
            $tahun = date('Y');
            $bulan = date('m');
            $last = Nasabah::whereYear('created_at', $tahun)
                ->whereMonth('created_at', $bulan)
                ->orderBy('id', 'desc')
                ->first();
            
            if ($last && $last->kode_nasabah) {
                $angka = (int) substr($last->kode_nasabah, -4);
                $urutan = str_pad($angka + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $urutan = '0001';
            }
            $kodeNasabah = 'NSB' . $tahun . $bulan . $urutan;

            // Simpan data
            $nasabah = Nasabah::create([
                'kode_nasabah' => $kodeNasabah,
                'nama' => $request->nama,
                'nik' => $request->nik,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat' => $request->alamat,
                'wilayah_id' => $request->wilayah_id,
                'dc_id' => $request->dc_id,
                'no_hp' => $request->no_hp,
                'pekerjaan' => $request->pekerjaan,
                'status' => 'aktif',
            ]);

            // Debug: cek apakah data tersimpan
            // dd($nasabah);

            return redirect()
                ->route('nasabah.index')
                ->with('success', '✅ Data nasabah berhasil ditambahkan. Kode: ' . $kodeNasabah);

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', '❌ Gagal menambahkan nasabah: ' . $e->getMessage())
                ->withInput();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DETAIL NASABAH
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        $nasabah = Nasabah::with(['wilayah', 'dc', 'pinjaman'])
            ->findOrFail($id);

        return view('nasabah.show', compact('nasabah'));
    }

    /*
    |--------------------------------------------------------------------------
    | FORM EDIT
    |--------------------------------------------------------------------------
    */
    public function edit($id)
    {
        $nasabah = Nasabah::findOrFail($id);

        $wilayah = Wilayah::query()
            ->orderBy('nama_wilayah', 'asc')
            ->get();

        $dc = Dc::query()
            ->where('status', 'aktif')
            ->orderBy('nama_dc', 'asc')
            ->get();

        return view('nasabah.edit', compact('nasabah', 'wilayah', 'dc'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE NASABAH
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, $id)
    {
        $nasabah = Nasabah::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'nik' => 'required|string|max:30|unique:nasabah,nik,' . $id,
            'tanggal_lahir' => 'nullable|date|before:today',
            'alamat' => 'required|string',
            'wilayah_id' => 'required|exists:wilayah,id',
            'dc_id' => 'required|exists:dc,id',
            'no_hp' => 'required|string|max:20',
            'pekerjaan' => 'nullable|string|max:100',
            'status' => 'required|in:aktif,nonaktif',
        ], [
            'nama.required' => 'Nama nasabah wajib diisi.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.unique' => 'NIK sudah terdaftar, silakan gunakan NIK lain.',
            'alamat.required' => 'Alamat wajib diisi.',
            'wilayah_id.required' => 'Wilayah wajib dipilih.',
            'dc_id.required' => 'DC wajib dipilih.',
            'no_hp.required' => 'No HP wajib diisi.',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini.',
            'status.required' => 'Status wajib dipilih.',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', '⚠️ Ada data yang belum lengkap. Silakan periksa kembali.');
        }

        try {
            $nasabah->update([
                'nama' => $request->nama,
                'nik' => $request->nik,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat' => $request->alamat,
                'wilayah_id' => $request->wilayah_id,
                'dc_id' => $request->dc_id,
                'no_hp' => $request->no_hp,
                'pekerjaan' => $request->pekerjaan,
                'status' => $request->status,
            ]);

            return redirect()
                ->route('nasabah.index')
                ->with('success', '✅ Data nasabah berhasil diperbarui.');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', '❌ Gagal memperbarui nasabah: ' . $e->getMessage())
                ->withInput();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | HAPUS NASABAH
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        try {
            $nasabah = Nasabah::findOrFail($id);

            // Cek apakah nasabah memiliki pinjaman aktif
            if ($nasabah->pinjaman()->where('status', 'aktif')->count() > 0) {
                return redirect()
                    ->back()
                    ->with('error', '❌ Nasabah tidak dapat dihapus karena memiliki pinjaman aktif.');
            }

            $nasabah->delete();

            return redirect()
                ->route('nasabah.index')
                ->with('success', '✅ Data nasabah berhasil dihapus.');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', '❌ Gagal menghapus nasabah: ' . $e->getMessage());
        }
    }
}