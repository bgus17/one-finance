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
                    <i class="fas fa-map-marked-alt me-2"></i>
                    Data Wilayah
                </h3>

                <small class="opacity-75">
                    Kelola seluruh data wilayah operasional
                </small>
            </div>

            <a href="/wilayah/create"
               class="btn btn-light rounded-pill px-4 fw-semibold shadow-sm">

                <i class="fas fa-plus-circle me-1"></i>
                Tambah Wilayah

            </a>

        </div>

    </div>

    {{-- Body --}}
    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-light">

                    <tr>
                        <th width="80">No</th>
                        <th>Nama Wilayah</th>
                        <th>Keterangan</th>
                        <th width="150" class="text-center">
                            Aksi
                        </th>
                    </tr>

                </thead>

                <tbody>

                @forelse($wilayah as $row)

                    <tr>

                        <td>
                            <span class="fw-bold text-primary">
                                {{ $loop->iteration }}
                            </span>
                        </td>

                        <td>
                            <div class="fw-semibold">
                                <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                {{ $row->nama_wilayah }}
                            </div>
                        </td>

                        <td>
                            {{ $row->keterangan ?: '-' }}
                        </td>

                        <td class="text-center">

                            <div class="btn-group">

                                <a href="/wilayah/edit/{{ $row->id }}"
                                   class="btn btn-outline-warning btn-sm">

                                    <i class="fas fa-edit"></i>

                                </a>

                                <form action="/wilayah/delete/{{ $row->id }}"
                                      method="POST"
                                      class="d-inline">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            onclick="return confirm('Yakin ingin menghapus data ini?')"
                                            class="btn btn-outline-danger btn-sm">

                                        <i class="fas fa-trash"></i>

                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="4" class="text-center py-5">

                            <div class="text-muted">

                                <i class="fas fa-map fa-3x mb-3"></i>

                                <h6>Belum Ada Data Wilayah</h6>

                                <p class="mb-0">
                                    Silakan tambahkan wilayah baru terlebih dahulu.
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
