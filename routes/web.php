<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DcController;
use App\Http\Controllers\DesaController;
use App\Http\Controllers\DkhController;
use App\Http\Controllers\KasKeluarController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LaporanKeuanganController;
use App\Http\Controllers\ModalController;
use App\Http\Controllers\NasabahController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PinjamanController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\DcImportController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ============================================
// AUTH ROUTES
// ============================================
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 🔥 ROUTE LUPA PASSWORD (tanpa login)
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('forgot-password');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password.post');

// 🔥 ROUTE GANTI SANDI (harus login)
Route::middleware(['auth'])->group(function () {
    Route::get('/change-password', [AuthController::class, 'showChangePassword'])->name('change-password');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('change-password.post');
});

/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // ============================================
    // DASHBOARD
    // ============================================
    Route::get('/dashboard', function () {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('kepala.dashboard');
    })->name('dashboard');

    Route::get('/admin/dashboard', function () {
        if (Auth::user()->role != 'admin') {
            abort(403, 'Unauthorized - Anda tidak memiliki akses!');
        }
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/kepala/dashboard', function () {
        if (Auth::user()->role != 'kepala') {
            abort(403, 'Unauthorized - Anda tidak memiliki akses!');
        }
        return view('kepala.dashboard');
    })->name('kepala.dashboard');

    // ============================================
    // 🔥 MASTER DATA WILAYAH
    // ============================================
    Route::prefix('wilayah')->name('wilayah.')->group(function () {
        Route::get('/', [WilayahController::class, 'index'])->name('index');
        Route::get('/create', [WilayahController::class, 'create'])->name('create');
        Route::post('/store', [WilayahController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [WilayahController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [WilayahController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [WilayahController::class, 'destroy'])->name('destroy');
    });

    // ============================================
    // 🔥 MASTER DATA DESA
    // ============================================
    Route::prefix('desa')->name('desa.')->group(function () {
        Route::get('/', [DesaController::class, 'index'])->name('index');
        Route::get('/create', [DesaController::class, 'create'])->name('create');
        Route::post('/store', [DesaController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [DesaController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [DesaController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [DesaController::class, 'destroy'])->name('destroy');
        Route::get('/by-wilayah/{wilayahId}', [DesaController::class, 'getByWilayah'])->name('by-wilayah');
    });

    // ============================================
    // 🔥 MASTER DATA DC
    // ============================================
    Route::prefix('dc')->name('dc.')->group(function () {
        Route::get('/', [DcController::class, 'index'])->name('index');
        Route::get('/create', [DcController::class, 'create'])->name('create');
        Route::post('/store', [DcController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [DcController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [DcController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [DcController::class, 'destroy'])->name('destroy');
    });

    // ============================================
    // 🔥 MASTER DATA NASABAH
    // ============================================
    Route::prefix('nasabah')->name('nasabah.')->group(function () {
        Route::get('/', [NasabahController::class, 'index'])->name('index');
        Route::get('/create', [NasabahController::class, 'create'])->name('create');
        Route::post('/store', [NasabahController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [NasabahController::class, 'edit'])->name('edit');
        Route::get('/show/{id}', [NasabahController::class, 'show'])->name('show');
        Route::put('/update/{id}', [NasabahController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [NasabahController::class, 'destroy'])->name('destroy');
    });

    // ============================================
    // 🔥 MODAL (KEPALA ONLY - PAKAI PENGECEKAN DI CONTROLLER)
    // ============================================
    Route::prefix('modal')->name('modal.')->group(function () {
        Route::get('/', [ModalController::class, 'index'])->name('index');
        Route::get('/create', [ModalController::class, 'create'])->name('create');
        Route::post('/store', [ModalController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [ModalController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [ModalController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [ModalController::class, 'destroy'])->name('destroy');
    });

    // ============================================
    // 🔥 KAS KELUAR (KEPALA ONLY)
    // ============================================
    Route::prefix('kas-keluar')->name('kas-keluar.')->group(function () {
        Route::get('/', [KasKeluarController::class, 'index'])->name('index');
        Route::get('/create', [KasKeluarController::class, 'create'])->name('create');
        Route::post('/store', [KasKeluarController::class, 'store'])->name('store');
        Route::delete('/{id}', [KasKeluarController::class, 'destroy'])->name('destroy');
    });

    // ============================================
    // 🔥 PINJAMAN
    // ============================================
    Route::prefix('pinjaman')->name('pinjaman.')->group(function () {
        Route::get('/', [PinjamanController::class, 'index'])->name('index');
        Route::get('/create', [PinjamanController::class, 'create'])->name('create');
        Route::post('/store', [PinjamanController::class, 'store'])->name('store');
        Route::get('/show/{id}', [PinjamanController::class, 'show'])->name('show');
        Route::get('/edit/{id}', [PinjamanController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [PinjamanController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [PinjamanController::class, 'destroy'])->name('destroy');
        Route::get('/cetak-berkas/{id}', [PinjamanController::class, 'generateBerkasPencairan'])->name('cetak-berkas');
        Route::get('/cetak-kontrak/{id}', [PinjamanController::class, 'generateSuratKontrak'])->name('cetak-kontrak');
        Route::get('/cetak-otomatis/{id}', [PinjamanController::class, 'generateBerkasOtomatis'])->name('cetak-otomatis');
        Route::get('/laporan', [PinjamanController::class, 'laporan'])->name('laporan');
    });

    // ============================================
    // 🔥 PEMBAYARAN
    // ============================================
    Route::prefix('pembayaran')->name('pembayaran.')->group(function () {
        Route::get('/', [PembayaranController::class, 'index'])->name('index');
        Route::get('/riwayat', [PembayaranController::class, 'riwayat'])->name('riwayat');
        Route::get('/export', [PembayaranController::class, 'export'])->name('export');
        Route::get('/create/{dkhDetailId}', [PembayaranController::class, 'createForm'])->name('create');
        Route::post('/store', [PembayaranController::class, 'store'])->name('store');
        Route::post('/bayar-langsung/{dkhDetailId}', [PembayaranController::class, 'bayarLangsung'])->name('bayar-langsung');
        Route::get('/bayar-multiple/{pinjamanId}', [PembayaranController::class, 'bayarMultipleForm'])->name('bayar-multiple.form');
        Route::post('/bayar-multiple', [PembayaranController::class, 'bayarMultiple'])->name('bayar-multiple');
        Route::get('/{id}', [PembayaranController::class, 'detail'])->name('detail');
        Route::get('/{id}/edit', [PembayaranController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PembayaranController::class, 'update'])->name('update');
        Route::delete('/{id}', [PembayaranController::class, 'destroy'])->name('destroy');
    });

    // ============================================
    // 🔥 DKH
    // ============================================
    Route::prefix('dkh')->name('dkh.')->group(function () {
        Route::get('/', [DkhController::class, 'index'])->name('index');
        Route::post('/generate', [DkhController::class, 'generate'])->name('generate');
        Route::get('/{id}', [DkhController::class, 'show'])->name('show');
        Route::post('/destroy/{id}', [DkhController::class, 'destroy'])->name('destroy');
        Route::get('/cetak/{id}', [DkhController::class, 'cetak'])->name('cetak');
        Route::get('/cetak-per-tanggal', [DkhController::class, 'cetakPerTanggal'])->name('cetak-per-tanggal');
    });

    // ============================================
    // 🔥 LAPORAN
    // ============================================
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        Route::get('/kas', [LaporanController::class, 'kas'])->name('kas');
        Route::get('/cetak-kas', [LaporanController::class, 'cetakKas'])->name('cetak-kas');
        Route::get('/pinjaman', [LaporanController::class, 'pinjaman'])->name('pinjaman');
        Route::get('/cetak-pinjaman', [LaporanController::class, 'cetakPinjaman'])->name('cetak-pinjaman');
        Route::get('/dkh', [LaporanController::class, 'dkh'])->name('dkh');
        Route::get('/cetak-dkh', [LaporanController::class, 'cetakDkh'])->name('cetak-dkh');
        Route::get('/labarugi', [LaporanController::class, 'labarugi'])->name('labarugi');
        Route::get('/cetak-labarugi', [LaporanController::class, 'cetakLabarugi'])->name('cetak-labarugi');
    });

    // ============================================
    // 🔥 LAPORAN KEUANGAN
    // ============================================
    Route::prefix('laporan-keuangan')->name('laporan-keuangan.')->group(function () {
        Route::get('/', [LaporanKeuanganController::class, 'index'])->name('index');
        Route::get('/cetak', [LaporanKeuanganController::class, 'cetak'])->name('cetak');
    });

    // ============================================
    // 🔥 BACKUP DATABASE
    // ============================================
    Route::prefix('backup')->name('backup.')->group(function () {
        Route::get('/', [BackupController::class, 'index'])->name('index');
        Route::post('/create', [BackupController::class, 'create'])->name('create');
        Route::get('/download/{filename}', [BackupController::class, 'download'])->name('download');
        Route::delete('/destroy/{filename}', [BackupController::class, 'destroy'])->name('destroy');
        Route::post('/restore/{filename}', [BackupController::class, 'restore'])->name('restore');
    });

    // ============================================
    // 🔥 AJAX ROUTES (UNTUK DROPDOWN DINAMIS)
    // ============================================
    Route::get('/get-desa/{wilayahId}', function ($wilayahId) {
        return App\Models\Desa::where('wilayah_id', $wilayahId)
            ->where('status', 'aktif')
            ->select('id', 'nama_desa')
            ->get();
    })->name('get-desa');
});

// ============================================
// IMPORT DC ROUTES
// ============================================
Route::middleware(['auth'])->prefix('import')->name('import.')->group(function () {
    Route::get('/dc', [DcImportController::class, 'index'])->name('dc');
    Route::post('/dc', [DcImportController::class, 'import'])->name('dc.post');
    Route::get('/dc/template', [DcImportController::class, 'downloadTemplate'])->name('dc.template');
});

Route::middleware(['auth'])->group(function () {
    // Route Pengaturan Profil & Ganti Password
    Route::get('/pengaturan', [ProfileController::class, 'settings'])->name('profile.settings');
    Route::post('/pengaturan/ganti-password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
});