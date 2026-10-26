<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pinjaman extends Model
{
    protected $table = 'pinjamans';

    protected $fillable = [

        /*
        |--------------------------------------------------------------------------
        | RELASI
        |--------------------------------------------------------------------------
        */

        'nasabah_id',
        'dc_id',
        'paket_pinjaman_id',
        


        /*
        |--------------------------------------------------------------------------
        | DATA PINJAMAN
        |--------------------------------------------------------------------------
        */

        'jumlah_pinjam',
        'total_pinjaman',
        'nominal_adm',
        'nominal_dicairkan',
        'sisa_tagihan',
        'tenor',
        'angsuran_per_minggu',

        /*
        |--------------------------------------------------------------------------
        | TANGGAL
        |--------------------------------------------------------------------------
        */

        'tanggal_pengajuan',
        'tanggal_cair',
        'jatuh_tempo',

        /*
        |--------------------------------------------------------------------------
        | MONITORING
        |--------------------------------------------------------------------------
        */

        'minggu_berjalan',
        'saldo',
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI NASABAH
    |--------------------------------------------------------------------------
    */

    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class, 'nasabah_id');
    }

    public function dc()
{
    return $this->belongsTo(
        Dc::class,
        'dc_id',
    );
}

    /*
    |--------------------------------------------------------------------------
    | RELASI PAKET PINJAMAN
    |--------------------------------------------------------------------------
    */

    public function paketPinjaman()
    {
        return $this->belongsTo(
            PaketPinjaman::class,
            'paket_pinjaman_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI ANGSURAN
    |--------------------------------------------------------------------------
    */

    public function angsuran()
    {
        return $this->hasMany(
            Angsuran::class,
            'pinjaman_id',
        );
    }

    /*
|--------------------------------------------------------------------------
| RELASI PEMBAYARAN
|--------------------------------------------------------------------------
*/

public function pembayaran()
{
    return $this->hasManyThrough(
        Pembayaran::class,
        Angsuran::class,
        'pinjaman_id',
        'angsuran_id',
        'id',
    );
}

    /*
    |--------------------------------------------------------------------------
    | HELPER
    |--------------------------------------------------------------------------
    */

    public function totalTerbayar()
    {
        return $this->angsuran()
            ->sum('jumlah_bayar');
    }

    public function sisaPinjaman()
    {
        return $this->total_pinjaman - $this->totalTerbayar();
    }

    public function dkhDetail()
{
    return $this->hasMany(
        DkhDetail::class,
        'pinjaman_id',
    );
}

// Relasi ke kas keluar (polymorphic)
public function kasKeluar()
{
    return $this->morphOne(KasKeluar::class, 'referensi');
}

// Accessor untuk mengetahui sisa modal
public function getSisaModalAttribute()
{
    $modal = Modal::query()->where('status', 'aktif')->first();
    return $modal ? $modal->saldo_terkini : 0;
}

  public function isMacet()
    {
        return $this->status == 'macet';
    }

}
