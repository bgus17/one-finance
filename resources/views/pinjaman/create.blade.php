@extends('layouts.app')

@section('content')

<div class="container py-4">
<div class="card border-0 shadow-lg rounded-4">

    {{-- Header --}}
    <div class="card-header border-0 text-white py-4"
         style="background: linear-gradient(135deg,#198754,#0d6efd);">

        <div class="d-flex align-items-center">

            <div class="bg-white bg-opacity-25 rounded-circle p-3 me-3">
                <i class="fas fa-hand-holding-usd fa-lg"></i>
            </div>

            <div>
                <h3 class="mb-0 fw-bold">
                    Tambah Pinjaman
                </h3>

                <small class="opacity-75">
                    Input data pinjaman nasabah baru
                </small>
            </div>

        </div>

    </div>

    {{-- Body --}}
    <div class="card-body p-4">

        @if ($errors->any())

            <div class="alert alert-danger border-0 shadow-sm rounded-3">

                <div class="d-flex">

                    <i class="fas fa-exclamation-circle me-2 mt-1"></i>

                    <div>

                        <strong>Terjadi Kesalahan :</strong>

                        <ul class="mb-0 mt-2">

                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach

                        </ul>

                    </div>

                </div>

            </div>

        @endif

        <form action="{{ route('pinjaman.store') }}" method="POST">

            @csrf

            <div class="row">

                {{-- Nasabah --}}
                <div class="col-md-6 mb-4">

                    <label class="form-label fw-semibold">
                        Nasabah
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="fas fa-user text-primary"></i>
                        </span>

                        <select id="nasabah_id"
                                name="nasabah_id"
                                class="form-select"
                                required>

                            <option value="">
                                Pilih Nasabah
                            </option>

                            @foreach ($nasabah as $n)
                                <option value="{{ $n->id }}">
                                    {{ $n->kode_nasabah }}
                                    -
                                    {{ $n->nama }}
                                </option>
                            @endforeach

                        </select>

                    </div>

                </div>

                {{-- Paket Pinjaman --}}
                <div class="col-md-6 mb-4">

                    <label class="form-label fw-semibold">
                        Paket Pinjaman
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="fas fa-wallet text-success"></i>
                        </span>

                        <select name="paket_pinjaman_id"
                                class="form-select"
                                required>

                            <option value="">
                                Pilih Paket
                            </option>

                            @foreach ($paketPinjaman as $p)

                                <option value="{{ $p->id }}">

                                    {{ $p->nama_paket }}

                                    | Rp {{ number_format($p->nominal_pinjaman,0,',','.') }}

                                    | {{ $p->tenor_minggu }} Minggu

                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>

            </div>

            <div class="row">

                {{-- Tanggal Pengajuan --}}
                <div class="col-md-6 mb-4">

                    <label class="form-label fw-semibold">
                        Tanggal Pengajuan
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="fas fa-calendar-alt text-warning"></i>
                        </span>

                        <input type="date"
                               name="tanggal_pengajuan"
                               class="form-control"
                               value="{{ date('Y-m-d') }}"
                               required>

                    </div>

                </div>

                {{-- Tanggal Cair --}}
                <div class="col-md-6 mb-4">

                    <label class="form-label fw-semibold">
                        Tanggal Cair
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="fas fa-money-check-alt text-success"></i>
                        </span>

                        <input type="date"
                               name="tanggal_cair"
                               class="form-control"
                               value="{{ date('Y-m-d') }}"
                               required>

                    </div>

                </div>

            </div>

            <hr>

            {{-- Action Button --}}
            <div class="d-flex justify-content-end gap-2">

                <a href="{{ route('pinjaman.index') }}"
                   class="btn btn-light border rounded-pill px-4">

                    <i class="fas fa-arrow-left me-1"></i>
                    Kembali

                </a>

                <button type="submit"
                        class="btn btn-primary rounded-pill px-4 shadow-sm">

                    <i class="fas fa-save me-1"></i>
                    Simpan Pinjaman

                </button>

            </div>

        </form>

    </div>

</div>

</div>

@push('scripts')

<script>

$(document).ready(function () {

    $('#nasabah_id').select2({
        placeholder: 'Cari Nasabah...',
        allowClear: true,
        width: '100%'
    });

});

</script>

@endpush

@endsection
