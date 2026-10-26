<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    /**
     * HALAMAN BACKUP
     */
    public function index()
    {
        $backups = $this->getBackupFiles();

        return view('backup.index', compact('backups'));
    }

    /**
     * JALANKAN BACKUP MANUAL
     */
    public function create()
    {
        try {
            Artisan::call('backup:database');
            $output = Artisan::output();

            return redirect()
                ->route('backup.index')
                ->with('success', '✅ Backup berhasil dibuat!');
        } catch (\Exception $e) {
            return redirect()
                ->route('backup.index')
                ->with('error', '❌ Backup gagal: ' . $e->getMessage());
        }
    }

    /**
     * DOWNLOAD BACKUP
     */
    public function download($filename)
    {
        $path = storage_path('app/backup/' . $filename);

        if (!file_exists($path)) {
            return redirect()
                ->route('backup.index')
                ->with('error', 'File tidak ditemukan!');
        }

        return response()->download($path, $filename);
    }

    /**
     * HAPUS BACKUP
     */
    public function destroy($filename)
    {
        $path = storage_path('app/backup/' . $filename);

        if (file_exists($path)) {
            unlink($path);
            return redirect()
                ->route('backup.index')
                ->with('success', '🗑️ Backup berhasil dihapus!');
        }

        return redirect()
            ->route('backup.index')
            ->with('error', 'File tidak ditemukan!');
    }

    /**
     * RESTORE BACKUP
     */
    public function restore($filename)
    {
        $path = storage_path('app/backup/' . $filename);

        if (!file_exists($path)) {
            return redirect()
                ->route('backup.index')
                ->with('error', 'File tidak ditemukan!');
        }

        try {
            $dbName = env('DB_DATABASE');
            $dbUser = env('DB_USERNAME');
            $dbPass = env('DB_PASSWORD');
            $dbHost = env('DB_HOST');
            $dbPort = env('DB_PORT');

            // 🔥 UNZIP FILE
            $sqlPath = str_replace('.gz', '', $path);
            $gz = gzopen($path, 'rb');
            $sql = gzread($gz, filesize($path) * 10);
            gzclose($gz);

            // 🔥 SIMPAN SEMENTARA
            file_put_contents($sqlPath, $sql);

            // 🔥 PERINTAH RESTORE
            $command = sprintf(
                'mysql --host=%s --port=%s --user=%s --password=%s %s < %s',
                escapeshellarg($dbHost),
                escapeshellarg($dbPort),
                escapeshellarg($dbUser),
                escapeshellarg($dbPass),
                escapeshellarg($dbName),
                escapeshellarg($sqlPath)
            );

            exec($command, $output, $returnCode);

            // 🔥 HAPUS FILE SQL SEMENTARA
            unlink($sqlPath);

            if ($returnCode === 0) {
                return redirect()
                    ->route('backup.index')
                    ->with('success', '✅ Database berhasil direstore!');
            } else {
                return redirect()
                    ->route('backup.index')
                    ->with('error', '❌ Restore gagal!');
            }
        } catch (\Exception $e) {
            return redirect()
                ->route('backup.index')
                ->with('error', '❌ Restore gagal: ' . $e->getMessage());
        }
    }

    /**
     * AMBIL DAFTAR FILE BACKUP
     */
    private function getBackupFiles()
    {
        $path = storage_path('app/backup');
        $files = [];

        if (is_dir($path)) {
            $list = glob($path . '/*.gz');

            foreach ($list as $file) {
                $filename = basename($file);
                $size = filesize($file);
                $date = date('d/m/Y H:i', filemtime($file));

                $files[] = [
                    'name' => $filename,
                    'size' => $this->formatSize($size),
                    'date' => $date,
                    'path' => $file,
                ];
            }
        }

        // Urutkan dari yang terbaru
        usort($files, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return $files;
    }

    /**
     * FORMAT UKURAN FILE
     */
    private function formatSize($size)
    {
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $size > 1024; $i++) {
            $size /= 1024;
        }

        return round($size, 2) . ' ' . $units[$i];
    }
}