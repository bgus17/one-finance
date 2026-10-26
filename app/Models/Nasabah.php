<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Nasabah extends Model
{
    protected $table = 'nasabah';

    protected $fillable = [
        'kode_nasabah',
        'nama',
        'nik',
        'tanggal_lahir',
        'alamat',
        'wilayah_id',
        'desa_id',
        'dc_id',
        'no_hp',
        'pekerjaan',
        'foto_ktp',
        'foto_nasabah',
        'status',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */
    public function dc()
    {
        return $this->belongsTo(Dc::class);
    }

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id');
    }

    public function pinjaman()
    {
        return $this->hasMany(Pinjaman::class);
    }

     public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSOR
    |--------------------------------------------------------------------------
    */
    public function getTanggalLahirFormattedAttribute()
    {
        if ($this->tanggal_lahir) {
            return Carbon::parse($this->tanggal_lahir)->format('d/m/Y');
        }
        return '-';
    }

    public function getUmurAttribute()
    {
        if ($this->tanggal_lahir) {
            return Carbon::parse($this->tanggal_lahir)->age;
        }
        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | AUTO GENERATE KODE NASABAH
    |--------------------------------------------------------------------------
    */
    protected static function booted()
    {
        static::creating(function ($nasabah) {
            if (empty($nasabah->kode_nasabah)) {
                $tahun = date('Y');
                $bulan = date('m');
                
                // Cari nasabah terakhir di bulan ini
                $last = self::whereYear('created_at', $tahun)
                    ->whereMonth('created_at', $bulan)
                    ->orderBy('id', 'desc')
                    ->first();
                
                if ($last && $last->kode_nasabah) {
                    // Ambil angka terakhir
                    $angka = (int) substr($last->kode_nasabah, -4);
                    $urutan = str_pad($angka + 1, 4, '0', STR_PAD_LEFT);
                } else {
                    $urutan = '0001';
                }
                
                $nasabah->kode_nasabah = 'NSB' . $tahun . $bulan . $urutan;
            }
        });
    }
}