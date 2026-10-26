<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayarans';

    protected $fillable = [
        'angsuran_id',
        'dkh_detail_id',   
        'jumlah_tagihan',
        'jumlah_bayar',
        'selisih',
        'tanggal_bayar',
        'metode_bayar',      
        'keterangan',
        'user_id',  
        'pokok',
        'margin',
    ];

    public function angsuran()
    {
        return $this->belongsTo(Angsuran::class);
    }

    public function dkhDetail()
    {
        return $this->belongsTo(DkhDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}