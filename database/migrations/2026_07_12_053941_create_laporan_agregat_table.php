<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('laporan_agregat', function (Blueprint $table) {
            $table->id();
             $table->integer('dc_id')->nullable();
            $table->string('periode', 10); // YYYY-MM
            $table->date('periode_start');
            $table->date('periode_end');
            
            // DATA KEUANGAN
            $table->decimal('total_kas_masuk', 15, 2)->default(0);
            $table->decimal('total_kas_keluar', 15, 2)->default(0);
            $table->decimal('total_admin', 15, 2)->default(0);
            $table->decimal('total_pencairan', 15, 2)->default(0);
            $table->decimal('total_pembayaran', 15, 2)->default(0);
            
            // DATA PINJAMAN
            $table->integer('total_nasabah')->default(0);
            $table->integer('total_pinjaman')->default(0);
            $table->integer('total_pinjaman_aktif')->default(0);
            $table->integer('total_pinjaman_lunas')->default(0);
            $table->integer('total_pinjaman_macet')->default(0);
            
            // DATA SALDO
            $table->decimal('total_saldo_pinjaman', 15, 2)->default(0);
            $table->decimal('sisa_pokok', 15, 2)->default(0);
            $table->decimal('sisa_bunga', 15, 2)->default(0);
            $table->decimal('sisa_bunga_pokok', 15, 2)->default(0);
            
            // DATA MACET
            $table->decimal('pokok_macet', 15, 2)->default(0);
            $table->decimal('margin_macet', 15, 2)->default(0);
            $table->decimal('total_macet', 15, 2)->default(0);
            
            // DATA REAL
            $table->decimal('real_margin', 15, 2)->default(0);
            $table->decimal('real_sisa_pokok', 15, 2)->default(0);
            $table->decimal('real_spp', 15, 2)->default(0);
            
            // DATA PENDAPATAN & BIAYA
            $table->decimal('pendapatan_bunga', 15, 2)->default(0);
            $table->decimal('pendapatan_admin', 15, 2)->default(0);
            $table->decimal('pendapatan_denda', 15, 2)->default(0);
            $table->decimal('total_pendapatan', 15, 2)->default(0);
            $table->decimal('total_biaya', 15, 2)->default(0);
            $table->decimal('laba_bersih', 15, 2)->default(0);
            
            // DATA LAINNYA
            $table->decimal('bunga_per_minggu', 15, 2)->default(0);
            $table->decimal('saldo_kas', 15, 2)->default(0);
            $table->decimal('saldo_modal', 15, 2)->default(0);
            
            $table->timestamps();
            
            // UNIQUE: satu periode per DC
            $table->unique(['dc_id', 'periode']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('laporan_agregat');
    }
};