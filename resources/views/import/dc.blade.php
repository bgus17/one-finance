@extends('layouts.app')

@section('title', 'Import Data DC')

@section('page-header')
<div class="page-header d-flex align-items-center">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col">
                <h4 class="page-title mb-0">
                    <i class="fas fa-file-import me-2 text-primary"></i>
                    Import Data DC
                </h4>
                <small class="text-muted">Import data nasabah dan pinjaman dari file Excel (Migrasi Data Awal)</small>
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
        <div class="col-lg-10">

            {{-- ALERT SUCCESS --}}
            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm rounded-3 alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i>
                    {!! session('success') !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- ALERT ERROR --}}
            @if(session('error'))
                <div class="alert alert-danger border-0 shadow-sm rounded-3 alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {!! session('error') !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- VALIDATION ERRORS --}}
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

            {{-- CARD UTAMA --}}
            <div class="card border-0 shadow-lg rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-upload me-2"></i>
                        Upload File Excel DC
                    </h5>
                </div>
                <div class="card-body p-4">

                    {{-- FORM --}}
                    <form action="{{ route('import.dc.post') }}" method="POST" enctype="multipart/form-data" id="formImport">
                        @csrf

                        {{-- PILIH DC --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pilih DC <span class="text-danger">*</span></label>
                            <select name="dc_id" class="form-select" required id="dcSelect">
                                <option value="">-- Pilih DC --</option>
                                @foreach($dcList as $dc)
                                    <option value="{{ $dc->id }}">{{ $dc->nama_dc }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Semua data akan diimport ke DC ini</small>
                        </div>

                        {{-- FILE UPLOAD --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">File Excel DC <span class="text-danger">*</span></label>
                            <input type="file" 
                                   name="file" 
                                   class="form-control @error('file') is-invalid @enderror" 
                                   accept=".xlsx,.xls,.csv" 
                                   id="fileInput"
                                   required>
                            <small class="text-muted">Format: .xlsx, .xls, atau .csv (Max 20MB)</small>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- PREVIEW NAMA FILE --}}
                        <div id="filePreview" class="d-none mb-3">
                            <div class="alert alert-info border-0 shadow-sm rounded-3">
                                <i class="fas fa-file-excel me-2 text-success"></i>
                                <strong>File dipilih:</strong> <span id="fileName"></span>
                                <br>
                                <small class="text-muted">Klik "Import Data" untuk memulai proses import</small>
                            </div>
                        </div>

                        {{-- INFORMASI FORMAT EXCEL --}}
                        <div class="alert alert-warning border-0 shadow-sm rounded-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Pastikan Format Excel Sesuai:</strong>
                            <ul class="mb-0 mt-2 ps-3">
                                <li><strong>Kolom yang wajib diisi:</strong> NAMA, KODE DESA, PINJAMAN, JANGKA WAKTU</li>
                                <li><strong>Kolom ANGSURAN KE:</strong> Menunjukkan minggu berjalan pinjaman</li>
                                <li><strong>Jika ANGSURAN KE = 0:</strong> Pinjaman dianggap baru cair</li>
                                <li><strong>Jika ANGSURAN KE > 0:</strong> Pinjaman sudah berjalan dan angsuran sebelumnya dianggap lunas</li>
                            </ul>
                        </div>

                        {{-- DATA YANG AKAN DIIMPORT --}}
                        <div class="alert alert-info border-0 shadow-sm rounded-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Yang akan diimport:</strong>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <ul class="mb-0 ps-3">
                                        <li><i class="fas fa-user text-success me-1"></i> <strong>Nasabah</strong> - Data nasabah (Nama, NIK, Alamat, Desa, No HP, Pekerjaan)</li>
                                        <li><i class="fas fa-hand-holding-usd text-success me-1"></i> <strong>Pinjaman</strong> - Data pinjaman (Nominal, Tenor, Tanggal Cair)</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="mb-0 ps-3">
                                        <li><i class="fas fa-calendar-check text-success me-1"></i> <strong>Angsuran</strong> - Otomatis digenerate sesuai tenor</li>
                                        <li><i class="fas fa-check-circle text-success me-1"></i> <strong>Status Angsuran</strong> - Otomatis ditandai lunas/belum berdasarkan ANGSURAN KE</li>
                                        <li><i class="fas fa-money-bill-wave text-success me-1"></i> <strong>Kas</strong> - Catat kas masuk & kas keluar</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- TOMBOL --}}
                        <div class="d-flex flex-wrap gap-2 mt-4">
                            <button type="submit" class="btn btn-success rounded-pill px-4" id="btnImport">
                                <i class="fas fa-upload me-1"></i> Import Data
                            </button>
                            <a href="{{ route('import.dc.template') }}" class="btn btn-outline-primary rounded-pill px-4">
                                <i class="fas fa-download me-1"></i> Download Template
                            </a>
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary rounded-pill px-4">
                                <i class="fas fa-arrow-left me-1"></i> Batal
                            </a>
                        </div>

                        {{-- LOADING SPINNER --}}
                        <div id="loadingSpinner" class="d-none text-center mt-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Sedang memproses data, mohon tunggu...</p>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                     role="progressbar" 
                                     style="width: 100%">
                                    Memproses...
                                </div>
                            </div>
                        </div>

                    </form>

                </div>
            </div>

            {{-- CARD BANTUAN --}}
            <div class="card border-0 shadow-sm rounded-3 mt-4">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-question-circle me-2 text-primary"></i>
                        Panduan Import Data
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary">📋 Format Kolom Excel</h6>
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kolom</th>
                                        <th>Keterangan</th>
                                        <th>Wajib</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>NAMA</strong></td>
                                        <td>Nama lengkap nasabah</td>
                                        <td class="text-success">✅</td>
                                    </tr>
                                    <tr>
                                        <td><strong>ALAMAT</strong></td>
                                        <td>Alamat nasabah</td>
                                        <td class="text-danger">❌</td>
                                    </tr>
                                    <tr>
                                        <td><strong>KODE DESA</strong></td>
                                        <td>Kode desa (1-22)</td>
                                        <td class="text-success">✅</td>
                                    </tr>
                                    <tr>
                                        <td><strong>PINJAMAN</strong></td>
                                        <td>Nominal pinjaman</td>
                                        <td class="text-success">✅</td>
                                    </tr>
                                    <tr>
                                        <td><strong>JANGKA WAKTU</strong></td>
                                        <td>Tenor (minggu)</td>
                                        <td class="text-success">✅</td>
                                    </tr>
                                    <tr>
                                        <td><strong>NIK</strong></td>
                                        <td>NIK nasabah</td>
                                        <td class="text-danger">❌</td>
                                    </tr>
                                    <tr>
                                        <td><strong>TTL</strong></td>
                                        <td>Tempat, Tanggal Lahir</td>
                                        <td class="text-danger">❌</td>
                                    </tr>
                                    <tr>
                                        <td><strong>PEKERJAAN</strong></td>
                                        <td>Pekerjaan nasabah</td>
                                        <td class="text-danger">❌</td>
                                    </tr>
                                    <tr>
                                        <td><strong>No. TELP</strong></td>
                                        <td>Nomor telepon</td>
                                        <td class="text-danger">❌</td>
                                    </tr>
                                    <tr>
                                        <td><strong>TGL PENCAIRAN</strong></td>
                                        <td>Tanggal cair pinjaman</td>
                                        <td class="text-danger">❌</td>
                                    </tr>
                                    <tr class="table-warning">
                                        <td><strong>ANGSURAN KE</strong></td>
                                        <td>Minggu berjalan (0 = baru cair)</td>
                                        <td class="text-warning">⚠️</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary">📌 Kode Desa</h6>
                            <div class="row">
                                <div class="col-6">
                                    <ul class="list-unstyled small">
                                        <li><strong>1</strong> = KARANG MANIK</li>
                                        <li><strong>2</strong> = MARGOMULYO</li>
                                        <li><strong>3</strong> = SUMBER RAHAYU</li>
                                        <li><strong>4</strong> = SRIBANTOLO</li>
                                        <li><strong>5</strong> = NUSA AGUNG</li>
                                        <li><strong>6</strong> = SINUMARGA</li>
                                        <li><strong>7</strong> = NUSA JAYA</li>
                                        <li><strong>8</strong> = NUSA BAKTI</li>
                                        <li><strong>12</strong> = NUSA TENGGARA</li>
                                    </ul>
                                </div>
                                <div class="col-6">
                                    <ul class="list-unstyled small">
                                        <li><strong>13</strong> = KARANG JADI</li>
                                        <li><strong>15</strong> = CAHYA MAS</li>
                                        <li><strong>16</strong> = SUMBER REJO</li>
                                        <li><strong>17</strong> = KARYA MAKMUR</li>
                                        <li><strong>18</strong> = SUKA JAYA</li>
                                        <li><strong>19</strong> = NUSA RAYA</li>
                                        <li><strong>20</strong> = NUSA BALI</li>
                                        <li><strong>21</strong> = SINAR BALI</li>
                                        <li><strong>22</strong> = KARYA MAJU</li>
                                    </ul>
                                </div>
                            </div>
                            <hr>
                            <h6 class="fw-bold text-primary">💡 Tips</h6>
                            <ul class="small">
                                <li>Gunakan template yang sudah disediakan</li>
                                <li>Pastikan format tanggal sesuai (YYYY-MM-DD)</li>
                                <li>Jika ada error, perbaiki data dan import ulang</li>
                                <li>Fitur ini hanya untuk migrasi data awal</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // 🔥 PREVIEW NAMA FILE
        $('#fileInput').on('change', function() {
            const fileName = $(this).val().split('\\').pop();
            if (fileName) {
                $('#fileName').text(fileName);
                $('#filePreview').removeClass('d-none');
            } else {
                $('#filePreview').addClass('d-none');
            }
        });

        // 🔥 VALIDASI SEBELUM SUBMIT
        $('#formImport').on('submit', function(e) {
            const dcId = $('#dcSelect').val();
            const fileInput = $('#fileInput')[0];

            if (!dcId) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih DC',
                    text: 'Silakan pilih DC terlebih dahulu!'
                });
                return false;
            }

            if (!fileInput.files || !fileInput.files[0]) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih File',
                    text: 'Silakan pilih file Excel terlebih dahulu!'
                });
                return false;
            }

            // 🔥 TAMPILKAN LOADING
            $('#btnImport').prop('disabled', true);
            $('#btnImport').html('<span class="spinner-border spinner-border-sm me-1"></span> Memproses...');
            $('#loadingSpinner').removeClass('d-none');
        });

        // 🔥 CEK SESSION WARNINGS
        @if(session('warnings') && count(session('warnings')) > 0)
            Swal.fire({
                icon: 'info',
                title: 'Info Import',
                html: `{!! implode('<br>', session('warnings')) !!}`,
                confirmButtonText: 'OK'
            });
        @endif

        // 🔥 CEK SESSION ERRORS
        @if(session('errors') && count(session('errors')) > 0)
            Swal.fire({
                icon: 'error',
                title: 'Error Import',
                html: `{!! implode('<br>', session('errors')) !!}`,
                confirmButtonText: 'OK'
            });
        @endif
    });
</script>
@endpush