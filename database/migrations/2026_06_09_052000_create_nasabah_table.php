<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nasabah', function (Blueprint $table) {
            $table->id();
            
            // ============================================
            // 📋 IDENTITAS NASABAH
            // ============================================
            $table->string('kode_nasabah')->unique();
            $table->string('nama');
            $table->string('nik')->unique();
            
            // ============================================
            // 🎂 TANGGAL LAHIR
            // ============================================
            $table->date('tanggal_lahir')->nullable();
            
            // ============================================
            // 📍 ALAMAT
            // ============================================
            $table->text('alamat')->nullable();
            
            // ============================================
            // 🔑 FOREIGN KEY
            // ============================================
            $table->foreignId('wilayah_id')->nullable()->constrained('wilayah')->onDelete('set null');
            $table->foreignId('desa_id')->nullable()->constrained('desas')->onDelete('set null');
            $table->foreignId('dc_id')->nullable()->constrained('dc')->onDelete('set null');
            
            // ============================================
            // 📞 KONTAK
            // ============================================
            $table->string('no_hp')->nullable(); // 🔥 PERBAIKI: HAPUS ->change()
            $table->string('pekerjaan')->nullable();
            
            // ============================================
            // 📷 FOTO
            // ============================================
            $table->string('foto_ktp')->nullable();
            $table->string('foto_nasabah')->nullable();
            
            // ============================================
            // 📊 STATUS
            // ============================================
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            
            // ============================================
            // ⏰ TIMESTAMPS
            // ============================================
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nasabah');
    }
};