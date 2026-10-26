<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::create('dkh_detail', function (Blueprint $table) {

    $table->id();

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    $table->foreignId('dkh_id')
        ->constrained('dkh')
        ->cascadeOnDelete();

    $table->foreignId('pinjaman_id')
        ->constrained('pinjamans')
        ->cascadeOnDelete();

    $table->foreignId('nasabah_id')
        ->constrained('nasabah')
        ->cascadeOnDelete();

    $table->foreignId('angsuran_id')
        ->constrained('angsurans')
        ->cascadeOnDelete();

    /*
    |--------------------------------------------------------------------------
    | DATA DKH
    |--------------------------------------------------------------------------
    */

    // Kekurangan / Kelebihan minggu lalu
    $table->bigInteger('jb')->default(0);

    // Angsuran minggu ini
    $table->bigInteger('angsuran');

    // Minggu yang dibayar
    $table->unsignedTinyInteger('bayar_ke');

    // Lama pinjaman
    $table->unsignedTinyInteger('jumlah_minggu');

    // Denda manual
    $table->bigInteger('denda')->default(0);

    // JB + Angsuran + Denda
    $table->bigInteger('total_tagihan');

    /*
    |--------------------------------------------------------------------------
    | HASIL TAGIHAN
    |--------------------------------------------------------------------------
    */

    $table->bigInteger('jumlah_bayar')->default(0);

    $table->bigInteger('sisa_bayar')->default(0);

    /*
    |--------------------------------------------------------------------------
    | STATUS
    |--------------------------------------------------------------------------
    */

    $table->enum('status',[
        'belum_bayar',
        'kurang_bayar',
        'lunas'
    ])->default('belum_bayar');

    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('dkh_detail');
    }
};