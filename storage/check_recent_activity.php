<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pembayaran;
use App\Models\KasMasuk;
use App\Models\KasKeluar;
use App\Models\Pinjaman;

echo "Checking transactions created after 2026-10-23 22:00:00:\n";
echo "Pembayaran after Oct 23: " . Pembayaran::where('created_at', '>', '2026-10-23 22:00:00')->count() . "\n";
echo "KasMasuk after Oct 23: " . KasMasuk::where('created_at', '>', '2026-10-23 22:00:00')->count() . "\n";
echo "KasKeluar after Oct 23: " . KasKeluar::where('created_at', '>', '2026-10-23 22:00:00')->count() . "\n";
echo "Pinjaman after Oct 23: " . Pinjaman::where('created_at', '>', '2026-10-23 22:00:00')->count() . "\n";
