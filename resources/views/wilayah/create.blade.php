@extends('layouts.app')

@section('content')

<div class="container py-4">

<div class="card border-0 shadow-lg rounded-4">

    {{-- Header --}}
    <div class="card-header border-0 text-white py-4"
         style="background: linear-gradient(135deg,#0d6efd,#6610f2);">

        <div class="d-flex align-items-center">

            <div class="bg-white bg-opacity-25 rounded-circle p-3 me-3">
                <i class="fas fa-map-marked-alt fa-lg"></i>
            </div>

            <div>
                <h3 class="mb-0 fw-bold">
                    Tambah Wilayah
                </h3>

                <small class="opacity-75">
                    Tambahkan data wilayah operasional baru
                </small>
            </div>

        </div>

    </div>

    {{-- Body --}}
    <div class="card-body p-4">

        <form action="/wilayah/store" method="POST">

            @csrf

            {{-- Nama Wilayah --}}
            <div class="mb-4">

                <label class="form-label fw-semibold">
                    Nama Wilayah
                </label>

                <div class="input-group">

                    <span class="input-group-text">
                        <i class="fas fa-map-marker-alt text-primary"></i>
                    </span>

                    <input type="text"
                           name="nama_wilayah"
                           class="form-control"
                           placeholder="Masukkan nama wilayah"
                           required>

                </div>

            </div>

            {{-- Keterangan --}}
            <div class="mb-4">

                <label class="form-label fw-semibold">
                    Keterangan
                </label>

                <div class="input-group">

                    <span class="input-group-text">
                        <i class="fas fa-file-alt text-primary"></i>
                    </span>

                    <textarea name="keterangan"
                              rows="4"
                              class="form-control"
                              placeholder="Masukkan keterangan wilayah (opsional)"></textarea>

                </div>

            </div>

            <hr>

            {{-- Button --}}
            <div class="d-flex justify-content-end gap-2">

                <a href="/wilayah"
                   class="btn btn-light border rounded-pill px-4">

                    <i class="fas fa-arrow-left me-1"></i>
                    Kembali

                </a>

                <button type="submit"
                        class="btn btn-success rounded-pill px-4 shadow-sm">

                    <i class="fas fa-save me-1"></i>
                    Simpan Data

                </button>

            </div>

        </form>

    </div>

</div>

</div>

@endsection
