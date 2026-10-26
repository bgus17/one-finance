<?php

namespace App\Http\Controllers;

use App\Imports\DcImport;
use App\Models\Dc;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DcImportController extends Controller
{
    public function index()
    {
        $dcList = Dc::where('status', 'aktif')->orderBy('nama_dc')->get();
        return view('import.dc', compact('dcList'));
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv|max:20480',
            'dc_id' => 'required|exists:dc,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $import = new DcImport($request->dc_id);
            $collection = Excel::toCollection($import, $request->file('file'));
            
            // Pastikan collection memiliki data
            if ($collection->isEmpty() || $collection->first()->isEmpty()) {
                return redirect()->back()->with('error', '❌ File kosong atau tidak valid!');
            }

            $result = $import->collection($collection->first());

            if ($result['success']) {
                return redirect()->back()->with('success', $result['message']);
            } else {
                $errorMsg = '⚠️ Terjadi ' . count($result['errors']) . ' error:';
                foreach ($result['errors'] as $error) {
                    $errorMsg .= '<br>- ' . $error;
                }

                if (!empty($result['warnings'])) {
                    $errorMsg .= '<br><br>📌 Warning:';
                    foreach ($result['warnings'] as $warning) {
                        $errorMsg .= '<br>- ' . $warning;
                    }
                }

                return redirect()->back()->with('error', $errorMsg);
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', '❌ Gagal import: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $headers = ['No.', 'NID', 'NAMA', 'ALAMAT', 'KODE DESA', 'PINJAMAN', 'JANGKA WAKTU', 'NIK', 'TTL', 'PEKERJAAN', 'No. TELP', 'TGL PENCAIRAN'];
        $rows = [
            ['1', '1', 'Contoh Nasabah', 'Alamat Contoh', '2', '1000000', '12', '1608081708950001', 'OKU TIMUR,17-08-1995', 'PETANI', '081234567890', '2026-06-01'],
        ];

        $filename = 'template_import_dc.csv';
        $handle = fopen('php://output', 'w');
        fputcsv($handle, $headers);
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);

        return response()->stream(
            function() {},
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }
}