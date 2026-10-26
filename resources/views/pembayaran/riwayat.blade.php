@extends('layouts.app')

@section('title', 'Riwayat Pembayaran')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4 overflow-hidden">
        <div class="card-body text-white position-relative" style="background: linear-gradient(135deg, #0d6efd, #198754);">
            <div class="position-absolute top-0 end-0 opacity-10" style="font-size: 120px; right: 20px; top: -20px;">
                <i class="fas fa-history"></i>
            </div>
            <div class="d-flex justify-content-between align-items-center position-relative">
                <div>
                    <h2 class="fw-bold mb-1">
                        <i class="fas fa-history me-2"></i>
                        Riwayat Pembayaran
                    </h2>
                    <p class="mb-0 opacity-75">
                        <i class="fas fa-database me-1"></i>
                        Total {{ number_format($pembayaran->total()) }} transaksi pembayaran
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('pembayaran.index') }}" class="btn btn-light fw-semibold shadow-sm rounded-pill px-4 py-2 hover-scale">
                        <i class="fas fa-arrow-left me-2"></i>
                        Kembali
                    </a>
                    <button onclick="window.print()" class="btn btn-outline-light fw-semibold rounded-pill px-4 py-2 hover-scale">
                        <i class="fas fa-print me-2"></i>
                        Cetak
                    </button>
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

    {{-- STATISTIK CARD --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Total Bayar</span>
                            <h4 class="fw-bold mb-0 text-success">
                                Rp {{ number_format($pembayaran->sum('jumlah_bayar'), 0, ',', '.') }}
                            </h4>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-money-bill-wave text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Total Pokok</span>
                            <h4 class="fw-bold mb-0 text-primary">
                                Rp {{ number_format($pembayaran->sum('pokok'), 0, ',', '.') }}
                            </h4>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-file-invoice text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Total Margin</span>
                            <h4 class="fw-bold mb-0 text-warning">
                                Rp {{ number_format($pembayaran->sum('margin'), 0, ',', '.') }}
                            </h4>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-percentage text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Total Transaksi</span>
                            <h4 class="fw-bold mb-0 text-info">
                                {{ number_format($pembayaran->total()) }}
                            </h4>
                        </div>
                        <div class="bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-receipt text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER CARD --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form action="{{ route('pembayaran.riwayat') }}" method="GET" id="filterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
    <label class="form-label small fw-bold text-muted">
        <i class="fas fa-search me-1"></i> Cari Nasabah
    </label>
    <div class="input-group">
        <span class="input-group-text bg-light border-0">
            <i class="fas fa-user"></i>
        </span>
        <input type="text" 
               name="search" 
               class="form-control form-control-sm rounded-3" 
               placeholder="Nama atau NIK..."
               value="{{ request('search') }}"
               onchange="this.form.submit()">
    </div>
</div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">
                            <i class="fas fa-calendar-alt me-1"></i> Dari
                        </label>
                        <input type="date" 
                               name="start_date" 
                               class="form-control form-control-sm rounded-3" 
                               value="{{ request('start_date') }}"
                               onchange="this.form.submit()">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">
                            <i class="fas fa-calendar-alt me-1"></i> Sampai
                        </label>
                        <input type="date" 
                               name="end_date" 
                               class="form-control form-control-sm rounded-3" 
                               value="{{ request('end_date') }}"
                               onchange="this.form.submit()">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">
                            <i class="fas fa-store me-1"></i> DC
                        </label>
                        <select name="dc_id" class="form-select form-select-sm rounded-3" onchange="this.form.submit()">
                            <option value="">Semua DC</option>
                            @foreach($dcList ?? [] as $dc)
                            <option value="{{ $dc->id }}" {{ request('dc_id') == $dc->id ? 'selected' : '' }}>
                                {{ $dc->nama_dc }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm rounded-3 flex-grow-1">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            <a href="{{ route('pembayaran.riwayat') }}" class="btn btn-outline-secondary btn-sm rounded-3">
                                <i class="fas fa-undo me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- TABLE CARD --}}
    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="riwayatTable">
                    <thead class="table-light">
                        <tr>
                            <th width="50" class="ps-3">#</th>
                            <th width="120">Tanggal</th>
                            <th width="180">Nasabah</th>
                            <th width="120">DKH</th>
                            <th class="text-center" width="100">Angsuran</th>
                            <th class="text-end" width="140">Tagihan</th>
                            <th class="text-end" width="140">Bayar</th>
                            <th class="text-end" width="130">Pokok</th>
                            <th class="text-end" width="130">Margin</th>
                            <th class="text-center" width="100">Metode</th>
                            <th class="text-center" width="120">Status</th>
                            <th class="text-center" width="80">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pembayaran as $key => $item)
                        @php
                            $nasabah = $item->angsuran->pinjaman->nasabah ?? null;
                            $pinjaman = $item->angsuran->pinjaman ?? null;
                            $dkh = $item->dkhDetail->dkh ?? null;
                        @endphp
                        <tr class="riwayat-row" data-id="{{ $item->id }}">
                            <td class="ps-3">
                                <span class="fw-bold text-primary">{{ $loop->iteration }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold">
                                    {{ \Carbon\Carbon::parse($item->tanggal_bayar)->format('d/m/Y') }}
                                </span>
                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ \Carbon\Carbon::parse($item->tanggal_bayar)->format('H:i') }}
                                </small>
                            </td>
                            <td>
                                @if($nasabah)
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2" 
                                         style="width: 32px; height: 32px;">
                                        <span class="fw-bold text-primary">{{ substr($nasabah->nama ?? 'N', 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <span class="fw-semibold">{{ $nasabah->nama ?? '-' }}</span>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-id-card me-1"></i>
                                            {{ $nasabah->nik ?? '-' }}
                                        </small>
                                    </div>
                                </div>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-3 py-2">
                                    <i class="fas fa-file-invoice me-1"></i>
                                    {{ $dkh->nomor_dkh ?? '-' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary bg-opacity-10 text-dark px-3 py-2">
                                    Ke-{{ $item->angsuran->minggu_ke ?? '-' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <span class="text-muted">
                                    Rp {{ number_format($item->jumlah_tagihan, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="text-end">
                                <span class="fw-bold text-success">
                                    Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="text-end">
                                <span class="text-primary">
                                    Rp {{ number_format($item->pokok ?? 0, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="text-end">
                                <span class="text-warning">
                                    Rp {{ number_format($item->margin ?? 0, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($item->metode_bayar == 'kas')
                                    <span class="badge bg-success rounded-pill px-3 py-2">
                                        <i class="fas fa-coins me-1"></i>
                                        Kas
                                    </span>
                                @else
                                    <span class="badge bg-primary rounded-pill px-3 py-2">
                                        <i class="fas fa-university me-1"></i>
                                        Transfer
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($item->angsuran && $item->angsuran->status == 'lunas')
                                    <span class="badge bg-success rounded-pill px-3 py-2">
                                        <i class="fas fa-check-circle me-1"></i>
                                        Lunas
                                    </span>
                                @else
                                    <span class="badge bg-warning rounded-pill px-3 py-2">
                                        <i class="fas fa-clock me-1"></i>
                                        Proses
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <button class="btn btn-sm btn-outline-info rounded-3" 
                                            onclick="toggleDetail({{ $item->id }})"
                                            data-bs-toggle="tooltip" 
                                            title="Lihat Detail">
                                        <i class="fas fa-chevron-down" id="icon-{{ $item->id }}"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning rounded-3" 
                                            onclick="window.print()"
                                            data-bs-toggle="tooltip" 
                                            title="Cetak">
                                        <i class="fas fa-print"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        {{-- DETAIL ROW (Expandable) --}}
                        <tr class="detail-row" id="detail-{{ $item->id }}" style="display: none;">
                            <td colspan="12" class="p-0">
                                <div class="bg-light p-4 mx-3 my-2 rounded-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="fw-bold text-primary mb-3">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Detail Transaksi
                                            </h6>
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <td width="140" class="fw-bold">ID Transaksi</td>
                                                    <td>: <span class="badge bg-secondary">{{ $item->id }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Tanggal Bayar</td>
                                                    <td>: {{ \Carbon\Carbon::parse($item->tanggal_bayar)->format('d/m/Y H:i:s') }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Nasabah</td>
                                                    <td>: {{ $nasabah->nama ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Alamat</td>
                                                    <td>: {{ $nasabah->alamat ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">DKH</td>
                                                    <td>: {{ $dkh->nomor_dkh ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">DC</td>
                                                    <td>: {{ $dkh->dc->nama_dc ?? '-' }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="fw-bold text-success mb-3">
                                                <i class="fas fa-money-bill-wave me-2"></i>
                                                Detail Pembayaran
                                            </h6>
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <td width="140" class="fw-bold">Angsuran Ke</td>
                                                    <td>: Ke-{{ $item->angsuran->minggu_ke ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Jumlah Tagihan</td>
                                                    <td>: Rp {{ number_format($item->jumlah_tagihan, 0, ',', '.') }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Jumlah Bayar</td>
                                                    <td>: <span class="text-success fw-bold">Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Pokok</td>
                                                    <td>: <span class="text-primary">Rp {{ number_format($item->pokok ?? 0, 0, ',', '.') }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Margin</td>
                                                    <td>: <span class="text-warning">Rp {{ number_format($item->margin ?? 0, 0, ',', '.') }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Metode Bayar</td>
                                                    <td>: {{ ucfirst($item->metode_bayar) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Keterangan</td>
                                                    <td>: {{ $item->keterangan ?? '-' }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-4x mb-3 d-block opacity-25"></i>
                                    <h5 class="fw-bold">Belum Ada Data Pembayaran</h5>
                                    <p class="mb-0">Belum ada transaksi pembayaran yang tercatat.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($pembayaran->isNotEmpty())
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="6" class="ps-3">
                                <i class="fas fa-calculator me-2"></i>
                                TOTAL KESELURUHAN
                            </td>
                            <td class="text-end text-success">
                                Rp {{ number_format($pembayaran->sum('jumlah_bayar'), 0, ',', '.') }}
                            </td>
                            <td class="text-end text-primary">
                                Rp {{ number_format($pembayaran->sum('pokok'), 0, ',', '.') }}
                            </td>
                            <td class="text-end text-warning">
                                Rp {{ number_format($pembayaran->sum('margin'), 0, ',', '.') }}
                            </td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>

        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Menampilkan {{ $pembayaran->firstItem() ?? 0 }} - {{ $pembayaran->lastItem() ?? 0 }} 
                        dari {{ $pembayaran->total() }} data
                    </small>
                    <br>
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        Terakhir diperbarui: {{ now()->format('d/m/Y H:i:s') }}
                    </small>
                </div>
                <div>
                    {{ $pembayaran->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>

</div>

<style>
    .hover-scale {
        transition: transform 0.2s ease;
    }
    .hover-scale:hover {
        transform: scale(1.02);
    }
    .avatar-sm {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
    .table > :not(caption) > * > * {
        padding: 10px 8px;
        vertical-align: middle;
    }
    .card {
        border: none !important;
    }
    .card-header {
        border-bottom: 1px solid #f0f0f0 !important;
    }
    .rounded-4 {
        border-radius: 1rem !important;
    }
    .badge {
        font-weight: 600;
    }
    .table-light th {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        color: #6c757d;
        border-bottom: 2px solid #e9ecef;
    }
    tfoot td {
        font-weight: 700;
        background-color: #f8f9fa;
        border-top: 2px solid #dee2e6;
        padding: 12px 8px;
    }
    .riwayat-row {
        transition: background-color 0.2s ease;
        cursor: pointer;
    }
    .riwayat-row:hover {
        background-color: #f0f7ff !important;
    }
    .detail-row td {
        background-color: #f8f9fa;
    }
    .form-select-sm, .form-control-sm {
        border-color: #e0e0e0;
    }
    .form-select-sm:focus, .form-control-sm:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
    }
    .table-sm td {
        padding: 4px 8px;
    }
    .table-sm tr:last-child td {
        border-bottom: none;
    }
    .btn-group-sm > .btn {
        padding: 0.2rem 0.5rem;
        font-size: 0.75rem;
    }
    
    @media print {
        .btn, .btn-group, .card-tools, .no-print {
            display: none !important;
        }
        .card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
        .table-light th {
            background-color: #e9ecef !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .badge {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .detail-row {
            display: table-row !important;
        }
    }
</style>

@endsection

@push('scripts')
<script>
    // Inisialisasi tooltip
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    // Toggle Detail Row
    function toggleDetail(id) {
        var detailRow = document.getElementById('detail-' + id);
        var icon = document.getElementById('icon-' + id);
        
        if (detailRow.style.display === 'none') {
            detailRow.style.display = 'table-row';
            icon.className = 'fas fa-chevron-up';
            // Sembunyikan detail lain
            document.querySelectorAll('.detail-row').forEach(function(row) {
                if (row.id !== 'detail-' + id) {
                    row.style.display = 'none';
                    var otherIcon = document.getElementById('icon-' + row.id.replace('detail-', ''));
                    if (otherIcon) {
                        otherIcon.className = 'fas fa-chevron-down';
                    }
                }
            });
        } else {
            detailRow.style.display = 'none';
            icon.className = 'fas fa-chevron-down';
        }
    }

    // Export Data
    function exportData() {
        // Ambil parameter filter saat ini
        var params = new URLSearchParams(window.location.search);
        var url = "{{ route('pembayaran.export') }}?" + params.toString();
        window.open(url, '_blank');
    }

    // Auto submit form on change
    document.querySelectorAll('select[onchange], input[onchange]').forEach(function(element) {
        element.addEventListener('change', function() {
            this.form.submit();
        });
    });

    // Keyboard shortcut: ESC to close detail
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.detail-row').forEach(function(row) {
                row.style.display = 'none';
                var icon = document.getElementById('icon-' + row.id.replace('detail-', ''));
                if (icon) {
                    icon.className = 'fas fa-chevron-down';
                }
            });
        }
    });
</script>
@endpush