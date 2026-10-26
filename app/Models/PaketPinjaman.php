<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaketPinjaman extends Model
{
    protected $table = 'paket_pinjaman';

    protected $fillable = [
        'kode_paket',
        'dc_id',
        'nama_paket',
        'nominal_pinjaman',
        'persen_adm',
        'nominal_adm',
        'nominal_dicairkan',
        'total_tagihan',
        'tenor_minggu',
        'angsuran_mingguan',
        'status'
    ];

    public function pinjaman()
    {
        return $this->hasMany(
            Pinjaman::class,
            'paket_pinjaman_id'
        );
    }

    public function dc()
{
    return $this->belongsTo(
        Dc::class,
        'dc_id'
    );
}
}