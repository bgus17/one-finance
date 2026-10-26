@extends('layouts.app')

@section('title', 'Dashboard Laporan')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4">
        <div class="card-body text-white" style="background: linear-gradient(135deg, #0d6efd, #6610f2);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1">
                        <i class="fas fa-chart-pie me-2"></i>
                        Dashboard Laporan
                    </h2>
                    <p class="mb-0 opacity-75">
                        <i class="fas fa-calendar-alt me-1"></i>
                        Ringkasan seluruh data keuangan koperasi
                    </p>
                </div>
                <div>
                    <span class="badge bg-light text-dark px-3 py-2">
                        <i class="fas fa-print me-1"></i>
                        Periode: {{ date('F Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- STATISTIK CARD --}}
    <div class="row g-3 mb-4">

        {{-- Total Kas Masuk --}}
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Kas Masuk</span>
                            <h4 class="fw-bold text-success mb-0">
                                Rp {{ number_format($totalKasMasuk ?? 0, 0, ',', '.') }}
                            </h4>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-arrow-down text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Kas Keluar --}}
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Kas Keluar</span>
                            <h4 class="fw-bold text-danger mb-0">
                                Rp {{ number_format($totalKasKeluar ?? 0, 0, ',', '.') }}
                            </h4>
                        </div>
                        <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-arrow-up text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Saldo Akhir --}}
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Saldo Akhir</span>
                            <h4 class="fw-bold text-primary mb-0">
                                Rp {{ number_format($saldoAkhir ?? 0, 0, ',', '.') }}
                            </h4>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-wallet text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Saldo Modal --}}
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Saldo Modal</span>
                            <h4 class="fw-bold text-info mb-0">
                                Rp {{ number_format($saldoModal ?? 0, 0, ',', '.') }}
                            </h4>
                        </div>
                        <div class="bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-building text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- STATISTIK PINJAMAN --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Total Pinjaman</span>
                            <h4 class="fw-bold mb-0">{{ $totalPinjaman ?? 0 }}</h4>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-list text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Pinjaman Aktif</span>
                            <h4 class="fw-bold text-primary mb-0">{{ $pinjamanAktif ?? 0 }}</h4>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-play-circle text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Pinjaman Lunas</span>
                            <h4 class="fw-bold text-success mb-0">{{ $pinjamanLunas ?? 0 }}</h4>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-check-circle text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Pinjaman Ditolak</span>
                            <h4 class="fw-bold text-danger mb-0">{{ $pinjamanDitolak ?? 0 }}</h4>
                        </div>
                        <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-times-circle text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MENU LAPORAN --}}
    <div class="row g-4">

        <div class="col-md-3">
            <a href="{{ route('laporan.kas') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 hover-card">
                    <div class="card-body text-center py-4">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-4 d-inline-block mb-3">
                            <i class="fas fa-money-bill-wave fa-3x text-primary"></i>
                        </div>
                        <h5 class="fw-bold">Laporan Kas</h5>
                        <p class="text-muted small">Kas Masuk & Keluar</p>
                        <span class="badge bg-primary rounded-pill px-3">Buka →</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="{{ route('laporan.pinjaman') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 hover-card">
                    <div class="card-body text-center py-4">
                        <div class="bg-success bg-opacity-10 rounded-circle p-4 d-inline-block mb-3">
                            <i class="fas fa-hand-holding-usd fa-3x text-success"></i>
                        </div>
                        <h5 class="fw-bold">Laporan Pinjaman</h5>
                        <p class="text-muted small">Data Pinjaman</p>
                        <span class="badge bg-success rounded-pill px-3">Buka →</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="{{ route('laporan.dkh') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 hover-card">
                    <div class="card-body text-center py-4">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-4 d-inline-block mb-3">
                            <i class="fas fa-file-invoice fa-3x text-warning"></i>
                        </div>
                        <h5 class="fw-bold">Laporan DKH</h5>
                        <p class="text-muted small">Tagihan per DC</p>
                        <span class="badge bg-warning rounded-pill px-3">Buka →</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="{{ route('laporan.labarugi') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 hover-card">
                    <div class="card-body text-center py-4">
                        <div class="bg-danger bg-opacity-10 rounded-circle p-4 d-inline-block mb-3">
                            <i class="fas fa-chart-line fa-3x text-danger"></i>
                        </div>
                        <h5 class="fw-bold">Laporan Laba Rugi</h5>
                        <p class="text-muted small">Pendapatan & Biaya</p>
                        <span class="badge bg-danger rounded-pill px-3">Buka →</span>
                    </div>
                </div>
            </a>
        </div>

    </div>

</div>

<style>
    .hover-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .hover-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.15) !important;
    }
    .rounded-4 {
        border-radius: 1rem !important;
    }
    .card {
        border-radius: 1rem !important;
        overflow: hidden;
    }
</style>
@endsection