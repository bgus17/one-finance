<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Desa extends Model
{
    protected $table = 'desas';

    protected $fillable = [
        'wilayah_id',
        'kode_desa',
        'nama_desa',
        'status',
    ];

    // 🔥 RELASI KE WILAYAH
    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class);
    }

    // 🔥 RELASI KE NASABAH
    public function nasabah()
    {
        return $this->hasMany(Nasabah::class);
    }
}