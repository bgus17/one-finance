<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database';
    protected $description = 'Backup database koperasi';

    public function handle()
    {
        // 🔥 KONFIGURASI
        $dbName = env('DB_DATABASE');
        $dbUser = env('DB_USERNAME');
        $dbPass = env('DB_PASSWORD');
        $dbHost = env('DB_HOST');
        $dbPort = env('DB_PORT');

        // 🔥 NAMA FILE BACKUP
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $filename = "backup_{$dbName}_{$timestamp}.sql";
        $backupPath = storage_path("app/backup/{$filename}");

        // 🔥 BUAT FOLDER BACKUP
        if (!is_dir(storage_path('app/backup'))) {
            mkdir(storage_path('app/backup'), 0777, true);
        }

        // 🔥 PERINTAH MYSQLDUMP
        $command = sprintf(
            'mysqldump --host=%s --port=%s --user=%s --password=%s %s > %s',
            escapeshellarg($dbHost),
            escapeshellarg($dbPort),
            escapeshellarg($dbUser),
            escapeshellarg($dbPass),
            escapeshellarg($dbName),
            escapeshellarg($backupPath)
        );

        // 🔥 EKSEKUSI
        exec($command, $output, $returnCode);

        if ($returnCode === 0) {
            // 🔥 KOMPRES FILE
            $gzipPath = $backupPath . '.gz';
            $gzip = gzopen($gzipPath, 'wb9');
            gzwrite($gzip, file_get_contents($backupPath));
            gzclose($gzip);

            // 🔥 HAPUS FILE SQL
            unlink($backupPath);

            // 🔥 HAPUS BACKUP LAMA (>30 hari)
            $this->deleteOldBackups(30);

            $this->info('✅ Backup berhasil: ' . $filename . '.gz');
            \Log::info('Backup database berhasil: ' . $filename . '.gz');

            return 0;
        } else {
            $this->error('❌ Backup gagal!');
            \Log::error('Backup database gagal!');
            return 1;
        }
    }

    /**
     * Hapus backup lama
     */
    private function deleteOldBackups($days)
    {
        $path = storage_path('app/backup');
        $files = glob($path . '/*.gz');

        $now = Carbon::now();

        foreach ($files as $file) {
            $timestamp = filemtime($file);
            $date = Carbon::createFromTimestamp($timestamp);

            if ($date->diffInDays($now) > $days) {
                unlink($file);
                $this->info('🗑️ Backup lama dihapus: ' . basename($file));
            }
        }
    }
}