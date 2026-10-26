<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('angsurans', function (Blueprint $table) {
            $table->id();

            // ============================================
            // 🔑 RELASI
            // ============================================
            $table->foreignId('pinjaman_id')
                ->constrained('pinjamans')
                ->cascadeOnDelete();

            // ============================================
            // 📊 INFORMASI ANGSURAN
            // ============================================
            $table->integer('minggu_ke');
            $table->date('tanggal_jatuh_tempo');
            $table->bigInteger('jumlah_tagihan');
            $table->bigInteger('jumlah_bayar')->default(0);
            $table->bigInteger('sisa_tagihan')->default(0);
            $table->date('tanggal_lunas')->nullable();

            // ============================================
            // 📌 STATUS (gabungan dari migration add_macet_status)
            // ============================================
            $table->enum('status', [
                'belum_bayar',
                'lunas',
                'overdue',
                'macet'
            ])->default('belum_bayar');

            // ============================================
            // ⏰ TIMESTAMPS
            // ============================================
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('angsurans');
    }
};