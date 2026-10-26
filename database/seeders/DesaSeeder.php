<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesaSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================
        // WILAYAH UTARA (untuk DC Ariel & DC Dian)
        // ============================================
        $wilayahUtara = DB::table('wilayah')->where('nama_wilayah', 'Wilayah Utara')->first();
        if (!$wilayahUtara) {
            $wilayahUtaraId = DB::table('wilayah')->insertGetId([
                'nama_wilayah' => 'Wilayah Utara',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $wilayahUtaraId = $wilayahUtara->id;
        }

        // ============================================
        // WILAYAH SELATAN (untuk DC Ikhsan)
        // ============================================
        $wilayahSelatan = DB::table('wilayah')->where('nama_wilayah', 'Wilayah Selatan')->first();
        if (!$wilayahSelatan) {
            $wilayahSelatanId = DB::table('wilayah')->insertGetId([
                'nama_wilayah' => 'Wilayah Selatan',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $wilayahSelatanId = $wilayahSelatan->id;
        }

        // ============================================
        // DESA DC IKHSAN (Wilayah Selatan)
        // ============================================
        $desaIkhsan = [
            ['kode_desa' => 'DS001', 'nama_desa' => 'SINUMARGA'],
            ['kode_desa' => 'DS002', 'nama_desa' => 'NUSA JAYA'],
            ['kode_desa' => 'DS003', 'nama_desa' => 'NUSA BAKTI'],
            ['kode_desa' => 'DS004', 'nama_desa' => 'NUSA TENGGARA'],
            ['kode_desa' => 'DS005', 'nama_desa' => 'KARANG JADI'],
            ['kode_desa' => 'DS006', 'nama_desa' => 'SUMBER REJO'],
            ['kode_desa' => 'DS007', 'nama_desa' => 'NUSA MAJU'],
            ['kode_desa' => 'DS008', 'nama_desa' => 'NUSA BALI'],
            ['kode_desa' => 'DS009', 'nama_desa' => 'KARYA MAJU'],
        ];

        foreach ($desaIkhsan as $item) {
            $this->insertDesa($item['kode_desa'], $item['nama_desa'], $wilayahSelatanId);
        }

        // ============================================
        // DESA DC ARIEL (Wilayah Utara)
        // ============================================
        $desaAriel = [
            ['kode_desa' => 'DS010', 'nama_desa' => 'SUMBER JAYA'],
            ['kode_desa' => 'DS011', 'nama_desa' => 'KARANG MANIK'],
            ['kode_desa' => 'DS012', 'nama_desa' => 'MARGOMULYO'],
            ['kode_desa' => 'DS013', 'nama_desa' => 'SRIBANTOLO'],
            ['kode_desa' => 'DS014', 'nama_desa' => 'NUSA AGUNG'],
            ['kode_desa' => 'DS015', 'nama_desa' => 'SUMBER RAHAYU'],
            ['kode_desa' => 'DS016', 'nama_desa' => 'NUSA RAYA'],
            ['kode_desa' => 'DS017', 'nama_desa' => 'SINAR BALI'],
            ['kode_desa' => 'DS018', 'nama_desa' => 'KARYA MAKMUR'],
        ];

        foreach ($desaAriel as $item) {
            $this->insertDesa($item['kode_desa'], $item['nama_desa'], $wilayahUtaraId);
        }

        // ============================================
        // DESA DC DIAN (Wilayah Utara)
        // ============================================
        $desaDian = [
            ['kode_desa' => 'DS019', 'nama_desa' => 'SUKA JAYA'],
            ['kode_desa' => 'DS020', 'nama_desa' => 'CAHYA MAS'],
            // Desa yang sama dengan Ariel (tidak perlu diulang, sudah ada)
            // MARGOMULYO, SRIBANTOLO sudah ada di DC Ariel
        ];

        foreach ($desaDian as $item) {
            $this->insertDesa($item['kode_desa'], $item['nama_desa'], $wilayahUtaraId);
        }

        // ============================================
        // DESA TAMBAHAN (Wilayah Utara)
        // ============================================
        $desaTambahan = [
            ['kode_desa' => 'DS021', 'nama_desa' => 'ULAK BUNTAR'],
            ['kode_desa' => 'DS022', 'nama_desa' => 'KARYA USAHA'],
            ['kode_desa' => 'DS023', 'nama_desa' => 'PURWOSARI'],
            ['kode_desa' => 'DS024', 'nama_desa' => 'NUSA TUNGGAL'],
            ['kode_desa' => 'DS025', 'nama_desa' => 'SRI WANGI'],
        ];

        foreach ($desaTambahan as $item) {
            $this->insertDesa($item['kode_desa'], $item['nama_desa'], $wilayahUtaraId);
        }
    }

    /**
     * Helper function untuk insert desa
     */
    private function insertDesa($kodeDesa, $namaDesa, $wilayahId)
    {
        $exists = DB::table('desas')->where('nama_desa', $namaDesa)->exists();
        if (!$exists) {
            DB::table('desas')->insert([
                'wilayah_id' => $wilayahId,
                'kode_desa' => $kodeDesa,
                'nama_desa' => $namaDesa,
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}