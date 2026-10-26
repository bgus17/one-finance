<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dc extends Model
{
    protected $table = 'dc';

    protected $fillable = [
        'user_id',
        'wilayah_id',
        'kode_dc',
        'nama_dc',
        'no_hp',
        'alamat',
        'tanggal_masuk',
        'status'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI USER
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI WILAYAH
    |--------------------------------------------------------------------------
    */

    public function wilayah()
    {
        return $this->belongsTo(
            Wilayah::class,
            'wilayah_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI NASABAH
    |--------------------------------------------------------------------------
    */

    public function nasabah()
    {
        return $this->hasMany(
            Nasabah::class,
            'dc_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI SETORAN DC
    |--------------------------------------------------------------------------
    */

    public function setoran()
    {
        return $this->hasMany(
            SetoranDc::class,
            'dc_id'
        );
    }

    public function dkh()
{
    return $this->hasMany(
        Dkh::class,
        'dc_id'
    );
}
}