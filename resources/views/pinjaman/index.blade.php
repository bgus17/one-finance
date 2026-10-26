@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg mb-4 rounded-4 overflow-hidden">
        <div class="card-body text-white position-relative" style="background: linear-gradient(135deg, #0d6efd, #6610f2);">
            <div class="position-absolute top-0 end-0 opacity-10" style="font-size: 120px; right: 20px; top: -20px;">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
            <div class="d-flex justify-content-between align-items-center position-relative">
                <div>
                    <h2 class="fw-bold mb-1">
                        <i class="fas fa-hand-holding-usd me-2"></i>
                        Data Pinjaman
                    </h2>
                    <p class="mb-0 opacity-75">
                        <i class="fas fa-database me-1"></i>
                        Total {{ $pinjaman->count() }} pinjaman terdaftar
                    </p>
                </div>
                <a href="{{ route('pinjaman.create') }}"
                   class="btn btn-light fw-semibold shadow-sm rounded-pill px-4 py-2 hover-scale">
                    <i class="fas fa-plus-circle me-2"></i>
                    Tambah Pinjaman
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

    {{-- STATISTIK CARD --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Total Pinjaman</span>
                            <h4 class="fw-bold mb-0">{{ $pinjaman->count() }}</h4>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-list text-primary"></i>
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
                            <h4 class="fw-bold mb-0 text-primary">{{ $pinjaman->where('status', 'aktif')->count() }}</h4>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-play-circle text-primary"></i>
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
                            <span class="text-muted small">Lunas</span>
                            <h4 class="fw-bold mb-0 text-success">{{ $pinjaman->where('status', 'lunas')->count() }}</h4>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-check-circle text-success"></i>
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
                            <span class="text-muted small">Macet</span>
                            <h4 class="fw-bold mb-0 text-danger">{{ $pinjaman->where('status', 'macet')->count() }}</h4>
                        </div>
                        <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-times-circle text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLE CARD --}}
    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-primary">
                <i class="fas fa-list me-2"></i>
                Daftar Pinjaman
            </h5>
            <span class="badge bg-primary rounded-pill px-3 py-2">
                <i class="fas fa-file me-1"></i>
                {{ $pinjaman->count() }} Data
            </span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" width="50">#</th>
                            <th>Kode</th>
                            <th>Nasabah</th>
                            <th class="text-end">Pinjaman</th>
                            <th class="text-end">Sisa Tagihan</th>
                            <th class="text-center">Tenor</th>
                            <th>Tgl Cair</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pinjaman as $item)
                        <tr>
                            <td class="ps-3">
                                <span class="fw-bold text-primary">{{ $loop->iteration }}</span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-3 py-2">
                                    <i class="fas fa-id-card me-1"></i>
                                    {{ $item->nasabah->kode_nasabah ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                        <span class="fw-bold text-primary">{{ substr($item->nasabah->nama ?? 'N', 0, 1) }}</span>
                                    </div>
                                    <span class="fw-semibold">{{ $item->nasabah->nama ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="text-end fw-bold text-success">
                                Rp {{ number_format($item->total_pinjaman,0,',','.') }}
                            </td>
                            <td class="text-end fw-bold {{ $item->sisa_tagihan > 0 ? 'text-danger' : 'text-success' }}">
                                Rp {{ number_format($item->sisa_tagihan,0,',','.') }}
                            </td>
                            <td class="text-center">
                                {{ $item->tenor }} Minggu
                            </td>
                            <td>
                                <span class="small">
                                    <i class="fas fa-calendar-alt text-muted me-1"></i>
                                    {{ \Carbon\Carbon::parse($item->tanggal_cair)->format('d/m/Y') }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($item->status == 'aktif')
                                    <span class="badge bg-primary rounded-pill px-3 py-2">
                                        <i class="fas fa-play-circle me-1"></i>
                                        Aktif
                                    </span>
                                @elseif($item->status == 'lunas')
                                    <span class="badge bg-success rounded-pill px-3 py-2">
                                        <i class="fas fa-check-circle me-1"></i>
                                        Lunas
                                    </span>
                                @elseif($item->status == 'ditolak')
                                    <span class="badge bg-danger rounded-pill px-3 py-2">
                                        <i class="fas fa-times-circle me-1"></i>
                                        Ditolak
                                    </span>
                                @else
                                    <span class="badge bg-warning rounded-pill px-3 py-2">
                                        <i class="fas fa-clock me-1"></i>
                                        Menunggu
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    {{-- Tombol Detail --}}
                                    <a href="{{ route('pinjaman.show', $item->id) }}" 
                                       class="btn btn-sm btn-outline-primary rounded-3" 
                                       data-bs-toggle="tooltip" 
                                       title="Detail Pinjaman">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    {{-- Tombol Edit --}}
                                    <a href="{{ route('pinjaman.edit', $item->id) }}" 
                                       class="btn btn-sm btn-outline-warning rounded-3" 
                                       data-bs-toggle="tooltip" 
                                       title="Edit Pinjaman">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    {{-- Tombol Cetak Surat Kontrak --}}
                                    <a href="{{ route('pinjaman.cetak-kontrak', $item->id) }}" 
                                       target="_blank" 
                                       class="btn btn-sm btn-outline-success rounded-3" 
                                       data-bs-toggle="tooltip" 
                                       title="Cetak Surat Kontrak">
                                        <i class="fas fa-file-contract"></i>
                                    </a>

                                    {{-- Tombol Hapus --}}
                                    <form action="{{ route('pinjaman.destroy', $item->id) }}" 
                                          method="POST" 
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                onclick="return confirm('Yakin ingin menghapus data pinjaman ini?')" 
                                                class="btn btn-sm btn-outline-danger rounded-3" 
                                                data-bs-toggle="tooltip" 
                                                title="Hapus Pinjaman">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-folder-open fa-4x mb-3 d-block opacity-25"></i>
                                    <h5 class="fw-bold">Belum Ada Data Pinjaman</h5>
                                    <p class="mb-0">Silakan tambahkan data pinjaman baru.</p>
                                    <a href="{{ route('pinjaman.create') }}" class="btn btn-primary mt-3 rounded-pill px-4">
                                        <i class="fas fa-plus-circle me-1"></i>
                                        Tambah Pinjaman
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Footer Table --}}
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Menampilkan {{ $pinjaman->count() }} data
                </small>
                <div>
                    <a href="{{ route('pinjaman.laporan') }}" class="btn btn-sm btn-outline-primary rounded-3">
                        <i class="fas fa-file-alt me-1"></i> Laporan
                    </a>
                </div>
            </div>
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
    .avatar-sm {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
    .table > :not(caption) > * > * {
        padding: 12px 8px;
    }
    .btn-group-sm > .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    .btn-group {
        gap: 4px;
    }
    .btn-group > .btn {
        border-radius: 0.375rem !important;
        border: 1px solid #e0e0e0;
    }
    .btn-group > .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
</style>

@endsection

@push('scripts')
<script>
    // Inisialisasi tooltip
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush