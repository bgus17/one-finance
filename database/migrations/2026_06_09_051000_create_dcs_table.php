<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dc', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('wilayah_id')
                ->constrained('wilayah')
                ->cascadeOnDelete();

            $table->string('kode_dc')->unique();

            $table->string('nama_dc');

            $table->string('no_hp')->nullable();

            $table->text('alamat')->nullable();

            $table->date('tanggal_masuk')->nullable();

            $table->enum('status', [
                'aktif',
                'nonaktif'
            ])->default('aktif');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dc');
    }
};