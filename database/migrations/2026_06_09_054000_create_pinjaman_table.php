<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pinjamans', function (Blueprint $table) {
            $table->id();

            // ============================================
            // 🔑 RELASI
            // ============================================
            $table->foreignId('nasabah_id')
                ->constrained('nasabah')
                ->cascadeOnDelete();

            $table->foreignId('dc_id')
                ->nullable()
                ->constrained('dc')
                ->nullOnDelete();

            $table->foreignId('paket_pinjaman_id')
                ->constrained('paket_pinjaman')
                ->cascadeOnDelete();

            // ============================================
            // 💰 DATA PINJAMAN
            // ============================================
            $table->bigInteger('jumlah_pinjam');
            $table->bigInteger('nominal_adm');
            $table->bigInteger('nominal_dicairkan');
            $table->bigInteger('total_pinjaman');
            $table->bigInteger('sisa_tagihan');
            $table->unsignedTinyInteger('tenor');
            $table->bigInteger('angsuran_per_minggu');
            $table->bigInteger('saldo')
                ->default(0)
                ->comment('Saldo berjalan: >0 = kekurangan bayar, <0 = kelebihan bayar, 0 = lunas pas');

            // ============================================
            // 📅 TANGGAL
            // ============================================
            $table->date('tanggal_pengajuan');
            $table->date('tanggal_cair');
            $table->date('jatuh_tempo');

            // ============================================
            // 📊 MONITORING
            // ============================================
            $table->unsignedTinyInteger('minggu_berjalan')->default(0);

            // ============================================
            // 📌 STATUS (gabungan dari migration add_macet_status)
            // ============================================
            $table->enum('status', [
                'menunggu',
                'aktif',
                'lunas',
                'ditolak',
                'macet'
            ])->default('menunggu');

            // ============================================
            // ⏰ TIMESTAMPS
            // ============================================
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pinjamans');
    }
};