<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::create('dkh', function (Blueprint $table) {

    $table->id();

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    $table->foreignId('dc_id')
        ->constrained('dc')
        ->cascadeOnDelete();

    /*
    |--------------------------------------------------------------------------
    | IDENTITAS DKH
    |--------------------------------------------------------------------------
    */

    $table->string('nomor_dkh')->unique();

    $table->date('tanggal_dkh');

    $table->unsignedTinyInteger('minggu_ke');

    $table->year('tahun');

    /*
    |--------------------------------------------------------------------------
    | REKAP
    |--------------------------------------------------------------------------
    */

    $table->integer('total_nasabah')->default(0);

    $table->bigInteger('total_jb')->default(0);

    $table->bigInteger('total_angsuran')->default(0);

    $table->bigInteger('total_denda')->default(0);

    $table->bigInteger('total_tagihan')->default(0);

    /*
    |--------------------------------------------------------------------------
    | STATUS
    |--------------------------------------------------------------------------
    */

    $table->enum('status',[
        'draft',
        'dicetak',
        'selesai'
    ])->default('draft');

    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('dkh');
    }
};