<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Desa;
use App\Models\PaketPinjaman;
use App\Models\Dc;
use App\Models\Modal;

echo "Desa count: " . Desa::count() . "\n";
echo "PaketPinjaman count: " . PaketPinjaman::count() . "\n";
echo "Dc count: " . Dc::count() . "\n";
echo "Modal count: " . Modal::count() . "\n";
foreach (Dc::all() as $d) {
    echo "  DC: ID {$d->id} | {$d->kode_dc} | {$d->nama_dc}\n";
}
