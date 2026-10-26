@extends('layouts.app')

@section('title', 'Data Nasabah')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4 overflow-hidden">
        <div class="card-body text-white position-relative" style="background: linear-gradient(135deg, #0d6efd, #6610f2);">
            <div class="position-absolute top-0 end-0 opacity-10" style="font-size: 120px; right: 20px; top: -20px;">
                <i class="fas fa-users"></i>
            </div>
            <div class="d-flex justify-content-between align-items-center position-relative">
                <div>
                    <h2 class="fw-bold mb-1">
                        <i class="fas fa-users me-2"></i>
                        Data Nasabah
                    </h2>
                    <p class="mb-0 opacity-75">
                        <i class="fas fa-database me-1"></i>
                        Total {{ $nasabah->count() }} nasabah terdaftar
                    </p>
                </div>
                <a href="{{ route('nasabah.create') }}" class="btn btn-light fw-semibold shadow-sm rounded-pill px-4 py-2 hover-scale">
                    <i class="fas fa-plus-circle me-2"></i>
                    Tambah Nasabah
                </a>
            </div>
        </div>
    </div>

    {{-- ALERT --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-3 alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- STATISTIK --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Total Nasabah</span>
                            <h4 class="fw-bold mb-0 text-primary">{{ $nasabah->count() }}</h4>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-users text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Aktif</span>
                            <h4 class="fw-bold mb-0 text-success">{{ $nasabah->where('status', 'aktif')->count() }}</h4>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-user-check text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Nonaktif</span>
                            <h4 class="fw-bold mb-0 text-danger">{{ $nasabah->where('status', 'nonaktif')->count() }}</h4>
                        </div>
                        <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-user-slash text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Total DC</span>
                            <h4 class="fw-bold mb-0 text-info">{{ $nasabah->groupBy('dc_id')->count() }}</h4>
                        </div>
                        <div class="bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-building text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CARD LIST --}}
    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-primary">
                <i class="fas fa-list me-2"></i>
                Daftar Nasabah
            </h5>
            <span class="badge bg-primary rounded-pill px-3 py-2">
                <i class="fas fa-file me-1"></i>
                {{ $nasabah->count() }} Data
            </span>
        </div>
        <div class="card-body">
            <div class="row">
                @forelse($nasabah as $row)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 hover-card">
                        {{-- Header Card --}}
                        <div class="card-header bg-primary text-white border-0 rounded-top-4 py-3 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="avatar-lg bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 45px; height: 45px;">
                                    <span class="fw-bold text-white fs-5">{{ substr($row->nama, 0, 1) }}</span>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $row->nama }}</h6>
                                    <small class="opacity-75">{{ $row->kode_nasabah }}</small>
                                </div>
                            </div>
                            <span class="badge {{ $row->status == 'aktif' ? 'bg-success' : 'bg-danger' }} px-3 py-2">
                                <i class="fas {{ $row->status == 'aktif' ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                {{ ucfirst($row->status) }}
                            </span>
                        </div>

                        {{-- Body Card --}}
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-6">
                                    <small class="text-muted d-block">
                                        <i class="fas fa-id-card me-1"></i> NIK
                                    </small>
                                    <span class="fw-semibold small">{{ $row->nik }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">
                                        <i class="fas fa-calendar-alt me-1"></i> Tanggal Lahir
                                    </small>
                                    <span class="fw-semibold small">{{ $row->tanggal_lahir_formatted }}</span>
                                    @if($row->umur)
                                    <span class="badge bg-secondary ms-1">{{ $row->umur }} th</span>
                                    @endif
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block">
                                        <i class="fas fa-home me-1"></i> Alamat
                                    </small>
                                    <span class="fw-semibold small">{{ Str::limit($row->alamat, 40) }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">
                                        <i class="fas fa-map-marker-alt me-1"></i> Wilayah
                                    </small>
                                    <span class="fw-semibold small">{{ $row->wilayah->nama_wilayah ?? '-' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">
                                        <i class="fas fa-building me-1"></i> DC
                                    </small>
                                    <span class="fw-semibold small">{{ $row->dc->nama_dc ?? '-' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">
                                        <i class="fas fa-phone me-1"></i> No HP
                                    </small>
                                    <span class="fw-semibold small">{{ $row->no_hp }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">
                                        <i class="fas fa-briefcase me-1"></i> Pekerjaan
                                    </small>
                                    <span class="fw-semibold small">{{ $row->pekerjaan ?? '-' }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Footer Card --}}
                        <div class="card-footer bg-white border-0 rounded-bottom-4 pt-0">
                            <div class="d-flex gap-2">
                                <a href="{{ route('nasabah.show', $row->id) }}" class="btn btn-sm btn-outline-primary rounded-pill flex-grow-1">
                                    <i class="fas fa-eye me-1"></i> Detail
                                </a>
                                <a href="{{ route('nasabah.edit', $row->id) }}" class="btn btn-sm btn-warning rounded-pill">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('nasabah.destroy', $row->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger rounded-pill" onclick="return confirm('Yakin ingin menghapus data nasabah ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-users-slash fa-4x text-muted mb-3 d-block opacity-25"></i>
                        <h5 class="fw-bold text-muted">Belum Ada Data Nasabah</h5>
                        <p class="text-muted">Silakan tambahkan data nasabah baru.</p>
                        <a href="{{ route('nasabah.create') }}" class="btn btn-primary rounded-pill px-4">
                            <i class="fas fa-plus-circle me-1"></i>
                            Tambah Nasabah
                        </a>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            <small class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                Menampilkan {{ $nasabah->count() }} data nasabah
                <i class="fas fa-clock ms-3 me-1"></i>
                Terakhir diperbarui: {{ now()->format('d/m/Y H:i:s') }}
            </small>
        </div>
    </div>

</div>

<style>
    .hover-scale {
        transition: transform 0.2s ease;
    }
    .hover-scale:hover {
        transform: scale(1.02);
    }
    .hover-card {
        transition: all 0.3s ease;
    }
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
    }
    .avatar-lg {
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
    .card {
        border: none !important;
    }
    .card-header {
        border-bottom: none !important;
    }
    .rounded-4 {
        border-radius: 1rem !important;
    }
    .rounded-top-4 {
        border-top-left-radius: 1rem !important;
        border-top-right-radius: 1rem !important;
    }
    .rounded-bottom-4 {
        border-bottom-left-radius: 1rem !important;
        border-bottom-right-radius: 1rem !important;
    }
    .badge {
        font-weight: 600;
    }
</style>

@endsection