<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paket_pinjaman', function (Blueprint $table) {
            $table->id();

            // ============================================
            // 📋 IDENTITAS PAKET
            // ============================================
            $table->string('kode_paket')->unique();
            $table->string('nama_paket');

            // ============================================
            // 💰 NOMINAL
            // ============================================
            $table->bigInteger('nominal_pinjaman');
            $table->integer('persen_adm')->default(5);
            $table->bigInteger('nominal_adm');
            $table->bigInteger('nominal_dicairkan');

            // ============================================
            // 📊 BUNGA (dari migration add_bunga)
            // ============================================
            $table->decimal('bunga', 10, 2)->nullable();

            // ============================================
            // 📅 TENOR & ANGSURAN
            // ============================================
            $table->integer('tenor_minggu');
            $table->bigInteger('angsuran_mingguan');
            $table->bigInteger('total_tagihan');

            // ============================================
            // 📌 STATUS
            // ============================================
            $table->enum('status', [
                'aktif',
                'nonaktif'
            ])->default('aktif');

            // ============================================
            // ⏰ TIMESTAMPS
            // ============================================
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paket_pinjaman');
    }
};