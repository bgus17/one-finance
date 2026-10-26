<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        $wilayah = [
            ['nama_wilayah' => 'Wilayah Utara', 'status' => 'aktif'],
            ['nama_wilayah' => 'Wilayah Selatan', 'status' => 'aktif'],
        ];

        foreach ($wilayah as $item) {
            $exists = DB::table('wilayah')->where('nama_wilayah', $item['nama_wilayah'])->exists();
            if (!$exists) {
                DB::table('wilayah')->insert([
                    'nama_wilayah' => $item['nama_wilayah'],
                    'status' => $item['status'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}