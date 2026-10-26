<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();

            // ============================================
            // 🔑 FOREIGN KEY
            // ============================================
            $table->foreignId('dkh_detail_id')->nullable()->constrained('dkh_detail');
            $table->foreignId('angsuran_id')->nullable()->constrained('angsurans')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users');

            // ============================================
            // 💰 DATA PEMBAYARAN
            // ============================================
            $table->bigInteger('jumlah_tagihan');
            $table->bigInteger('jumlah_bayar');

            // ============================================
            // 📊 POKOK & MARGIN
            // ============================================
            $table->decimal('pokok', 15, 2)->default(0);
            $table->decimal('margin', 15, 2)->default(0);

            // ============================================
            // 📈 SELISIH & SISA
            // ============================================
            $table->bigInteger('selisih')->default(0);
            $table->bigInteger('sisa_tagihan')->default(0);

            // ============================================
            // 📅 INFORMASI PEMBAYARAN
            // ============================================
            $table->date('tanggal_bayar');
            $table->enum('metode_bayar', ['kas', 'transfer'])->nullable()->default('kas');
            $table->string('no_kwitansi')->nullable();
            $table->text('keterangan')->nullable();

            // ============================================
            // ⏰ TIMESTAMPS
            // ============================================
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};