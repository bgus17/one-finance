@extends('layouts.app')

@section('title', 'Ganti Sandi')

@section('page-header')
<div class="page-header d-flex align-items-center">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col">
                <h4 class="page-title mb-0">
                    <i class="fas fa-key me-2 text-primary"></i>
                    Ganti Sandi
                </h4>
                <small class="text-muted">Perbarui password akun Anda</small>
            </div>
            <div class="col-auto">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm rounded-pill">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container py-4">

    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">

            <div class="card border-0 shadow-lg rounded-4">
                
                {{-- Header --}}
                <div class="card-header border-0 text-white py-4 rounded-top-4"
                     style="background: linear-gradient(135deg, #0d6efd, #6610f2);">
                    <div class="d-flex align-items-center">
                        <div class="bg-white bg-opacity-25 rounded-circle p-3 me-3">
                            <i class="fas fa-key fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">Ganti Sandi</h5>
                            <small class="opacity-75">Aman dan terenkripsi</small>
                        </div>
                    </div>
                </div>

                {{-- Body --}}
                <div class="card-body p-4">

                    {{-- Alert Success --}}
                    @if(session('success'))
                        <div class="alert alert-success border-0 shadow-sm rounded-3 alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Alert Error --}}
                    @if(session('error'))
                        <div class="alert alert-danger border-0 shadow-sm rounded-3 alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm rounded-3 alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>Terjadi kesalahan:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Form --}}
                    <form action="{{ route('change-password.post') }}" method="POST">
                        @csrf

                        {{-- Password Saat Ini --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-lock text-primary me-1"></i>
                                Password Saat Ini <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-key text-primary"></i>
                                </span>
                                <input type="password" 
                                       name="current_password" 
                                       id="current_password"
                                       class="form-control border-start-0 @error('current_password') is-invalid @enderror" 
                                       placeholder="Masukkan password saat ini"
                                       style="border-left: none;"
                                       required>
                                <button class="btn btn-outline-secondary border-start-0" type="button" onclick="togglePassword('current_password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- Password Baru --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-key text-primary me-1"></i>
                                Password Baru <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-lock text-primary"></i>
                                </span>
                                <input type="password" 
                                       name="new_password" 
                                       id="new_password"
                                       class="form-control border-start-0 @error('new_password') is-invalid @enderror" 
                                       placeholder="Minimal 6 karakter"
                                       style="border-left: none;"
                                       required>
                                <button class="btn btn-outline-secondary border-start-0" type="button" onclick="togglePassword('new_password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('new_password')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror
                            <div class="mt-2">
                                <div class="progress" style="height: 5px; background: #e9ecef;">
                                    <div id="passwordStrength" class="progress-bar" style="width: 0%; transition: width 0.3s ease;"></div>
                                </div>
                                <small id="strengthText" class="text-muted">Kekuatan password: <span id="strengthLabel">-</span></small>
                            </div>
                        </div>

                        {{-- Konfirmasi Password Baru --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-check-circle text-primary me-1"></i>
                                Konfirmasi Password Baru <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-check text-primary"></i>
                                </span>
                                <input type="password" 
                                       name="new_password_confirmation" 
                                       id="new_password_confirmation"
                                       class="form-control border-start-0 @error('new_password_confirmation') is-invalid @enderror" 
                                       placeholder="Ulangi password baru"
                                       style="border-left: none;"
                                       required>
                                <button class="btn btn-outline-secondary border-start-0" type="button" onclick="togglePassword('new_password_confirmation')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('new_password_confirmation')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- Tips --}}
                        <div class="alert alert-info border-0 shadow-sm rounded-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>
                                <strong>Tips password aman:</strong>
                                <ul class="mb-0 mt-1 ps-3">
                                    <li>Minimal 6 karakter</li>
                                    <li>Kombinasi huruf besar, kecil, dan angka</li>
                                    <li>Jangan gunakan password yang sama dengan akun lain</li>
                                </ul>
                            </small>
                        </div>

                        {{-- Button --}}
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('dashboard') }}" class="btn btn-light border rounded-pill px-4">
                                <i class="fas fa-arrow-left me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                <i class="fas fa-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>

                    </form>

                </div>

            </div>

        </div>
    </div>

</div>

@push('scripts')
<script>
    // ============================================
    // TOGGLE PASSWORD
    // ============================================
    function togglePassword(id) {
        const input = document.getElementById(id);
        const button = input.closest('.input-group').querySelector('.btn-outline-secondary');
        const icon = button.querySelector('i');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // ============================================
    // PASSWORD STRENGTH
    // ============================================
    document.getElementById('new_password')?.addEventListener('input', function() {
        const password = this.value;
        const strengthBar = document.getElementById('passwordStrength');
        const strengthLabel = document.getElementById('strengthLabel');
        
        let strength = 0;
        let label = 'Sangat Lemah';
        let color = '#dc3545';
        let width = 20;
        
        if (password.length === 0) {
            label = '-';
            color = '#6c757d';
            width = 0;
        } else {
            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;
        }
        
        if (strength >= 4) {
            label = 'Kuat';
            color = '#198754';
            width = 100;
        } else if (strength >= 3) {
            label = 'Sedang';
            color = '#ffc107';
            width = 75;
        } else if (strength >= 2) {
            label = 'Lemah';
            color = '#fd7e14';
            width = 50;
        } else if (strength >= 1) {
            label = 'Sangat Lemah';
            color = '#dc3545';
            width = 25;
        }
        
        strengthBar.style.width = width + '%';
        strengthBar.style.background = color;
        strengthLabel.textContent = label;
        strengthLabel.style.color = color;
    });
</script>
@endpush

@endsection