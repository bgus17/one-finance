@extends('layouts.app')

@section('title', 'Kas Keluar')

@section('content')
<div class="container-fluid py-4">

    <div class="card border-0 shadow-lg rounded-4 mb-4">
        <div class="card-body text-white" style="background: linear-gradient(135deg, #0d6efd, #6610f2);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1">
                        <i class="fas fa-arrow-up me-2"></i>
                        Kas Keluar
                    </h2>
                    <p class="mb-0 opacity-75">Catatan pengeluaran biaya</p>
                </div>
                <a href="{{ route('kas-keluar.create') }}" class="btn btn-light">
                    <i class="fas fa-plus-circle me-1"></i> Tambah Biaya
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-3">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Tanggal</th>
                            <th>Jenis Biaya</th>
                            <th class="text-end">Nominal</th>
                            <th>Keterangan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kasKeluar as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                            <td>
                                @if($item->jenis == 'Biaya Operasional')
                                    <span class="badge bg-primary">Operasional</span>
                                @elseif($item->jenis == 'Biaya Admin')
                                    <span class="badge bg-warning text-dark">Admin</span>
                                @elseif($item->jenis == 'Gaji')
                                    <span class="badge bg-info">Gaji</span>
                                @else
                                    <span class="badge bg-secondary">Lain-lain</span>
                                @endif
                            </td>
                            <td class="text-end text-danger">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                            <td>{{ $item->keterangan ?? '-' }}</td>
                            <td class="text-center">
                                <form action="{{ route('kas-keluar.destroy', $item->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus data ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x mb-3 d-block opacity-25"></i>
                                <h6>Belum ada data kas keluar</h6>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold">
                            <td colspan="3" class="text-end">TOTAL</td>
                            <td class="text-end text-danger">Rp {{ number_format($total, 0, ',', '.') }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection