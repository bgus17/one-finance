<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Angsuran extends Model
{
    protected $table = 'angsurans';

    protected $fillable = [
        'pinjaman_id',
        'minggu_ke',
        'tanggal_jatuh_tempo',
        'jumlah_tagihan',
        'jumlah_bayar',
        'sisa_tagihan',
        'tanggal_lunas',
        'status',
    ];

    public function pinjaman()
    {
        return $this->belongsTo(
            Pinjaman::class,
            'pinjaman_id',
        );
    }

    public function pembayaran()
    {
        return $this->hasMany(
            Pembayaran::class,
            'angsuran_id',
        );
    }

    public function dkhDetail()
{
    return $this->hasMany(
        DkhDetail::class,
        'angsuran_id',
    );
}

public function kasMasuk()
{
    return $this->morphOne(KasMasuk::class, 'referensi');
}

/*
|--------------------------------------------------------------------------
| HELPER
|--------------------------------------------------------------------------
*/

public function belumLunas()
{
    return $this->status != 'lunas';
}

  public function isMacet()
    {
        return $this->status == 'macet';
    }

}