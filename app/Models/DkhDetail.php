<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DkhDetail extends Model
{
    protected $table = 'dkh_detail';

    protected $fillable = [
        'dkh_id',
        'pinjaman_id',
        'nasabah_id',
        'angsuran_id',
        'jb',
        'angsuran',
        'bayar_ke',
        'jumlah_minggu',
        'denda',
        'total_tagihan',
        'sisa_bayar',
        'jumlah_bayar',
        'status',
    ];

    public function dkh()
    {
        return $this->belongsTo(Dkh::class);
    }

    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class);
    }

    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class);
    }

    public function angsuran()
    {
        return $this->belongsTo(Angsuran::class);
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }
}