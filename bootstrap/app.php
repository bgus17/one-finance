<?php

use App\Console\Commands\BackupDatabase;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

// Otomatis buat file .env jika belum ada dan generate APP_KEY
$baseDir = dirname(__DIR__);
$envPath = $baseDir . DIRECTORY_SEPARATOR . '.env';
$envExamplePath = $baseDir . DIRECTORY_SEPARATOR . '.env.example';

if (!file_exists($envPath) && file_exists($envExamplePath)) {
    copy($envExamplePath, $envPath);
}

if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    if (strpos($envContent, 'APP_KEY=') !== false && preg_match('/^APP_KEY=\s*$/m', $envContent)) {
        $appKey = 'base64:' . base64_encode(random_bytes(32));
        $envContent = preg_replace('/^APP_KEY=.*$/m', 'APP_KEY=' . $appKey, $envContent);
        file_put_contents($envPath, $envContent);
    }
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        __DIR__.'/../app/Console/Commands',
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withSchedule(function (Schedule $schedule) {
        // 🔥 BACKUP OTOMATIS SETIAP HARI JAM 01:00
        $schedule->command('backup:database')
            ->dailyAt('01:00')
            ->appendOutputTo(storage_path('logs/backup.log'));

        // 🔥 BACKUP JUGA SETIAP MINGGU (Minggu jam 02:00)
        $schedule->command('backup:database')
            ->weeklyOn(7, '02:00') // 7 = Minggu
            ->appendOutputTo(storage_path('logs/backup.log'));
    })
    ->create();