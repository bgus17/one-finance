@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">
{{-- Header --}}
<div class="card border-0 shadow-lg rounded-4 mb-4">

    <div class="card-body text-white"
         style="background: linear-gradient(135deg,#0d6efd,#6610f2); border-radius:1rem;">

        <div class="d-flex justify-content-between align-items-center">

            <div>

                <h2 class="fw-bold mb-1">
                    <i class="fas fa-file-invoice-dollar me-2"></i>
                    Detail Pinjaman Nasabah
                </h2>

                <p class="mb-0 opacity-75">
                    Informasi lengkap pinjaman dan riwayat pembayaran
                </p>

            </div>

            <a href="{{ route('pembayaran.index') }}"
               class="btn btn-light rounded-pill px-4">

                <i class="fas fa-arrow-left me-1"></i>
                Kembali

            </a>

        </div>

    </div>

</div>

{{-- Data Nasabah --}}
<div class="card border-0 shadow-lg rounded-4 mb-4">

    <div class="card-header bg-primary text-white border-0">

        <i class="fas fa-user me-2"></i>
        Data Nasabah

    </div>

    <div class="card-body">

        <div class="row g-4">

            <div class="col-md-4">

                <small class="text-muted">Nama Nasabah</small>

                <h6 class="fw-bold mb-0">
                    {{ $pinjaman->nasabah->nama }}
                </h6>

            </div>

            <div class="col-md-4">

                <small class="text-muted">Kode Nasabah</small>

                <h6 class="fw-bold mb-0">
                    {{ $pinjaman->nasabah->kode_nasabah }}
                </h6>

            </div>

            <div class="col-md-4">

                <small class="text-muted">DC</small>

                <h6 class="fw-bold mb-0">
                    {{ $pinjaman->nasabah->dc->nama_dc ?? '-' }}
                </h6>

            </div>

        </div>

    </div>

</div>

{{-- Ringkasan Pinjaman --}}
<div class="row mb-4">

    <div class="col-md-3">

        <div class="card border-0 shadow-sm bg-primary text-white">

            <div class="card-body">

                <small>Pinjaman Awal</small>

                <h5 class="fw-bold mb-0">
                    Rp {{ number_format($pinjaman->jumlah_pinjam,0,',','.') }}
                </h5>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card border-0 shadow-sm bg-success text-white">

            <div class="card-body">

                <small>Total Tagihan</small>

                <h5 class="fw-bold mb-0">
                    Rp {{ number_format($pinjaman->total_pinjaman,0,',','.') }}
                </h5>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card border-0 shadow-sm bg-danger text-white">

            <div class="card-body">

                <small>Sisa Tagihan</small>

                <h5 class="fw-bold mb-0">
                    Rp {{ number_format($pinjaman->sisa_tagihan,0,',','.') }}
                </h5>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card border-0 shadow-sm bg-dark text-white">

            <div class="card-body">

                <small>Status Pinjaman</small>

                <h5 class="fw-bold mb-0">

                    @if($pinjaman->status == 'aktif')
                        Aktif
                    @elseif($pinjaman->status == 'lunas')
                        Lunas
                    @else
                        Top Up
                    @endif

                </h5>

            </div>

        </div>

    </div>

</div>

{{-- Status Top Up --}}
@php
    $jumlahLunas = $pinjaman->angsuran->where('status','lunas')->count();
    $minimalTopup = ceil($pinjaman->tenor / 2);
    $persen = min(100, ($jumlahLunas / $minimalTopup) * 100);
@endphp

<div class="card border-0 shadow-lg rounded-4 mb-4">

    <div class="card-header bg-warning border-0">

        <i class="fas fa-chart-line me-2"></i>
        Status Top Up

    </div>

    <div class="card-body">

        <div class="mb-3">

            <div class="d-flex justify-content-between">

                <span>Progress Kelayakan Top Up</span>

                <strong>{{ $jumlahLunas }}/{{ $minimalTopup }}</strong>

            </div>

            <div class="progress mt-2" style="height:10px;">

                <div class="progress-bar bg-success"
                     style="width: {{ $persen }}%">
                </div>

            </div>

        </div>

        @if($jumlahLunas >= $minimalTopup)

            <div class="alert alert-success mb-0">

                <i class="fas fa-check-circle me-2"></i>

                Nasabah memenuhi syarat untuk melakukan
                <strong>TOP UP</strong>.

            </div>

        @else

            <div class="alert alert-danger mb-0">

                <i class="fas fa-times-circle me-2"></i>

                Nasabah belum memenuhi syarat TOP UP.

            </div>

        @endif

    </div>

</div>

{{-- Riwayat Angsuran --}}
<div class="card border-0 shadow-lg rounded-4">

    <div class="card-header bg-dark text-white border-0">

        <i class="fas fa-history me-2"></i>
        Riwayat Angsuran

    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-light">

                    <tr>
                        <th>Minggu</th>
                        <th>Jatuh Tempo</th>
                        <th>Tagihan</th>
                        <th>Terbayar</th>
                        <th>Sisa</th>
                        <th>Status</th>
                    </tr>

                </thead>

                <tbody>

                @foreach($pinjaman->angsuran as $angsuran)

                    <tr>

                        <td>
                            <span class="badge bg-primary">
                                Minggu ke-{{ $angsuran->minggu_ke }}
                            </span>
                        </td>

                        <td>
                            {{ \Carbon\Carbon::parse($angsuran->tanggal_jatuh_tempo)->format('d M Y') }}
                        </td>

                        <td>
                            Rp {{ number_format($angsuran->jumlah_tagihan,0,',','.') }}
                        </td>

                        <td class="text-success fw-bold">
                            Rp {{ number_format($angsuran->jumlah_bayar,0,',','.') }}
                        </td>

                        <td class="text-danger fw-bold">
                            Rp {{ number_format($angsuran->sisa_tagihan,0,',','.') }}
                        </td>

                        <td>

                            @if($angsuran->status == 'lunas')

                                <span class="badge bg-success rounded-pill">

                                    <i class="fas fa-check-circle me-1"></i>
                                    Lunas

                                </span>

                            @else

                                <span class="badge bg-danger rounded-pill">

                                    <i class="fas fa-clock me-1"></i>
                                    Belum Bayar

                                </span>

                            @endif

                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        </div>

    </div>

</div>

</div>

@endsection
