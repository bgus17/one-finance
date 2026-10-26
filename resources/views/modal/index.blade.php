@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">

{{-- Alert --}}
@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm rounded-3">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
    </div>
@endif

{{-- Header + Total Modal --}}
<div class="row mb-4">

    <div class="col-md-8">

        <div class="card border-0 shadow-lg rounded-4">

            <div class="card-body">

                <h6 class="text-muted mb-2">
                    Total Modal
                </h6>

                <h2 class="fw-bold text-success mb-0">
                    Rp {{ number_format($totalModal, 0, ',', '.') }}
                </h2>

            </div>

        </div>

    </div>

    <div class="col-md-4">

        <div class="card border-0 shadow-lg rounded-4 h-100">

            <div class="card-body d-flex align-items-center justify-content-between">

                <div>

                    <h6 class="text-muted mb-1">
                        Data Modal
                    </h6>

                    <small class="text-muted">
                        Manajemen modal masuk & keluar
                    </small>

                </div>

                <a href="{{ route('modal.create') }}"
                   class="btn btn-primary rounded-pill px-3 shadow-sm">

                    <i class="fas fa-plus-circle me-1"></i>
                    Tambah

                </a>

            </div>

        </div>

    </div>

</div>

{{-- Table --}}
<div class="card border-0 shadow-lg rounded-4">

    <div class="card-header bg-white border-0 py-3">

        <h5 class="mb-0 fw-bold text-primary">
            <i class="fas fa-coins me-2"></i>
            Riwayat Modal
        </h5>

    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-light">

                    <tr>
                        <th width="70">No</th>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Nominal</th>
                        <th>Keterangan</th>
                        <th width="160" class="text-center">Aksi</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($modal as $item)

                        <tr>

                            <td>
                                <span class="fw-bold text-primary">
                                    {{ $loop->iteration }}
                                </span>
                            </td>

                            <td>
                                <i class="fas fa-calendar-alt text-warning me-1"></i>
                                {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                            </td>

                            <td>

                                @if($item->nama_modal == 'Kas Koperasi' || $item->nama_modal == 'modal_awal')
                                    <span class="badge bg-primary rounded-pill px-3">
                                        <i class="fas fa-coins me-1"></i>
                                        Modal Awal
                                    </span>
                                @elseif($item->nama_modal == 'tambahan_modal' || $item->jenis == 'masuk')
                                    <span class="badge bg-success rounded-pill px-3">
                                        <i class="fas fa-plus-circle me-1"></i>
                                        Tambahan Modal
                                    </span>
                                @elseif($item->jenis == 'keluar')
                                    <span class="badge bg-danger rounded-pill px-3">
                                        <i class="fas fa-arrow-up me-1"></i>
                                        Keluar
                                    </span>
                                @else
                                    <span class="badge bg-success rounded-pill px-3">
                                        <i class="fas fa-arrow-down me-1"></i>
                                        {{ ucwords(str_replace('_', ' ', $item->nama_modal ?? 'Modal Masuk')) }}
                                    </span>
                                @endif

                            </td>

                            <td class="fw-bold text-success">
                                Rp {{ number_format($item->nominal, 0, ',', '.') }}
                            </td>

                            <td>
                                {{ $item->keterangan ?: '-' }}
                            </td>

                            <td class="text-center">

                                <div class="btn-group">

                                    <a href="{{ route('modal.edit', $item->id) }}"
                                       class="btn btn-outline-warning btn-sm"
                                       title="Edit Modal">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    @if($item->id != 1 && $item->nama_modal != 'Kas Koperasi')
                                        <form action="{{ route('modal.destroy', $item->id) }}"
                                              method="POST"
                                              class="d-inline">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-outline-danger btn-sm"
                                                    onclick="return confirm('Yakin ingin menghapus data modal ini? Saldo kas akan otomatis berkurang.')"
                                                    title="Hapus Modal">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6" class="text-center py-5">

                                <div class="text-muted">

                                    <i class="fas fa-coins fa-3x mb-3"></i>

                                    <h6>Belum Ada Data Modal</h6>

                                    <p class="mb-0">
                                        Silakan tambahkan data modal terlebih dahulu.
                                    </p>

                                </div>

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
