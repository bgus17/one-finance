<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Modal;

$modals = Modal::all();
foreach ($modals as $m) {
    echo "Modal ID: {$m->id} | {$m->nama_modal} | Saldo: {$m->saldo} | Status: {$m->status}\n";
}
