<?php

namespace App\Console\Commands;

use App\Models\KasKeluar;
use App\Models\KasMasuk;
use App\Models\Modal;
use App\Models\Pinjaman;
use Illuminate\Console\Command;

class SyncPendapatanAdministrasiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pinjaman:sync-admin-fees';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi pendapatan administrasi pinjaman ke tabel kas_masuk dan perbarui dc_id kas_keluar';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Memulai sinkronisasi pendapatan administrasi pinjaman...');

        $modal = Modal::where('status', 'aktif')->first();
        if (!$modal) {
            $this->error('❌ Modal aktif tidak ditemukan!');
            return Command::FAILURE;
        }

        $pinjamans = Pinjaman::with('nasabah')
            ->where('status', '!=', 'ditolak')
            ->where('nominal_adm', '>', 0)
            ->get();

        $syncedMasuk = 0;
        $skippedMasuk = 0;
        $updatedKeluar = 0;

        foreach ($pinjamans as $pinjaman) {
            // 1. Cek kas_masuk biaya administrasi
            $kasMasukExists = KasMasuk::where(function ($q) use ($pinjaman) {
                $q->where(function ($sub) use ($pinjaman) {
                    $sub->where('referensi_type', Pinjaman::class)
                        ->where('referensi_id', $pinjaman->id);
                })->orWhere(function ($sub) use ($pinjaman) {
                    $sub->where('jenis', 'Biaya Administrasi')
                        ->where('referensi', 'Pinjaman: ' . $pinjaman->id);
                });
            })->first();

            if (!$kasMasukExists) {
                KasMasuk::create([
                    'modal_id'       => $modal->id,
                    'dc_id'          => $pinjaman->dc_id,
                    'tanggal'        => $pinjaman->tanggal_cair,
                    'jenis'          => 'Biaya Administrasi',
                    'nominal'        => $pinjaman->nominal_adm,
                    'referensi'      => 'Pinjaman: ' . $pinjaman->id,
                    'referensi_id'   => $pinjaman->id,
                    'referensi_type' => Pinjaman::class,
                    'keterangan'     => 'Biaya administrasi pinjaman - ' . ($pinjaman->nasabah->nama ?? 'Nasabah #' . $pinjaman->nasabah_id),
                ]);
                $syncedMasuk++;
                $this->line("➕ Kas Masuk Admin dibuat: Pinjaman #{$pinjaman->id} (Rp " . number_format($pinjaman->nominal_adm, 0, ',', '.') . ") - Nasabah: " . ($pinjaman->nasabah->nama ?? '-'));
            } else {
                // Update dc_id jika masih null
                if (is_null($kasMasukExists->dc_id) && $pinjaman->dc_id) {
                    $kasMasukExists->dc_id = $pinjaman->dc_id;
                    $kasMasukExists->save();
                }
                $skippedMasuk++;
            }

            // 2. Cek kas_keluar pencairan pinjaman untuk sinkronisasi dc_id & referensi
            $kasKeluar = KasKeluar::where(function ($q) use ($pinjaman) {
                $q->where(function ($sub) use ($pinjaman) {
                    $sub->where('referensi_type', Pinjaman::class)
                        ->where('referensi_id', $pinjaman->id);
                })->orWhere('referensi', 'Pinjaman: ' . $pinjaman->id);
            })->first();

            if ($kasKeluar && is_null($kasKeluar->dc_id) && $pinjaman->dc_id) {
                $kasKeluar->dc_id = $pinjaman->dc_id;
                if (!$kasKeluar->referensi) {
                    $kasKeluar->referensi = 'Pinjaman: ' . $pinjaman->id;
                }
                $kasKeluar->save();
                $updatedKeluar++;
            }
        }

        $this->info("✅ Sinkronisasi selesai!");
        $this->table(
            ['Total Pinjaman Diperiksa', 'Kas Masuk Admin Baru', 'Kas Masuk Sudah Ada', 'Kas Keluar dc_id Diperbarui'],
            [[count($pinjamans), $syncedMasuk, $skippedMasuk, $updatedKeluar]]
        );

        return Command::SUCCESS;
    }
}
