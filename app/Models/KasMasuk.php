<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KasMasuk extends Model
{
    protected $table = 'kas_masuk';

    protected $fillable = [
        'dc_id',
        'modal_id',
        'tanggal',
        'jenis',          
        'nominal',        
        'referensi',
        'keterangan',
        'referensi_id',
        'referensi_type'
    ];

    public function modal()
    {
        return $this->belongsTo(Modal::class);
    }

    public function referensi()
    {
        return $this->morphTo();
    }

    public function dc()
    {
        return $this->belongsTo(Dc::class, 'dc_id');
    }

    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class, 'referensi_id')
            ->where('referensi_type', Pinjaman::class);
    }
}