<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Modal;
use App\Models\KasMasuk;
use App\Models\KasKeluar;
use Illuminate\Support\Facades\DB;

$m = Modal::find(1);
echo "Modal 1: {$m->nama_modal} | Saldo: {$m->saldo}\n";

$kmTotal = KasMasuk::where('modal_id', 1)->sum('nominal');
$kkTotal = KasKeluar::where('modal_id', 1)->sum('nominal');
echo "Kas Masuk sum: $kmTotal\n";
echo "Kas Keluar sum: $kkTotal\n";

$modals = DB::table('modal')->get();
foreach ($modals as $mo) {
    echo "ID: {$mo->id} | {$mo->nama_modal} | Saldo: {$mo->saldo}\n";
}
