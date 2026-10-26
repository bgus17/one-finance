@extends('layouts.app')

@section('title', 'Backup Database')

@section('content')
<div class="container-fluid py-4">

    <div class="card border-0 shadow-lg rounded-4 mb-4">
        <div class="card-body text-white" style="background: linear-gradient(135deg, #0d6efd, #6610f2);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1">
                        <i class="fas fa-database me-2"></i>
                        Backup Database
                    </h2>
                    <p class="mb-0 opacity-75">Backup otomatis setiap hari jam 01:00</p>
                </div>
                <form action="{{ route('backup.create') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-light">
                        <i class="fas fa-plus-circle me-1"></i> Backup Sekarang
                    </button>
                </form>
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
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-primary">
                    <i class="fas fa-list me-2"></i>
                    Daftar Backup
                </h5>
                <span class="badge bg-primary rounded-pill px-3 py-2">
                    <i class="fas fa-file me-1"></i>
                    {{ count($backups) }} File
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nama File</th>
                            <th>Ukuran</th>
                            <th>Tanggal</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($backups as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <i class="fas fa-file-archive text-warning me-2"></i>
                                {{ $item['name'] }}
                            </td>
                            <td>{{ $item['size'] }}</td>
                            <td>{{ $item['date'] }}</td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('backup.download', $item['name']) }}" 
                                       class="btn btn-success" title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <form action="{{ route('backup.destroy', $item['name']) }}" 
                                          method="POST" 
                                          style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-danger"
                                                onclick="return confirm('Yakin hapus backup ini?')"
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-4x mb-3 d-block opacity-25"></i>
                                    <h5 class="fw-bold">Belum Ada Backup</h5>
                                    <p class="mb-0">Klik "Backup Sekarang" untuk membuat backup pertama.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Backup otomatis setiap hari jam 01:00
                </small>
                <small class="text-muted">
                    <i class="fas fa-trash me-1"></i>
                    Backup otomatis dihapus setelah 30 hari
                </small>
            </div>
        </div>
    </div>

</div>
@endsection