<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SetoranDc extends Model
{
    protected $table = 'setoran_dc';
    protected $fillable = [
        'dc_id',
        'tanggal_setor',
        'jumlah_tagihan',
        'jumlah_setor',
        'selisih',
        'keterangan'
    ];

    public function dc()
    {
        return $this->belongsTo(Dc::class);
    }
}