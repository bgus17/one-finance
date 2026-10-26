@extends('layouts.app')

@section('title', 'Laporan Pinjaman')

@section('content')
<div class="container-fluid py-4">

    <div class="card border-0 shadow-lg rounded-4 mb-4">
        <div class="card-body text-white" style="background: linear-gradient(135deg,#0d6efd,#6610f2);">
            <h2 class="fw-bold mb-1">
                <i class="fas fa-file-alt me-2"></i>
                Laporan Pinjaman
            </h2>
            <p class="mb-0 opacity-75">Ringkasan seluruh data pinjaman</p>
        </div>
    </div>

    {{-- FILTER --}}
    <div class="card border-0 shadow rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('pinjaman.laporan') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" name="dari_tanggal" class="form-control" value="{{ request('dari_tanggal') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="sampai_tanggal" class="form-control" value="{{ request('sampai_tanggal') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- STATISTIK --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow rounded-4 bg-primary text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Total Pinjaman</h6>
                    <h4 class="fw-bold">Rp {{ number_format($totalPinjaman, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow rounded-4 bg-success text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Total Dicairkan</h6>
                    <h4 class="fw-bold">Rp {{ number_format($totalDicairkan, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow rounded-4 bg-warning text-dark">
                <div class="card-body">
                    <h6 class="text-dark-50">Total Tagihan</h6>
                    <h4 class="fw-bold">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow rounded-4 bg-danger text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Sisa Tagihan</h6>
                    <h4 class="fw-bold">Rp {{ number_format($totalSisaTagihan, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL --}}
    <div class="card border-0 shadow rounded-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nasabah</th>
                            <th>Paket</th>
                            <th>Jumlah Pinjam</th>
                            <th>Dicairkan</th>
                            <th>Tenor</th>
                            <th>Status</th>
                            <th>Tanggal Cair</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pinjaman as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->nasabah->nama ?? '-' }}</td>
                            <td>{{ $item->paketPinjaman->nama ?? '-' }}</td>
                            <td>Rp {{ number_format($item->jumlah_pinjam, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($item->nominal_dicairkan, 0, ',', '.') }}</td>
                            <td>{{ $item->tenor }} Minggu</td>
                            <td>
                                @if($item->status == 'aktif')
                                    <span class="badge bg-primary">Aktif</span>
                                @elseif($item->status == 'lunas')
                                    <span class="badge bg-success">Lunas</span>
                                @elseif($item->status == 'ditolak')
                                    <span class="badge bg-danger">Ditolak</span>
                                @else
                                    <span class="badge bg-warning">Menunggu</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_cair)->format('d/m/Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                                Tidak ada data pinjaman
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection