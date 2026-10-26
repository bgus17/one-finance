<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin
        User::updateOrCreate(
            ['email' => 'admin@koperasi.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Kepala
        User::updateOrCreate(
            ['email' => 'kepala@koperasi.com'],
            [
                'name' => 'Kepala Koperasi',
                'password' => Hash::make('password'),
                'role' => 'kepala',
            ]
        );

        $this->command->info('✅ User berhasil dibuat!');
        $this->command->info('📧 Admin: admin@koperasi.com');
        $this->command->info('📧 Kepala: kepala@koperasi.com');
        $this->command->info('🔑 Password: password');
    }
}