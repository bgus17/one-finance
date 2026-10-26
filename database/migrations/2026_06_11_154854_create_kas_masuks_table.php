<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kas_masuk', function (Blueprint $table) {
            $table->id();
            
            // ============================================
            // 🔑 TANPA FOREIGN KEY (SEMENTARA)
            // ============================================
            $table->unsignedBigInteger('dc_id')->nullable();
            // $table->foreign('dc_id')->references('id')->on('dcs')->onDelete('cascade');
            
            $table->foreignId('modal_id')
                  ->nullable()
                  ->constrained('modal')
                  ->onDelete('cascade');
            
            $table->date('tanggal');
            $table->string('jenis');
            $table->bigInteger('nominal');
            $table->string('referensi')->nullable();
            $table->unsignedBigInteger('referensi_id')->nullable();
            $table->string('referensi_type')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kas_masuk');
    }
};