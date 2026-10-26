<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dkh extends Model
{
    protected $table = 'dkh';

    protected $fillable = [

        'dc_id',

        'nomor_dkh',

        'tanggal_dkh',

        'minggu_ke',

        'tahun',

        'total_nasabah',

        'total_jb',

        'total_angsuran',

        'total_denda',

        'total_tagihan',

        'status'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI DC
    |--------------------------------------------------------------------------
    */

    public function dc()
    {
        return $this->belongsTo(
            Dc::class,
            'dc_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI DETAIL
    |--------------------------------------------------------------------------
    */

    public function detail()
    {
        return $this->hasMany(
            DkhDetail::class,
            'dkh_id'
        );
    }
}