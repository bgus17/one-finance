<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\KasMasuk;
use App\Models\KasKeluar;
use App\Models\Nasabah;
use App\Models\Pinjaman;
use App\Models\Angsuran;
use App\Models\Pembayaran;

echo "KasMasuk count: " . KasMasuk::count() . "\n";
echo "KasKeluar count: " . KasKeluar::count() . "\n";
echo "Nasabah count: " . Nasabah::count() . "\n";
echo "Pinjaman count: " . Pinjaman::count() . "\n";
echo "Angsuran count: " . Angsuran::count() . "\n";
echo "Pembayaran count: " . Pembayaran::count() . "\n";
