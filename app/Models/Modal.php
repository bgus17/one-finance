<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modal extends Model
{
    protected $table = 'modal';

    protected $fillable = [
        'tanggal',
        'nominal',
        'nama_modal',
        'saldo',
        'status',
        'keterangan'
    ];

    // Accessor & Mutator agar support 'jenis' sebagai alias 'nama_modal'
    public function getJenisAttribute()
    {
        return $this->nama_modal;
    }

    public function setJenisAttribute($value)
    {
        $this->attributes['nama_modal'] = $value;
    }

    // Relasi ke kas masuk
    public function kasMasuk()
    {
        return $this->hasMany(KasMasuk::class);
    }

    // Relasi ke kas keluar
    public function kasKeluar()
    {
        return $this->hasMany(KasKeluar::class);
    }

    // Method untuk update saldo
    public function updateSaldo($jumlah, $jenis)
    {
        if ($jenis == 'masuk') {
            $this->saldo += $jumlah;
        } else if ($jenis == 'keluar') {
            $this->saldo -= $jumlah;
        }
        $this->save();
        return $this;
    }

    // Cek saldo mencukupi
    public function saldoMencukupi($jumlah)
    {
        return $this->saldo >= $jumlah;
    }

    // Scope untuk ambil modal aktif
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}