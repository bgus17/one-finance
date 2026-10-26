<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModalSeeder extends Seeder
{
    public function run()
    {
        // Cek apakah sudah ada data modal
        $existing = DB::table('modal')->where('nama_modal', 'Kas Koperasi')->first();
        
        if (!$existing) {
            DB::table('modal')->insert([
                'tanggal' => now(),
                'nominal' => 185863738, // Rp 185.863.738
                'nama_modal' => 'Kas Koperasi',
                'saldo' => 185863738,
                'status' => 'aktif',
                'keterangan' => 'Modal awal koperasi',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $this->command->info('✅ Data modal awal berhasil dibuat!');
        } else {
            $this->command->info('⚠️ Data modal sudah ada!');
        }
    }
}