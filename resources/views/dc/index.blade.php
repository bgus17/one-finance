@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">
<div class="card border-0 shadow-lg rounded-4">

    {{-- Header --}}
    <div class="card-header border-0 text-white py-4"
         style="background: linear-gradient(135deg,#0d6efd,#6610f2);">

        <div class="d-flex justify-content-between align-items-center">

            <div>

                <h3 class="mb-1 fw-bold">
                    <i class="fas fa-users-cog me-2"></i>
                    Data DC
                </h3>

                <small class="opacity-75">
                    Kelola data Debt Collector
                </small>

            </div>

            <a href="{{ route('dc.create') }}"
               class="btn btn-light rounded-pill px-4 fw-semibold shadow-sm">

                <i class="fas fa-plus-circle me-1"></i>
                Tambah DC

            </a>

        </div>

    </div>

    {{-- Body --}}
    <div class="card-body">

        @if(session('success'))

            <div class="alert alert-success border-0 shadow-sm rounded-3">

                <i class="fas fa-check-circle me-2"></i>

                {{ session('success') }}

            </div>

        @endif

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-light">

                    <tr>
                        <th width="70">No</th>
                        <th>Kode DC</th>
                        <th>Nama DC</th>
                        <th>Wilayah</th>
                        <th>No HP</th>
                        <th>Status</th>
                        <th width="150" class="text-center">
                            Aksi
                        </th>
                    </tr>

                </thead>

                <tbody>

                @forelse($dc as $item)

                    <tr>

                        <td>
                            <span class="fw-bold text-primary">
                                {{ $loop->iteration }}
                            </span>
                        </td>

                        <td>

                            <span class="badge bg-light text-dark border">
                                {{ $item->kode_dc }}
                            </span>

                        </td>

                        <td>

                            <div class="fw-semibold">
                                {{ $item->nama_dc }}
                            </div>

                        </td>

                        <td>

                            <i class="fas fa-map-marker-alt text-danger me-1"></i>

                            {{ $item->wilayah->nama_wilayah ?? '-' }}

                        </td>

                        <td>

                            <i class="fas fa-phone-alt text-success me-1"></i>

                            {{ $item->no_hp }}

                        </td>

                        <td>

                            @if($item->status == 'aktif')

                                <span class="badge bg-success rounded-pill px-3">

                                    <i class="fas fa-check-circle me-1"></i>
                                    Aktif

                                </span>

                            @else

                                <span class="badge bg-danger rounded-pill px-3">

                                    <i class="fas fa-times-circle me-1"></i>
                                    Nonaktif

                                </span>

                            @endif

                        </td>

                        <td class="text-center">

                            <div class="btn-group">

                                <a href="{{ route('dc.edit',$item->id) }}"
                                   class="btn btn-outline-warning btn-sm">

                                    <i class="fas fa-edit"></i>

                                </a>

                                <form action="{{ route('dc.destroy',$item->id) }}"method="POST"
                                      class="d-inline">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-outline-danger btn-sm"
                                            onclick="return confirm('Hapus data ini?')">

                                        <i class="fas fa-trash"></i>

                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="7" class="text-center py-5">

                            <div class="text-muted">

                                <i class="fas fa-users fa-3x mb-3"></i>

                                <h6>Belum Ada Data DC</h6>

                                <p class="mb-0">
                                    Silakan tambahkan data DC terlebih dahulu.
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
