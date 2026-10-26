<?php

namespace App\Console\Commands;

use App\Services\DkhService;
use Illuminate\Console\Command;

class GenerateDkhOtomatisCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dkh:generate-otomatis 
                            {--minggu= : Nomor minggu ke- (1-52)} 
                            {--tahun= : Tahun (misal: 2026)} 
                            {--force : Paksa sinkronisasi ulang jika DKH sudah ada}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate atau sinkronisasi DKH otomatis untuk setiap DC mengikuti tanggal cair pinjaman nasabah';

    /**
     * Execute the console command.
     */
    public function handle(DkhService $dkhService): int
    {
        $this->info('🚀 Memulai proses generate DKH otomatis...');

        $minggu = $this->option('minggu');
        $tahun = $this->option('tahun');
        $force = (bool) $this->option('force');

        if (!$minggu || !$tahun) {
            $aktif = $dkhService->getMingguDanTahunAktif();
            $minggu = $minggu ? (int) $minggu : $aktif['minggu'];
            $tahun = $tahun ? (int) $tahun : $aktif['tahun'];
        } else {
            $minggu = (int) $minggu;
            $tahun = (int) $tahun;
        }

        $periode = $dkhService->getPeriodeMinggu($tahun, $minggu);
        $this->line("📅 Periode DKH: Minggu ke-{$minggu} ({$tahun}) [{$periode['label']}]");

        $hasil = $dkhService->generateUntukMinggu($minggu, $tahun, $force);

        if (!$hasil['status']) {
            $this->error("❌ Gagal men-generate DKH: " . ($hasil['error'] ?? 'Terjadi kesalahan sistem'));
            return Command::FAILURE;
        }

        $this->info("✅ Sukses memproses DKH:");
        $this->table(
            ['Total DC', 'DKH Baru Dibuat', 'DKH Diperbarui', 'DKH Dilewati (Sudah Ada)'],
            [[
                $hasil['total_dc'],
                $hasil['created'],
                $hasil['updated'],
                $hasil['skipped'],
            ]]
        );

        return Command::SUCCESS;
    }
}
