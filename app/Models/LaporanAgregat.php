<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanAgregat extends Model
{
    use HasFactory;

    protected $fillable = [
        'dc_id',
        'periode',
        'periode_start',
        'periode_end',
        
        // DATA KEUANGAN
        'total_kas_masuk',
        'total_kas_keluar',
        'total_admin',
        'total_pencairan',
        'total_pembayaran',
        
        // DATA PINJAMAN
        'total_nasabah',
        'total_pinjaman',
        'total_pinjaman_aktif',
        'total_pinjaman_lunas',
        'total_pinjaman_macet',
        
        // DATA SALDO
        'total_saldo_pinjaman',
        'sisa_pokok',
        'sisa_bunga',
        'sisa_bunga_pokok',
        
        // DATA MACET
        'pokok_macet',
        'margin_macet',
        'total_macet',
        
        // DATA REAL
        'real_margin',
        'real_sisa_pokok',
        'real_spp',
        
        // DATA PENDAPATAN & BIAYA
        'pendapatan_bunga',
        'pendapatan_admin',
        'pendapatan_denda',
        'total_pendapatan',
        'total_biaya',
        'laba_bersih',
        
        // DATA LAINNYA
        'bunga_per_minggu',
        'saldo_kas',
        'saldo_modal',
    ];

    protected $casts = [
        'periode_start' => 'date',
        'periode_end' => 'date',
        'total_kas_masuk' => 'decimal:2',
        'total_kas_keluar' => 'decimal:2',
        'total_admin' => 'decimal:2',
        'total_pencairan' => 'decimal:2',
        'total_pembayaran' => 'decimal:2',
        'total_saldo_pinjaman' => 'decimal:2',
        'sisa_pokok' => 'decimal:2',
        'sisa_bunga' => 'decimal:2',
        'sisa_bunga_pokok' => 'decimal:2',
        'pokok_macet' => 'decimal:2',
        'margin_macet' => 'decimal:2',
        'total_macet' => 'decimal:2',
        'real_margin' => 'decimal:2',
        'real_sisa_pokok' => 'decimal:2',
        'real_spp' => 'decimal:2',
        'pendapatan_bunga' => 'decimal:2',
        'pendapatan_admin' => 'decimal:2',
        'pendapatan_denda' => 'decimal:2',
        'total_pendapatan' => 'decimal:2',
        'total_biaya' => 'decimal:2',
        'laba_bersih' => 'decimal:2',
        'bunga_per_minggu' => 'decimal:2',
        'saldo_kas' => 'decimal:2',
        'saldo_modal' => 'decimal:2',
    ];

    // 🔥 RELASI
    public function dc()
    {
        return $this->belongsTo(Dc::class);
    }
}