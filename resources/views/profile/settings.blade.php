@extends('layouts.app')

@section('title', 'Pengaturan Akun')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- BANNER HEADER --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden position-relative">
        <div class="position-absolute start-0 top-0 h-100 bg-primary" style="width: 5px;"></div>
        <div class="card-body p-4">
            <h4 class="fw-bold text-dark mb-1">Pengaturan & Keamanan</h4>
            <p class="text-muted small mb-0">Kelola informasi profil dan perbarui kata sandi akun Anda secara berkala.</p>
        </div>
    </div>

    {{-- ALERT SUKSES --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 alert-dismissible fade show mb-4 d-flex align-items-center" role="alert">
            <i class="fas fa-check-circle fs-5 me-3 text-success"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- KARTU INFORMASI PROFIL --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white text-center p-4 h-100">
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <div class="avatar-circle bg-primary bg-opacity-10 text-primary fw-bold fs-3 rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <h5 class="fw-bold text-dark mb-1">{{ Auth::user()->name }}</h5>
                    <span class="badge bg-light text-primary border px-3 py-1 rounded-pill fs-xs mb-3">
                        {{ ucfirst(Auth::user()->role ?? 'Pengguna') }}
                    </span>
                    <p class="text-muted small mb-0"><i class="fas fa-envelope me-1"></i> {{ Auth::user()->email }}</p>
                </div>
            </div>
        </div>

        {{-- FORM GANTI PASSWORD --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 bg-white">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="fw-bold text-dark mb-0 fs-6">
                        <i class="fas fa-key me-2 text-primary"></i> Ganti Kata Sandi
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('profile.update-password') }}" method="POST">
                        @csrf
                        
                        {{-- Password Saat Ini --}}
                        <div class="mb-3">
                            <label class="form-label text-uppercase fs-xs fw-bold text-muted mb-1">Password Saat Ini</label>
                            <input type="password" name="current_password" class="form-control border-1 bg-light rounded-3 text-dark fw-medium @error('current_password') is-invalid @enderror" placeholder="Masukkan password lama" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4 text-muted opacity-10">

                        {{-- Password Baru --}}
                        <div class="mb-3">
                            <label class="form-label text-uppercase fs-xs fw-bold text-muted mb-1">Password Baru</label>
                            <input type="password" name="password" class="form-control border-1 bg-light rounded-3 text-dark fw-medium @error('password') is-invalid @enderror" placeholder="Minimal 8 karakter" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Konfirmasi Password Baru --}}
                        <div class="mb-4">
                            <label class="form-label text-uppercase fs-xs fw-bold text-muted mb-1">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="form-control border-1 bg-light rounded-3 text-dark fw-medium" placeholder="Ulangi password baru" required>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary rounded-3 px-4 py-2 fw-semibold shadow-sm">
                                <i class="fas fa-save me-1"></i> Simpan Password Baru
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
    .fs-xs {
        font-size: 0.75rem;
    }
</style>
@endsection