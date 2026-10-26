<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DcSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil user_id dari user pertama
        $user = DB::table('users')->first();
        $userId = $user ? $user->id : 1;

        // Cari wilayah berdasarkan nama
        $wilayahUtara = DB::table('wilayah')->where('nama_wilayah', 'Wilayah Utara')->first();
        $wilayahSelatan = DB::table('wilayah')->where('nama_wilayah', 'Wilayah Selatan')->first();

        $dc = [
            [
                'kode_dc' => 'DC001',
                'nama_dc' => 'DC Ikhsan',
                 'no_hp' => '081936206695',
                'alamat' => 'Jl. Margomulyo',
                'wilayah_id' => $wilayahSelatan ? $wilayahSelatan->id : null,
                'user_id' => $userId,
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_dc' => 'DC002',
                'nama_dc' => 'DC Ariel',
                 'no_hp' => '085769503213',
                'alamat' => 'Jl. Nusa Jaya',
                'wilayah_id' => $wilayahUtara ? $wilayahUtara->id : null,
                'user_id' => $userId,
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_dc' => 'DC003',
                'nama_dc' => 'DC Dian',
                'no_hp' => '081540964579',
                'alamat' => 'Jl. Margomulyo',
                'wilayah_id' => $wilayahUtara ? $wilayahUtara->id : null,
                'user_id' => $userId,
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_dc' => 'DC004',
                'nama_dc' => 'DC Muksin',
                'no_hp' => '085769633700',
                'alamat' => 'Wilayah Utara',
                'wilayah_id' => $wilayahUtara ? $wilayahUtara->id : null,
                'user_id' => $userId,
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($dc as $item) {
            $exists = DB::table('dc')->where('kode_dc', $item['kode_dc'])->exists();
            if (!$exists) {
                DB::table('dc')->insert($item);
            }
        }
    }
}