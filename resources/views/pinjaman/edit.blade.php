@extends('layouts.app')

@section('title', 'Edit Pinjaman')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg mb-4 rounded-4 overflow-hidden">
        <div class="card-body text-white" style="background: linear-gradient(135deg, #0d6efd, #6610f2);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1">
                        <i class="fas fa-edit me-2"></i>
                        Edit Pinjaman
                    </h2>
                    <p class="mb-0 opacity-75">
                        <i class="fas fa-user me-1"></i>
                        {{ $pinjaman->nasabah->nama ?? '-' }}
                        <span class="mx-2">|</span>
                        <i class="fas fa-id-card me-1"></i>
                        {{ $pinjaman->nasabah->kode_nasabah ?? '-' }}
                        <span class="mx-2">|</span>
                        <span class="badge bg-warning text-dark">
                            Status: {{ ucfirst($pinjaman->status) }}
                        </span>
                    </p>
                </div>
                <div>
                    <a href="{{ route('pinjaman.show', $pinjaman->id) }}" class="btn btn-info btn-light me-2">
                        <i class="fas fa-eye me-1"></i> Detail
                    </a>
                    <a href="{{ route('pinjaman.index') }}" class="btn btn-light">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ALERT --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-3 alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- FORM --}}
    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-primary">
                    <i class="fas fa-edit me-2"></i>
                    Form Edit Pinjaman
                </h5>
                @if($pinjaman->status == 'menunggu')
                    <span class="badge bg-warning text-dark">
                        <i class="fas fa-edit me-1"></i> Dapat Diedit
                    </span>
                @else
                    <span class="badge bg-secondary">
                        <i class="fas fa-lock me-1"></i> Hanya Status
                    </span>
                @endif
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('pinjaman.update', $pinjaman->id) }}" method="POST" id="formPinjaman">
                @csrf
                @method('PUT')

                <div class="row">

                    {{-- ============================================ --}}
                    {{-- INFORMASI PINJAMAN --}}
                    {{-- ============================================ --}}
                    <div class="col-12 mb-3">
                        <h6 class="fw-bold text-primary">
                            <i class="fas fa-info-circle me-2"></i>
                            Informasi Pinjaman
                        </h6>
                        <hr>
                    </div>

                    {{-- Nasabah --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">
                            Nasabah <span class="text-danger">*</span>
                        </label>
                        <select name="nasabah_id" class="form-select @error('nasabah_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Nasabah --</option>
                            @foreach($nasabah as $item)
                                <option value="{{ $item->id }}" {{ old('nasabah_id', $pinjaman->nasabah_id) == $item->id ? 'selected' : '' }}>
                                    {{ $item->kode_nasabah }} - {{ $item->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('nasabah_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Paket Pinjaman --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">
                            Paket Pinjaman <span class="text-danger">*</span>
                        </label>
                        <select name="paket_pinjaman_id" 
                                class="form-select @error('paket_pinjaman_id') is-invalid @enderror" 
                                {{ $pinjaman->status != 'menunggu' ? 'disabled' : '' }}
                                required>
                            <option value="">-- Pilih Paket --</option>
                            @foreach($paketPinjaman as $item)
                                <option value="{{ $item->id }}" 
                                        data-nominal="{{ $item->nominal_pinjaman }}"
                                        data-adm="{{ $item->nominal_adm }}"
                                        data-tenor="{{ $item->tenor_minggu }}"
                                        data-angsuran="{{ $item->angsuran_mingguan }}"
                                        data-dicairkan="{{ $item->nominal_dicairkan }}"
                                        data-total="{{ $item->total_tagihan }}"
                                        {{ old('paket_pinjaman_id', $pinjaman->paket_pinjaman_id) == $item->id ? 'selected' : '' }}>
                                    {{ $item->nama_paket }} - Rp {{ number_format($item->nominal_pinjaman, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                        @error('paket_pinjaman_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($pinjaman->status != 'menunggu')
                            <input type="hidden" name="paket_pinjaman_id" value="{{ $pinjaman->paket_pinjaman_id }}">
                            <small class="text-muted">
                                <i class="fas fa-lock me-1"></i>
                                Paket tidak dapat diubah karena pinjaman sudah {{ $pinjaman->status }}
                            </small>
                        @endif
                    </div>

                    {{-- ============================================ --}}
                    {{-- DETAIL PINJAMAN (HANYA BISA DIEDIT JIKA MENUNGGU) --}}
                    {{-- ============================================ --}}
                    <div class="col-12 mb-3">
                        <h6 class="fw-bold text-primary">
                            <i class="fas fa-money-bill-wave me-2"></i>
                            Detail Pinjaman
                            @if($pinjaman->status != 'menunggu')
                                <span class="badge bg-secondary ms-2">
                                    <i class="fas fa-lock me-1"></i> Terkunci
                                </span>
                            @endif
                        </h6>
                        <hr>
                    </div>

                    {{-- Jumlah Pinjaman --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Jumlah Pinjaman</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" 
                                   name="jumlah_pinjam" 
                                   id="jumlah_pinjam"
                                   class="form-control @error('jumlah_pinjam') is-invalid @enderror" 
                                   value="{{ old('jumlah_pinjam', $pinjaman->jumlah_pinjam) }}"
                                   {{ $pinjaman->status != 'menunggu' ? 'readonly' : '' }}
                                   step="1000">
                            @error('jumlah_pinjam')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @if($pinjaman->status != 'menunggu')
                            <small class="text-muted">
                                <i class="fas fa-lock me-1"></i>
                                Nominal tidak dapat diubah karena pinjaman sudah {{ $pinjaman->status }}
                            </small>
                        @endif
                    </div>

                    {{-- Biaya Administrasi --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Biaya Administrasi</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" 
                                   name="nominal_adm" 
                                   id="nominal_adm"
                                   class="form-control @error('nominal_adm') is-invalid @enderror" 
                                   value="{{ old('nominal_adm', $pinjaman->nominal_adm) }}"
                                   {{ $pinjaman->status != 'menunggu' ? 'readonly' : '' }}
                                   step="1000">
                            @error('nominal_adm')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Tenor --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Tenor (Minggu)</label>
                        <input type="number" 
                               name="tenor" 
                               id="tenor"
                               class="form-control @error('tenor') is-invalid @enderror" 
                               value="{{ old('tenor', $pinjaman->tenor) }}"
                               {{ $pinjaman->status != 'menunggu' ? 'readonly' : '' }}
                               min="1">
                        @error('tenor')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Angsuran Per Minggu --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Angsuran Per Minggu</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" 
                                   name="angsuran_per_minggu" 
                                   id="angsuran_per_minggu"
                                   class="form-control @error('angsuran_per_minggu') is-invalid @enderror" 
                                   value="{{ old('angsuran_per_minggu', $pinjaman->angsuran_per_minggu) }}"
                                   {{ $pinjaman->status != 'menunggu' ? 'readonly' : '' }}
                                   step="1000">
                            @error('angsuran_per_minggu')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- ============================================ --}}
                    {{-- AUTO CALCULATE --}}
                    {{-- ============================================ --}}
                    <div class="col-12 mb-3">
                        <div class="alert alert-info border-0 rounded-3">
                            <div class="row">
                                <div class="col-md-4">
                                    <small class="text-muted">Nominal Dicairkan</small>
                                    <h6 class="fw-bold text-success" id="display_dicairkan">
                                        Rp {{ number_format($pinjaman->nominal_dicairkan, 0, ',', '.') }}
                                    </h6>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Total Tagihan</small>
                                    <h6 class="fw-bold text-primary" id="display_total">
                                        Rp {{ number_format($pinjaman->total_pinjaman, 0, ',', '.') }}
                                    </h6>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Sisa Tagihan</small>
                                    <h6 class="fw-bold text-danger" id="display_sisa">
                                        Rp {{ number_format($pinjaman->sisa_tagihan, 0, ',', '.') }}
                                    </h6>
                                </div>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-calculator me-1"></i>
                                Auto calculate: Dicairkan = Pinjaman - Admin | Total = Angsuran x Tenor
                            </small>
                        </div>
                    </div>

                    {{-- ============================================ --}}
                    {{-- TANGGAL --}}
                    {{-- ============================================ --}}
                    <div class="col-12 mb-3">
                        <h6 class="fw-bold text-primary">
                            <i class="fas fa-calendar-alt me-2"></i>
                            Tanggal
                        </h6>
                        <hr>
                    </div>

                    {{-- Tanggal Pengajuan --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Tanggal Pengajuan</label>
                        <input type="date" 
                               name="tanggal_pengajuan" 
                               class="form-control @error('tanggal_pengajuan') is-invalid @enderror" 
                               value="{{ old('tanggal_pengajuan', $pinjaman->tanggal_pengajuan) }}"
                               required>
                        @error('tanggal_pengajuan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tanggal Cair --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Tanggal Cair</label>
                        <input type="date" 
                               name="tanggal_cair" 
                               class="form-control @error('tanggal_cair') is-invalid @enderror" 
                               value="{{ old('tanggal_cair', $pinjaman->tanggal_cair) }}"
                               required>
                        @error('tanggal_cair')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- ============================================ --}}
                    {{-- STATUS --}}
                    {{-- ============================================ --}}
                    <div class="col-12 mb-3">
                        <h6 class="fw-bold text-primary">
                            <i class="fas fa-tag me-2"></i>
                            Status Pinjaman
                        </h6>
                        <hr>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="menunggu" {{ old('status', $pinjaman->status) == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                            <option value="aktif" {{ old('status', $pinjaman->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="lunas" {{ old('status', $pinjaman->status) == 'lunas' ? 'selected' : '' }}>Lunas</option>
                            <option value="ditolak" {{ old('status', $pinjaman->status) == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Ubah status untuk mengelola pinjaman
                        </small>
                    </div>

                    {{-- ============================================ --}}
                    {{-- TOMBOL --}}
                    {{-- ============================================ --}}
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg px-5">
                            <i class="fas fa-save me-2"></i>
                            Update Pinjaman
                        </button>
                        <a href="{{ route('pinjaman.index') }}" class="btn btn-secondary btn-lg px-5 ms-2">
                            <i class="fas fa-times me-2"></i>
                            Batal
                        </a>
                        @if($pinjaman->status == 'menunggu')
                            <button type="reset" class="btn btn-warning btn-lg px-5 ms-2">
                                <i class="fas fa-undo me-2"></i>
                                Reset
                            </button>
                        @endif
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- INFORMASI TAMBAHAN --}}
    {{-- ============================================ --}}
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card border-0 shadow rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold text-primary mb-0">
                        <i class="fas fa-file-invoice me-2"></i>
                        Detail Pinjaman
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="150">Jumlah Pinjaman</td>
                            <td>: <strong>Rp {{ number_format($pinjaman->jumlah_pinjam, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <td>Biaya Administrasi</td>
                            <td>: Rp {{ number_format($pinjaman->nominal_adm, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Nominal Dicairkan</td>
                            <td>: <strong class="text-success">Rp {{ number_format($pinjaman->nominal_dicairkan, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <td>Total Tagihan</td>
                            <td>: Rp {{ number_format($pinjaman->total_pinjaman, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Sisa Tagihan</td>
                            <td>: <strong class="text-danger">Rp {{ number_format($pinjaman->sisa_tagihan, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <td>Tenor</td>
                            <td>: {{ $pinjaman->tenor }} Minggu</td>
                        </tr>
                        <tr>
                            <td>Angsuran/Minggu</td>
                            <td>: Rp {{ number_format($pinjaman->angsuran_per_minggu, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Jatuh Tempo</td>
                            <td>: {{ \Carbon\Carbon::parse($pinjaman->jatuh_tempo)->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td>Minggu Berjalan</td>
                            <td>: {{ $pinjaman->minggu_berjalan }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold text-primary mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informasi Nasabah
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="150">Nama</td>
                            <td>: <strong>{{ $pinjaman->nasabah->nama ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td>Kode Nasabah</td>
                            <td>: {{ $pinjaman->nasabah->kode_nasabah ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>NIK</td>
                            <td>: {{ $pinjaman->nasabah->nik ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Alamat</td>
                            <td>: {{ $pinjaman->nasabah->alamat ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>No HP</td>
                            <td>: {{ $pinjaman->nasabah->no_hp ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>DC</td>
                            <td>: {{ $pinjaman->nasabah->dc->nama_dc ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Wilayah</td>
                            <td>: {{ $pinjaman->nasabah->wilayah->nama_wilayah ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
    .card {
        border-radius: 1rem !important;
    }
    .form-control, .form-select {
        border-radius: 0.5rem !important;
        padding: 0.6rem 1rem;
    }
    .form-control:focus, .form-select:focus {
        border-color: #6610f2;
        box-shadow: 0 0 0 0.2rem rgba(102, 16, 242, 0.25);
    }
    .form-control:read-only {
        background-color: #f8f9fa;
        cursor: not-allowed;
    }
    .form-select:disabled {
        background-color: #f8f9fa;
        cursor: not-allowed;
    }
    .btn {
        border-radius: 0.5rem !important;
    }
    .btn-lg {
        padding: 0.6rem 1.5rem;
    }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto calculate saat input berubah
        const form = document.getElementById('formPinjaman');
        const jumlahPinjam = document.getElementById('jumlah_pinjam');
        const nominalAdm = document.getElementById('nominal_adm');
        const tenor = document.getElementById('tenor');
        const angsuran = document.getElementById('angsuran_per_minggu');
        const paketSelect = document.querySelector('select[name="paket_pinjaman_id"]');

        function updateAutoCalculate() {
            // Auto calculate dari paket pinjaman
            if (paketSelect) {
                const selected = paketSelect.options[paketSelect.selectedIndex];
                if (selected && selected.dataset.nominal) {
                    const pinjam = parseInt(selected.dataset.nominal) || 0;
                    const adm = parseInt(selected.dataset.adm) || 0;
                    const tenorVal = parseInt(selected.dataset.tenor) || 0;
                    const angsuranVal = parseInt(selected.dataset.angsuran) || 0;

                    if (jumlahPinjam) jumlahPinjam.value = pinjam;
                    if (nominalAdm) nominalAdm.value = adm;
                    if (tenor) tenor.value = tenorVal;
                    if (angsuran) angsuran.value = angsuranVal;

                    updateDisplay(pinjam, adm, tenorVal, angsuranVal);
                }
            }

            // Manual update
            const pinjam = parseInt(jumlahPinjam?.value) || 0;
            const adm = parseInt(nominalAdm?.value) || 0;
            const tenorVal = parseInt(tenor?.value) || 0;
            const angsuranVal = parseInt(angsuran?.value) || 0;

            updateDisplay(pinjam, adm, tenorVal, angsuranVal);
        }

        function updateDisplay(pinjam, adm, tenorVal, angsuranVal) {
            const dicairkan = pinjam - adm;
            const total = angsuranVal * tenorVal;
            const sisa = total;

            document.getElementById('display_dicairkan').textContent = 'Rp ' + 
                dicairkan.toLocaleString('id-ID').replace(/,/g, '.');

            document.getElementById('display_total').textContent = 'Rp ' + 
                total.toLocaleString('id-ID').replace(/,/g, '.');

            document.getElementById('display_sisa').textContent = 'Rp ' + 
                sisa.toLocaleString('id-ID').replace(/,/g, '.');
        }

        // Event listeners
        if (paketSelect) {
            paketSelect.addEventListener('change', updateAutoCalculate);
        }

        if (jumlahPinjam) jumlahPinjam.addEventListener('input', updateAutoCalculate);
        if (nominalAdm) nominalAdm.addEventListener('input', updateAutoCalculate);
        if (tenor) tenor.addEventListener('input', updateAutoCalculate);
        if (angsuran) angsuran.addEventListener('input', updateAutoCalculate);

        // Initial update
        updateAutoCalculate();
    });
</script>
@endpush
@endsection