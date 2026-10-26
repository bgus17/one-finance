<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Dc;
use App\Models\PaketPinjaman;
use App\Models\Desa;
use PhpOffice\PhpSpreadsheet\IOFactory;

echo "=== DC DETAILS IN DB ===\n";
foreach (Dc::all() as $dc) {
    echo "ID: {$dc->id}, user_id: {$dc->user_id}, wilayah_id: {$dc->wilayah_id}, kode: {$dc->kode_dc}, nama: {$dc->nama_dc}, no_hp: {$dc->no_hp}\n";
}

$pakets = PaketPinjaman::all();
$validPakets = [];
foreach ($pakets as $p) {
    $validPakets[$p->nominal_pinjaman . '_' . $p->tenor_minggu] = $p;
}

$desas = Desa::all()->keyBy('nama_desa');

$dir = 'C:/Users/acer/Downloads/data_impor';
$files = scandir($dir);

echo "\n=== DETAILED SCAN OF ALL ROWS IN 4 FILES ===\n";

foreach ($files as $f) {
    if (!str_ends_with($f, '.xlsx')) continue;
    echo "\n--------------------------------------------------\n";
    echo "ANALYZING: $f\n";
    echo "--------------------------------------------------\n";
    
    $path = $dir . '/' . $f;
    $spreadsheet = IOFactory::load($path);
    $sheet = $spreadsheet->getActiveSheet();
    $highestRow = $sheet->getHighestRow();
    $highestCol = $sheet->getHighestColumn();
    $highestColIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestCol);
    
    // Header
    $headerRow = 1;
    for ($r = 1; $r <= 3; $r++) {
        $val = trim((string)$sheet->getCell("C$r")->getValue());
        if (strtoupper($val) === 'NAMA') {
            $headerRow = $r;
            break;
        }
    }
    
    $headers = [];
    for ($c = 1; $c <= $highestColIndex; $c++) {
        $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
        $headers[$c] = strtoupper(trim((string)$sheet->getCell($colLetter . $headerRow)->getValue()));
    }
    
    echo "Header at Row $headerRow: " . implode(" | ", array_filter($headers)) . "\n";
    
    for ($r = $headerRow + 1; $r <= $highestRow; $r++) {
        $row = [];
        for ($c = 1; $c <= $highestColIndex; $c++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
            $h = $headers[$c] ?? "COL_$c";
            $row[$h] = [
                'val' => $sheet->getCell($colLetter . $r)->getValue(),
                'formatted' => $sheet->getCell($colLetter . $r)->getFormattedValue(),
                'cell' => $colLetter . $r
            ];
        }
        
        $nama = trim((string)($row['NAMA']['val'] ?? ''));
        if (empty($nama) || stripos($nama, 'total') !== false || stripos($nama, 'jumlah') !== false) {
            continue;
        }
        
        // Pinjaman & Tenor
        $pinjRaw = (string)($row['PINJAMAN']['val'] ?? '');
        $pinjClean = (int)str_replace(['Rp', 'rp', ' ', '.', ',', '"', "'"], '', $pinjRaw);
        $tenor = (int)($row['JANGKA WAKTU']['val'] ?? 0);
        
        $key = $pinjClean . '_' . $tenor;
        if (!isset($validPakets[$key])) {
            echo "[MISMATCH PAKET] Row $r ({$row['NAMA']['cell']}): Nama: '$nama', Pinjaman: $pinjClean, Tenor: $tenor (Not in paket_pinjaman!)\n";
            // Print other columns to see angsuran
            $angsuranVal = $row['PKOK+MARGIN']['val'] ?? $row['POKOK+MARGIN']['val'] ?? $row['ANGSURAN']['val'] ?? $row['MARGIN+POKOK']['val'] ?? 'N/A';
            echo "   -> Recorded Angsuran in Excel: $angsuranVal\n";
        }
        
        // Desa & Alamat
        $alamat = trim((string)($row['ALAMAT']['val'] ?? ''));
        $kodeDesa = trim((string)($row['KODE DESA']['val'] ?? ''));
        
        // Check if desa exists in DB
        // Standardize some names
        $stdAlamat = $alamat;
        if ($stdAlamat === 'SENUMARGA' || $stdAlamat === 'SINOMARGA') $stdAlamat = 'SINUMARGA';
        if ($stdAlamat === 'SUMBER RAHYAU') $stdAlamat = 'SUMBER RAHAYU';
        if ($stdAlamat === 'NUSAJAYA') $stdAlamat = 'NUSA JAYA';
        
        if (!$desas->has($stdAlamat) && !$desas->has($alamat)) {
            echo "[UNKNOWN DESA] Row $r: Nama: '$nama', Alamat: '$alamat', Kode Desa: '$kodeDesa' (Desa not in desas table!)\n";
        }
        
        // Check NIK
        $nikRaw = trim((string)($row['NIK']['val'] ?? ''));
        $nikClean = preg_replace('/[^0-9]/', '', str_replace('*', '', $nikRaw));
        if (strlen($nikClean) !== 16) {
            echo "[NIK ISSUE] Row $r: Nama: '$nama', NIK Raw: '$nikRaw', Cleaned: '$nikClean' (Length: " . strlen($nikClean) . ")\n";
        }
        
        // Check TTL
        $ttlRaw = trim((string)($row['TTL']['val'] ?? ''));
        if (empty($ttlRaw)) {
            echo "[TTL EMPTY] Row $r: Nama: '$nama'\n";
        }
        
        // Check Sudah Bayar
        $sudahBayarVal = $row['SUDAH BAYA']['val'] ?? $row['SUDAH BAYAR']['val'] ?? null;
        if ($sudahBayarVal === null || $sudahBayarVal === '') {
            echo "[SUDAH BAYAR EMPTY] Row $r: Nama: '$nama'\n";
        }
    }
}
