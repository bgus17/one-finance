<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PDO;
use Exception;

class SetupEnvCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'env:auto {--force : Timpa file .env yang sudah ada}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatis setup file .env, APP_KEY, dan database untuk project OneFinance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Memulai konfigurasi otomatis environment OneFinance...');

        $baseDir = base_path();
        $envPath = $baseDir . DIRECTORY_SEPARATOR . '.env';
        $envExamplePath = $baseDir . DIRECTORY_SEPARATOR . '.env.example';

        // 1. Cek / Salin .env
        $isNewEnv = false;
        if (!file_exists($envPath) || $this->option('force')) {
            if (file_exists($envExamplePath)) {
                copy($envExamplePath, $envPath);
                $this->info('✅ File .env berhasil disalin otomatis dari .env.example.');
                $isNewEnv = true;
            } else {
                $this->error('❌ File .env.example tidak ditemukan!');
                return 1;
            }
        } else {
            $this->comment('ℹ️ File .env sudah ada.');
        }

        // 2. Cek & Generate APP_KEY jika belum ada
        $envContent = file_get_contents($envPath);
        if ($isNewEnv || !preg_match('/^APP_KEY=.+$/m', $envContent) || preg_match('/^APP_KEY=\s*$/m', $envContent)) {
            $this->call('key:generate', ['--force' => true]);
            $this->info('✅ Application Key (APP_KEY) berhasil dibuat otomatis.');
        } else {
            $this->comment('ℹ️ APP_KEY sudah terisi di .env.');
        }

        // 3. Cek Koneksi & Buat Database jika MySQL berjalan
        $this->setupDatabase();

        // 4. Bersihkan cache konfigurasi
        $this->callSilent('config:clear');
        $this->info('✅ Cache konfigurasi dibersihkan.');

        $this->newLine();
        $this->info('🎉 Konfigurasi .env otomatis selesai! Project OneFinance siap digunakan.');
        return 0;
    }

    /**
     * Cek dan buat database otomatis jika MySQL tersedia
     */
    protected function setupDatabase()
    {
        $dbConnection = config('database.default', env('DB_CONNECTION', 'mysql'));

        if ($dbConnection === 'sqlite') {
            $dbPath = database_path('database.sqlite');
            if (!file_exists($dbPath)) {
                touch($dbPath);
                $this->info('✅ File SQLite database dibuat: ' . $dbPath);
            }
            return;
        }

        if ($dbConnection === 'mysql') {
            $host = env('DB_HOST', '127.0.0.1');
            $port = env('DB_PORT', '3306');
            $database = env('DB_DATABASE', 'onefinance');
            $username = env('DB_USERNAME', 'root');
            $password = env('DB_PASSWORD', '');

            try {
                $pdo = new PDO("mysql:host={$host};port={$port}", $username, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]);

                $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
                $this->info("✅ Database MySQL '{$database}' berhasil diverifikasi / dibuat otomatis.");
            } catch (Exception $e) {
                $this->warn("⚠️ Tidak dapat terhubung ke MySQL ({$host}:{$port}): " . $e->getMessage());
                $this->line("   Pastikan MySQL di Laragon / XAMPP sudah berjalan jika ingin menghubungkan database.");
            }
        }
    }
}
