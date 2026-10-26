<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
    protected $table = 'wilayah';

    protected $fillable = [
        'nama_wilayah',
        'keterangan',
        'status'
    ];

    public function dcs()
    {
        return $this->hasMany(
            Dc::class,
            'wilayah_id'
        );
    }

      public function desas()
    {
        return $this->hasMany(Desa::class);
    }
}