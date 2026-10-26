@extends('layouts.app')

@section('title', 'Tambah Nasabah')

@section('content')
<div class="container py-4">

    <div class="card border-0 shadow-lg rounded-4">

        {{-- Header --}}
        <div class="card-header border-0 text-white py-4 rounded-top-4"
             style="background: linear-gradient(135deg, #0d6efd, #6610f2);">
            <div class="d-flex align-items-center">
                <div class="bg-white bg-opacity-25 rounded-circle p-3 me-3">
                    <i class="fas fa-user-plus fa-lg"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold">Tambah Nasabah</h3>
                    <small class="opacity-75">Lengkapi data nasabah baru</small>
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="card-body p-4">

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <h5 class="alert-heading"><i class="fas fa-exclamation-circle me-2"></i>Terjadi Kesalahan:</h5>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('nasabah.store') }}" method="POST">
                @csrf

                <div class="row">

                    {{-- Nama --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Nama Nasabah <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user text-primary"></i></span>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                                   placeholder="Masukkan nama nasabah" value="{{ old('nama') }}" required>
                        </div>
                        @error('nama')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    {{-- NIK --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            NIK <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-id-card text-primary"></i></span>
                            <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror"
                                   placeholder="Masukkan NIK" value="{{ old('nik') }}" required>
                        </div>
                        @error('nik')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    {{-- Tanggal Lahir --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-calendar-alt text-primary me-1"></i> Tanggal Lahir
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-birthday-cake text-primary"></i></span>
                            <input type="date" name="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                   value="{{ old('tanggal_lahir') }}">
                        </div>
                        @error('tanggal_lahir')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    {{-- No HP --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            No HP <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone text-primary"></i></span>
                            <input type="text" name="no_hp" class="form-control @error('no_hp') is-invalid @enderror"
                                   placeholder="08xxxxxxxxxx" value="{{ old('no_hp') }}" required>
                        </div>
                        @error('no_hp')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    {{-- WILAYAH --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Wilayah <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-map-marker-alt text-primary"></i></span>
                            <select name="wilayah_id" id="wilayah_id" class="form-select @error('wilayah_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Wilayah --</option>
                                @foreach($wilayah as $item)
                                    <option value="{{ $item->id }}" {{ old('wilayah_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->nama_wilayah }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('wilayah_id')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    {{-- DESA --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Desa <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-home text-primary"></i></span>
                            <select name="desa_id" id="desa_id" class="form-select @error('desa_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Desa --</option>
                            </select>
                        </div>
                        @error('desa_id')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    {{-- ALAMAT DETAIL (OPSIONAL) --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Alamat Detail</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-pen text-primary"></i></span>
                            <input type="text" name="alamat" class="form-control @error('alamat') is-invalid @enderror"
                                   placeholder="Jl. Mawar No.123 (opsional)" value="{{ old('alamat') }}">
                        </div>
                        <small class="text-muted">Detail rumah/jalan (tidak wajib)</small>
                        @error('alamat')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    {{-- DC --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            DC <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-building text-primary"></i></span>
                            <select name="dc_id" class="form-select @error('dc_id') is-invalid @enderror" required>
                                <option value="">-- Pilih DC --</option>
                                @foreach($dc as $item)
                                    <option value="{{ $item->id }}" {{ old('dc_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->nama_dc }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('dc_id')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    {{-- Pekerjaan --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Pekerjaan</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-briefcase text-primary"></i></span>
                            <input type="text" name="pekerjaan" class="form-control @error('pekerjaan') is-invalid @enderror"
                                   placeholder="Masukkan pekerjaan" value="{{ old('pekerjaan') }}">
                        </div>
                        @error('pekerjaan')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                </div>

                <hr>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('nasabah.index') }}" class="btn btn-light border rounded-pill px-4">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm">
                        <i class="fas fa-save me-1"></i> Simpan Data
                    </button>
                </div>

            </form>

        </div>

    </div>

</div>

<style>
    .rounded-top-4 { border-top-left-radius: 1rem !important; border-top-right-radius: 1rem !important; }
    .card { border: none !important; }
    .form-control:focus, .form-select:focus {
        border-color: #6610f2;
        box-shadow: 0 0 0 0.2rem rgba(102, 16, 242, 0.15);
    }
    .alert ul { padding-left: 20px; }
</style>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#wilayah_id').on('change', function() {
            var wilayahId = $(this).val();
            var desaSelect = $('#desa_id');
            
            desaSelect.empty();
            desaSelect.append('<option value="">-- Pilih Desa --</option>');
            
            if (wilayahId) {
                // 🔥 PAKAI URL LANGSUNG
                var url = '/get-desa/' + wilayahId;
                
                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        if (data.length > 0) {
                            $.each(data, function(key, value) {
                                desaSelect.append('<option value="'+ value.id +'">'+ value.nama_desa +'</option>');
                            });
                        } else {
                            desaSelect.append('<option value="">Tidak ada desa</option>');
                        }
                    },
                    error: function(xhr) {
                        console.log('Error:', xhr.responseText);
                        desaSelect.append('<option value="">Gagal memuat data</option>');
                    }
                });
            }
        });

        // Trigger change jika ada old value
        @if(old('wilayah_id'))
            $('#wilayah_id').trigger('change');
        @endif
    });
</script>
@endpush

@endsection