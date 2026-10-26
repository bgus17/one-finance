@extends('layouts.app')

@section('title', 'Detail Pinjaman')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4">
        <div class="card-body text-white" style="background: linear-gradient(135deg, #0d6efd, #6610f2);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1">
                        <i class="fas fa-file-invoice me-2"></i>
                        Detail Pinjaman
                    </h2>
                    <p class="mb-0 opacity-75">
                        <i class="fas fa-user me-1"></i>
                        {{ $pinjaman->nasabah->nama ?? '-' }}
                        <span class="mx-2">|</span>
                        <i class="fas fa-id-card me-1"></i>
                        {{ $pinjaman->nasabah->kode_nasabah ?? '-' }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('pinjaman.index') }}" class="btn btn-light">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
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

    {{-- MAIN CONTENT --}}
    <div class="row">
        {{-- KOLOM KIRI: INFORMASI PINJAMAN --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold text-primary mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informasi Pinjaman
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="150" class="fw-bold text-muted">Jumlah Pinjaman</td>
                            <td>: <strong>Rp {{ number_format($pinjaman->jumlah_pinjam, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Biaya Administrasi</td>
                            <td>: Rp {{ number_format($pinjaman->nominal_adm, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Nominal Dicairkan</td>
                            <td>: <strong class="text-success">Rp {{ number_format($pinjaman->nominal_dicairkan, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Total Tagihan</td>
                            <td>: Rp {{ number_format($pinjaman->total_pinjaman, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Sisa Tagihan</td>
                            <td>: <strong class="text-danger">Rp {{ number_format($pinjaman->sisa_tagihan, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Tenor</td>
                            <td>: {{ $pinjaman->tenor }} Minggu</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Angsuran/Minggu</td>
                            <td>: Rp {{ number_format($pinjaman->angsuran_per_minggu, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Tanggal Pengajuan</td>
                            <td>: {{ \Carbon\Carbon::parse($pinjaman->tanggal_pengajuan)->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Tanggal Cair</td>
                            <td>: {{ \Carbon\Carbon::parse($pinjaman->tanggal_cair)->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Jatuh Tempo</td>
                            <td>: {{ \Carbon\Carbon::parse($pinjaman->jatuh_tempo)->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Status</td>
                            <td>
                                @if($pinjaman->status == 'aktif')
                                    <span class="badge bg-primary rounded-pill px-3 py-2">
                                        <i class="fas fa-play-circle me-1"></i> Aktif
                                    </span>
                                @elseif($pinjaman->status == 'lunas')
                                    <span class="badge bg-success rounded-pill px-3 py-2">
                                        <i class="fas fa-check-circle me-1"></i> Lunas
                                    </span>
                                @elseif($pinjaman->status == 'ditolak')
                                    <span class="badge bg-danger rounded-pill px-3 py-2">
                                        <i class="fas fa-times-circle me-1"></i> Ditolak
                                    </span>
                                @else
                                    <span class="badge bg-warning rounded-pill px-3 py-2">
                                        <i class="fas fa-clock me-1"></i> Menunggu
                                    </span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: INFORMASI NASABAH --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold text-primary mb-0">
                        <i class="fas fa-user me-2"></i>
                        Informasi Nasabah
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="150" class="fw-bold text-muted">Nama</td>
                            <td>: <strong>{{ $pinjaman->nasabah->nama ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Kode Nasabah</td>
                            <td>: {{ $pinjaman->nasabah->kode_nasabah ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">NIK</td>
                            <td>: {{ $pinjaman->nasabah->nik ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Tempat, Tgl Lahir</td>
                            <td>: {{ $pinjaman->nasabah->tempat_lahir ?? '-' }}, {{ $pinjaman->nasabah->tanggal_lahir ? \Carbon\Carbon::parse($pinjaman->nasabah->tanggal_lahir)->format('d/m/Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Pekerjaan</td>
                            <td>: {{ $pinjaman->nasabah->pekerjaan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Alamat</td>
                            <td>: {{ $pinjaman->nasabah->alamat ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">No HP</td>
                            <td>: {{ $pinjaman->nasabah->no_hp ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">DC</td>
                            <td>: {{ $pinjaman->nasabah->dc->nama_dc ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Wilayah</td>
                            <td>: {{ $pinjaman->nasabah->wilayah->nama_wilayah ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Status</td>
                            <td>
                                @if($pinjaman->nasabah->status == 'aktif')
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- DAFTAR ANGSURAN --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold text-primary mb-0">
                        <i class="fas fa-list me-2"></i>
                        Daftar Angsuran
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Minggu Ke</th>
                                    <th>Jatuh Tempo</th>
                                    <th class="text-end">Tagihan</th>
                                    <th class="text-end">Bayar</th>
                                    <th class="text-end">Sisa</th>
                                    <th>Status</th>
                                    <th>Tanggal Bayar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pinjaman->angsuran as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>Ke-{{ $item->minggu_ke }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->format('d/m/Y') }}</td>
                                    <td class="text-end">Rp {{ number_format($item->jumlah_tagihan, 0, ',', '.') }}</td>
                                    <td class="text-end text-success">Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</td>
                                    <td class="text-end text-danger">Rp {{ number_format($item->sisa_tagihan, 0, ',', '.') }}</td>
                                    <td>
                                        @if($item->status == 'lunas')
                                            <span class="badge bg-success">Lunas</span>
                                        @else
                                            <span class="badge bg-danger">Belum</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->tanggal_bayar)
                                            {{ \Carbon\Carbon::parse($item->tanggal_bayar)->format('d/m/Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <span class="text-muted">Belum ada angsuran</span>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="3" class="text-end">TOTAL</td>
                                    <td class="text-end">Rp {{ number_format($pinjaman->angsuran->sum('jumlah_tagihan'), 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($pinjaman->angsuran->sum('jumlah_bayar'), 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($pinjaman->angsuran->sum('sisa_tagihan'), 0, ',', '.') }}</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TOMBOL AKSI --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('pinjaman.edit', $pinjaman->id) }}" 
                   class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i> Edit Pinjaman
                </a>
                
                <a href="{{ route('pinjaman.cetak-kontrak', $pinjaman->id) }}" 
                   target="_blank" 
                   class="btn btn-success">
                    <i class="fas fa-file-contract me-1"></i> Surat Kontrak
                </a>
                
                <a href="{{ route('pinjaman.cetak-berkas', $pinjaman->id) }}" 
                   target="_blank" 
                   class="btn btn-primary">
                    <i class="fas fa-file-pdf me-1"></i> Berkas Pencairan
                </a>
                
                <form action="{{ route('pinjaman.destroy', $pinjaman->id) }}" 
                      method="POST" 
                      class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="btn btn-danger"
                            onclick="return confirm('Yakin ingin menghapus pinjaman ini?')">
                        <i class="fas fa-trash me-1"></i> Hapus Pinjaman
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

<style>
    .card {
        border-radius: 1rem !important;
        overflow: hidden;
    }
    .table > :not(caption) > * > * {
        padding: 10px 12px;
        vertical-align: middle;
    }
</style>

@endsection