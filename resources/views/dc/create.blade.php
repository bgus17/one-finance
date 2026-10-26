@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="card border-0 shadow-lg rounded-4">

            {{-- Header --}}
            <div class="card-header border-0 text-white py-4" style="background: linear-gradient(135deg,#0d6efd,#6610f2);">

                <div class="d-flex align-items-center">

                    <div class="bg-white bg-opacity-25 rounded-circle p-3 me-3">
                        <i class="fas fa-user-shield fa-lg"></i>
                    </div>

                    <div>
                        <h3 class="mb-0 fw-bold">Tambah DC</h3>
                        <small class="opacity-75">
                            Tambahkan data Debt Collector baru
                        </small>
                    </div>

                </div>

            </div>

            {{-- Body --}}
            <div class="card-body p-4">
                <form action="{{ route('dc.store') }}" method="POST">
                    @csrf

                    <div class="row">

                        {{-- User Login --}}
                        <div class="col-md-6 mb-4">

                            <label class="form-label fw-semibold">
                                User Login
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="fas fa-user text-primary"></i>
                                </span>

                                <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>

                                    <option value="">
                                        -- Pilih User --
                                    </option>

                                    @foreach ($user as $item)
                                        <option value="{{ $item->id }}"
                                            {{ old('user_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach

                                </select>

                            </div>

                            @error('user_id')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        {{-- Wilayah --}}
                        <div class="col-md-6 mb-4">

                            <label class="form-label fw-semibold">
                                Wilayah
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="fas fa-map-marker-alt text-primary"></i>
                                </span>

                                <select name="wilayah_id" class="form-select @error('wilayah_id') is-invalid @enderror"
                                    required>

                                    <option value="">
                                        -- Pilih Wilayah --
                                    </option>

                                    @foreach ($wilayah as $item)
                                        <option value="{{ $item->id }}"
                                            {{ old('wilayah_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->nama_wilayah }}
                                        </option>
                                    @endforeach

                                </select>

                            </div>

                            @error('wilayah_id')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                    </div>

                    <div class="row">

                        {{-- Nama DC --}}
                        <div class="col-md-6 mb-4">

                            <label class="form-label fw-semibold">
                                Nama DC
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="fas fa-id-badge text-success"></i>
                                </span>

                                <input type="text" name="nama_dc" value="{{ old('nama_dc') }}"
                                    class="form-control @error('nama_dc') is-invalid @enderror"
                                    placeholder="Masukkan nama DC" required>

                            </div>

                            @error('nama_dc')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        {{-- No HP --}}
                        <div class="col-md-6 mb-4">

                            <label class="form-label fw-semibold">
                                No HP
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="fas fa-phone text-success"></i>
                                </span>

                                <input type="text" name="no_hp" value="{{ old('no_hp') }}"
                                    class="form-control @error('no_hp') is-invalid @enderror" placeholder="08xxxxxxxxxx">

                            </div>

                            @error('no_hp')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                    </div>

                    {{-- Alamat --}}
                    <div class="mb-4">

                        <label class="form-label fw-semibold">
                            Alamat
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="fas fa-home text-primary"></i>
                            </span>

                            <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3"
                                placeholder="Masukkan alamat DC">{{ old('alamat') }}</textarea>

                        </div>

                        @error('alamat')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <div class="row">

                        {{-- Tanggal Masuk --}}
                        <div class="col-md-6 mb-4">

                            <label class="form-label fw-semibold">
                                Tanggal Masuk
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="fas fa-calendar-alt text-warning"></i>
                                </span>

                                <input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk') }}"
                                    class="form-control @error('tanggal_masuk') is-invalid @enderror">

                            </div>

                            @error('tanggal_masuk')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        {{-- Status --}}
                        <div class="col-md-6 mb-4">

                            <label class="form-label fw-semibold">
                                Status
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="fas fa-toggle-on text-danger"></i>
                                </span>

                                <select name="status" class="form-select @error('status') is-invalid @enderror">

                                    <option value="aktif" {{ old('status', 'aktif') == 'aktif' ? 'selected' : '' }}>
                                        Aktif
                                    </option>

                                    <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>
                                        Nonaktif
                                    </option>

                                </select>

                            </div>

                            @error('status')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                    </div>

                    <hr>

                    {{-- Button --}}
                    <div class="d-flex justify-content-end gap-2">

                        <a href="{{ route('dc.index') }}" class="btn btn-light border rounded-pill px-4">

                            <i class="fas fa-arrow-left me-1"></i>
                            Kembali

                        </a>

                        <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm">

                            <i class="fas fa-save me-1"></i>
                            Simpan DC

                        </button>

                    </div>

                </form>

            </div>

        </div>
    </div>
@endsection
