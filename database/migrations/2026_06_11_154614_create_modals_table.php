<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modal', function (Blueprint $table) {
            $table->id();
            
            // ============================================
            // 📅 INFORMASI MODAL
            // ============================================
            $table->date('tanggal');
            $table->bigInteger('nominal');
            
            // ============================================
            // 💰 SALDO & STATUS (dari migration add_saldo)
            // ============================================
            $table->decimal('saldo', 15, 2)->default(0);
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            
            // ============================================
            // 📝 NAMA MODAL (rename dari 'jenis')
            // ============================================
            $table->string('nama_modal')->default('modal_awal');
            
            // ============================================
            // 📝 KETERANGAN
            // ============================================
            $table->text('keterangan')->nullable();
            
            // ============================================
            // ⏰ TIMESTAMPS
            // ============================================
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modal');
    }
};