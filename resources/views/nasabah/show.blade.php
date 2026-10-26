@extends('layouts.app')

@section('title', 'Detail Nasabah')

@section('content')
<div class="container py-4">

    <div class="card border-0 shadow-lg rounded-4">

        {{-- Header --}}
        <div class="card-header border-0 text-white py-4 rounded-top-4"
             style="background: linear-gradient(135deg, #198754, #0d6efd);">
            <div class="d-flex align-items-center">
                <div class="bg-white bg-opacity-25 rounded-circle p-3 me-3">
                    <i class="fas fa-user-circle fa-lg"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold">Detail Nasabah</h3>
                    <small class="opacity-75">{{ $nasabah->nama }}</small>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td width="150" class="fw-bold text-muted">Kode Nasabah</td>
                            <td>: <span class="badge bg-primary">{{ $nasabah->kode_nasabah }}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Nama</td>
                            <td>: <strong>{{ $nasabah->nama }}</strong></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">NIK</td>
                            <td>: {{ $nasabah->nik }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Tanggal Lahir</td>
                            <td>: {{ $nasabah->tanggal_lahir_formatted }} 
                                @if($nasabah->umur)
                                <span class="badge bg-secondary">Umur {{ $nasabah->umur }} tahun</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Status</td>
                            <td>: 
                                <span class="badge {{ $nasabah->status == 'aktif' ? 'bg-success' : 'bg-danger' }}">
                                    {{ ucfirst($nasabah->status) }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td width="150" class="fw-bold text-muted">Wilayah</td>
                            <td>: {{ $nasabah->wilayah->nama_wilayah ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">DC</td>
                            <td>: {{ $nasabah->dc->nama_dc ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">No HP</td>
                            <td>: {{ $nasabah->no_hp }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Pekerjaan</td>
                            <td>: {{ $nasabah->pekerjaan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Alamat</td>
                            <td>: {{ $nasabah->alamat }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="card-footer bg-white border-0 rounded-bottom-4 py-3">
            <div class="d-flex gap-2">
                <a href="{{ route('nasabah.index') }}" class="btn btn-secondary rounded-pill px-4">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
                <a href="{{ route('nasabah.edit', $nasabah->id) }}" class="btn btn-warning rounded-pill px-4">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>
            </div>
        </div>

    </div>

</div>
@endsection