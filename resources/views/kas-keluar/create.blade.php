@extends('layouts.app')

@section('title', 'Tambah Biaya')

@section('content')
<div class="container-fluid py-4">

    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0 fw-bold text-primary">
                <i class="fas fa-plus-circle me-2"></i>
                Tambah Biaya / Kas Keluar
            </h5>
        </div>
        <div class="card-body">

            <form action="{{ route('kas-keluar.store') }}" method="POST">
                @csrf

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" 
                               name="tanggal" 
                               class="form-control @error('tanggal') is-invalid @enderror" 
                               value="{{ old('tanggal', date('Y-m-d')) }}" 
                               required>
                        @error('tanggal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Jenis Biaya <span class="text-danger">*</span></label>
                        <select name="jenis" class="form-select @error('jenis') is-invalid @enderror" required>
                            <option value="">-- Pilih Jenis Biaya --</option>
                            <option value="Biaya Operasional" {{ old('jenis') == 'Biaya Operasional' ? 'selected' : '' }}>Biaya Operasional</option>
                            <option value="Biaya Admin" {{ old('jenis') == 'Biaya Admin' ? 'selected' : '' }}>Biaya Admin</option>
                            <option value="Gaji" {{ old('jenis') == 'Gaji' ? 'selected' : '' }}>Gaji</option>
                            <option value="Lain-lain" {{ old('jenis') == 'Lain-lain' ? 'selected' : '' }}>Lain-lain</option>
                        </select>
                        @error('jenis')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Nominal <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" 
                                   name="nominal" 
                                   class="form-control @error('nominal') is-invalid @enderror" 
                                   placeholder="0" 
                                   value="{{ old('nominal') }}" 
                                   required>
                        </div>
                        @error('nominal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Keterangan</label>
                        <input type="text" 
                               name="keterangan" 
                               class="form-control @error('keterangan') is-invalid @enderror" 
                               placeholder="Deskripsi biaya..." 
                               value="{{ old('keterangan') }}">
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-success px-5">
                            <i class="fas fa-save me-1"></i> Simpan
                        </button>
                        <a href="{{ route('kas-keluar.index') }}" class="btn btn-secondary px-5">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>

                </div>

            </form>

        </div>
    </div>

</div>
@endsection