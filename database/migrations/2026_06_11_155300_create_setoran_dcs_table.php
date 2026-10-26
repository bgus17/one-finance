<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setoran_dc', function (Blueprint $table) {

            $table->id();

            $table->foreignId('dc_id')
                ->constrained('dc')
                ->cascadeOnDelete();

            $table->date('tanggal_setor');

            $table->bigInteger('jumlah_tagihan');

            $table->bigInteger('jumlah_setor');

            $table->bigInteger('selisih')->default(0);

            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setoran_dc');
    }
};