<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jadwal Generate DKH Otomatis setiap hari pukul 00:05
Schedule::command('dkh:generate-otomatis')->dailyAt('00:05');
