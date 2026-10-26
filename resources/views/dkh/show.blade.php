@extends('layouts.app')

@section('title', 'Detail DKH - ' . $dkh->nomor_dkh)

@section('content')

<div class="container-fluid px-3 px-md-4">

    {{-- HEADER & TOMBOL AKSI --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-4">
        <div>
            <h3 class="mb-0 fw-bold text-dark">
                <i class="fas fa-file-invoice text-primary me-2"></i>Detail DKH
            </h3>
            <small class="text-muted">
                <i class="fas fa-tag me-1"></i>Daftar Kunjungan Harian Collector
            </small>
        </div>
        <div class="d-flex gap-2 w-100 w-md-auto">
            <a href="{{ route('dkh.cetak', $dkh->id) }}" target="_blank" class="btn btn-primary btn-sm rounded-3 px-3 shadow-sm flex-fill flex-md-grow-0">
                <i class="fas fa-print me-1"></i> Cetak DKH
            </a>
            <a href="{{ route('dkh.index', ['minggu' => $dkh->minggu_ke, 'tahun' => $dkh->tahun]) }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3 shadow-sm flex-fill flex-md-grow-0">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    {{-- ALERT NOTIFIKASI --}}
    @foreach (['success' => 'check-circle', 'error' => 'exclamation-circle', 'info' => 'info-circle'] as $msg => $icon)
        @if(session($msg))
            <div class="alert alert-{{ $msg == 'error' ? 'danger' : $msg }} border-0 shadow-sm rounded-3 alert-dismissible fade show">
                <i class="fas fa-{{ $icon }} me-2"></i>{{ session($msg) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    {{-- RANGKUMAN STATISTIK DKH --}}
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-primary bg-gradient text-white rounded-top-4 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="fas fa-info-circle me-2"></i>Informasi DKH</span>
                <span class="badge bg-white text-primary fw-bold px-3 py-1 rounded-pill">{{ strtoupper($dkh->status) }}</span>
            </div>
        </div>
        <div class="card-body p-3 p-md-4">
            <div class="row g-3">
                <div class="col-6 col-md-3 border-end-md">
                    <small class="text-muted d-block fw-semibold">Nomor DKH</small>
                    <span class="fw-bold text-dark fs-6">{{ $dkh->nomor_dkh }}</span>
                </div>
                <div class="col-6 col-md-3 border-end-md">
                    <small class="text-muted d-block fw-semibold">Tanggal</small>
                    <span class="fw-bold text-dark fs-6">{{ \Carbon\Carbon::parse($dkh->tanggal_dkh)->format('d-m-Y') }}</span>
                </div>
                <div class="col-6 col-md-3 border-end-md">
                    <small class="text-muted d-block fw-semibold">Collector / DC</small>
                    <span class="fw-bold text-dark fs-6">{{ $dkh->dc->nama_dc ?? '-' }}</span>
                </div>
                <div class="col-6 col-md-3">
                    <small class="text-muted d-block fw-semibold">Total Nasabah</small>
                    <span class="fw-bold text-dark fs-6">{{ $dkh->total_nasabah }} Orang</span>
                </div>
            </div>

            <hr class="my-3 text-muted opacity-25">

            <div class="row g-2 text-center text-md-start">
                <div class="col-6 col-sm-3 col-md">
                    <small class="text-muted d-block">JB</small>
                    <strong class="text-dark">Rp {{ number_format($dkh->total_jb,0,',','.') }}</strong>
                </div>
                <div class="col-6 col-sm-3 col-md">
                    <small class="text-muted d-block">Angsuran</small>
                    <strong class="text-dark">Rp {{ number_format($dkh->total_angsuran,0,',','.') }}</strong>
                </div>
                <div class="col-6 col-sm-3 col-md">
                    <small class="text-muted d-block">Denda</small>
                    <strong class="text-dark">Rp {{ number_format($dkh->total_denda,0,',','.') }}</strong>
                </div>
                <div class="col-6 col-sm-3 col-md">
                    <small class="text-muted d-block">Grand Total</small>
                    <strong class="text-danger fs-5">Rp {{ number_format($dkh->total_tagihan,0,',','.') }}</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL DAFTAR TAGIHAN & CARI --}}
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white border-0 pt-4 pb-3">
            <div class="row g-2 align-items-center justify-content-between">
                <div class="col-12 col-md-4">
                    <strong class="text-primary fs-6">
                        <i class="fas fa-list me-2"></i>Daftar Tagihan ({{ $dkh->detail->count() }})
                    </strong>
                </div>
                {{-- SEARCH BAR --}}
                <div class="col-12 col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-pill text-muted px-3">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" id="searchNasabah" class="form-control bg-light border-start-0 rounded-end-pill ps-0" placeholder="Cari nama, kode nasabah, atau alamat...">
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                <table class="table table-hover align-middle mb-0" id="tableTagihan" style="font-size: 12.5px;">
                    <thead class="table-dark sticky-top" style="z-index: 5;">
                        <tr>
                            <th class="text-center" width="40">No</th>
                            <th>Nasabah</th>
                            <th>Alamat</th>
                            <th class="text-center" width="60">Bayar</th>
                            <th class="text-center" width="50">Tenor</th>
                            <th class="text-end" width="85">JB</th>
                            <th class="text-end" width="95">Angsuran</th>
                            <th class="text-end" width="85">Denda</th>
                            <th class="text-end" width="105">Total</th>
                            <th class="text-center" width="80">Status</th>
                            <th class="text-center" width="130">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyTagihan">
                        @forelse($dkh->detail as $item)
                        <tr class="baris-tagihan">
                            <td class="text-center fw-semibold text-muted">{{ $loop->iteration }}</td>
                            
                            {{-- NASABAH --}}
                            <td>
                                <div class="fw-bold text-dark cari-target">{{ $item->nasabah->nama ?? '-' }}</div>
                                <div class="text-muted fs-11 cari-target">
                                    <i class="fas fa-id-card me-1 text-primary"></i>{{ $item->nasabah->kode_nasabah ?? '-' }}
                                </div>
                                <div class="text-secondary fs-11">
                                    <i class="fas fa-wallet me-1 text-success"></i>Rp {{ number_format($item->pinjaman->jumlah_pinjam ?? 0,0,',','.') }}
                                </div>
                            </td>
                            
                            {{-- ALAMAT --}}
                            <td>
                                <div class="text-dark cari-target text-truncate" style="max-width: 180px;" title="{{ $item->nasabah->alamat ?? '-' }}">
                                    <i class="fas fa-map-marker-alt text-danger me-1"></i>{{ $item->nasabah->alamat ?? '-' }}
                                </div>
                                @if(!empty($item->nasabah->kelurahan) || !empty($item->nasabah->kecamatan))
                                    <div class="text-muted fs-11 text-truncate" style="max-width: 180px;">
                                        {{ $item->nasabah->kelurahan ?? '' }} {{ $item->nasabah->kecamatan ? ', '.$item->nasabah->kecamatan : '' }}
                                    </div>
                                @endif
                                @if(!empty($item->nasabah->rt) || !empty($item->nasabah->rw))
                                    <div class="text-muted fs-11">RT {{ $item->nasabah->rt ?? '-' }} / RW {{ $item->nasabah->rw ?? '-' }}</div>
                                @endif
                            </td>
                            
                            <td class="text-center fw-bold text-primary">Ke-{{ $item->bayar_ke }}</td>
                            <td class="text-center">{{ $item->jumlah_minggu }}</td>
                            <td class="text-end">Rp {{ number_format($item->jb,0,',','.') }}</td>
                            <td class="text-end">Rp {{ number_format($item->angsuran,0,',','.') }}</td>
                            <td class="text-end">Rp {{ number_format($item->denda,0,',','.') }}</td>
                            <td class="text-end fw-bold text-danger">Rp {{ number_format($item->total_tagihan,0,',','.') }}</td>
                            
                            {{-- STATUS --}}
                            <td class="text-center">
                                @if($item->status == 'belum_bayar')
                                    <span class="badge bg-danger-subtle text-danger border border-danger px-2 py-1 rounded-pill">Belum</span>
                                @elseif($item->status == 'kurang_bayar')
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning px-2 py-1 rounded-pill">Kurang</span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success px-2 py-1 rounded-pill">Lunas</span>
                                @endif
                            </td>

                            {{-- AKSI --}}
                            <td class="text-center">
                                @if($item->status != 'lunas')
                                    <div class="d-flex flex-column gap-1">
                                        <button type="button" 
                                                class="btn btn-sm btn-success rounded-pill btn-bayar shadow-sm px-2 py-1"
                                                style="font-size: 11px;"
                                                data-id="{{ $item->id }}"
                                                data-nasabah="{{ $item->nasabah->nama ?? '-' }}"
                                                data-kode="{{ $item->nasabah->kode_nasabah ?? '-' }}"
                                                data-tagihan="Rp {{ number_format($item->sisa_bayar, 0, ',', '.') }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalBayar">
                                            <i class="fas fa-cash-register me-1"></i> Bayar
                                        </button>
                                        <a href="{{ route('pembayaran.bayar-multiple.form', $item->pinjaman_id) }}" 
                                           class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1"
                                           style="font-size: 11px;">
                                            <i class="fas fa-layer-group me-1"></i> Multiple
                                        </a>
                                    </div>
                                @else
                                    <span class="text-success fw-semibold"><i class="fas fa-check-circle me-1"></i>Selesai</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr id="emptyRow">
                            <td colspan="11" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                                    <h6 class="fw-bold">Tidak Ada Data Tagihan</h6>
                                    <p class="mb-0">Belum ada tagihan yang tercatat di DKH ini.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                        <tr id="noResultRow" class="d-none">
                            <td colspan="11" class="text-center py-4 text-muted">
                                <i class="fas fa-search me-2"></i>Tidak ada nasabah yang cocok dengan pencarian.
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="table-light fw-bold sticky-bottom" style="z-index: 5;">
                        <tr style="font-size: 12.5px;">
                            <td colspan="5" class="text-end">TOTAL:</td>
                            <td class="text-end">Rp {{ number_format($dkh->total_jb,0,',','.') }}</td>
                            <td class="text-end">Rp {{ number_format($dkh->total_angsuran,0,',','.') }}</td>
                            <td class="text-end">Rp {{ number_format($dkh->total_denda,0,',','.') }}</td>
                            <td class="text-end text-danger">Rp {{ number_format($dkh->total_tagihan,0,',','.') }}</td>
                            <td colspan="2" class="text-center">
                                <span class="badge bg-secondary px-3 py-1 rounded-pill">{{ $dkh->total_nasabah }} Nasabah</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- MODAL KONFIRMASI BAYAR --}}
<div class="modal fade" id="modalBayar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-success text-white border-0 py-3">
                <h6 class="modal-title fw-bold">
                    <i class="fas fa-money-bill-wave me-2"></i>Konfirmasi Pembayaran
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="formBayar" method="POST" action="">
                @csrf
                <div class="modal-body p-4">
                    
                    <div class="card border-0 bg-light rounded-3 mb-3 p-3">
                        <div class="row g-2">
                            <div class="col-7">
                                <small class="text-muted d-block">Nama Nasabah</small>
                                <strong id="modalNasabah" class="text-dark fs-6">-</strong>
                                <small class="text-muted d-block id-code" id="modalKode">-</small>
                            </div>
                            <div class="col-5 text-end">
                                <small class="text-muted d-block">Jumlah Tagihan</small>
                                <strong id="modalTagihan" class="text-danger fs-6">-</strong>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Metode Pembayaran</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="radio" name="metode_bayar" value="kas" id="metodeKas" class="btn-check" checked>
                                <label for="metodeKas" class="btn btn-outline-success w-100 py-2 rounded-3 text-start">
                                    <i class="fas fa-money-bill me-1"></i> <strong>Kas / Tunai</strong>
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" name="metode_bayar" value="transfer" id="metodeTransfer" class="btn-check">
                                <label for="metodeTransfer" class="btn btn-outline-primary w-100 py-2 rounded-3 text-start">
                                    <i class="fas fa-university me-1"></i> <strong>Transfer</strong>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold small text-muted">Keterangan (Opsional)</label>
                        <input type="text" name="keterangan" class="form-control rounded-3" placeholder="Catatan pembayaran...">
                    </div>

                </div>

                <div class="modal-footer border-0 bg-light py-3 px-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 btn-sm fw-bold">
                        <i class="fas fa-check-circle me-1"></i> Proses Bayar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .fs-11 { font-size: 11px; }
    .card { border-radius: 1rem !important; }
    .rounded-4 { border-radius: 1rem !important; }
    
    /* Smooth Scrollbar & Table Fixes */
    .table-responsive::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .table-responsive::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 4px;
    }
    
    /* Search highlighting */
    .btn-check:checked + .btn-outline-success {
        background-color: #198754;
        color: white;
    }
    .btn-check:checked + .btn-outline-primary {
        background-color: #0d6efd;
        color: white;
    }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const formBayar = document.getElementById('formBayar');
        const searchInput = document.getElementById('searchNasabah');
        const rows = document.querySelectorAll('.baris-tagihan');
        const noResultRow = document.getElementById('noResultRow');

        // 1. Fitur Pencarian Realtime
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const keyword = this.value.toLowerCase().trim();
                let hasMatch = false;

                rows.forEach(row => {
                    const targets = row.querySelectorAll('.cari-target');
                    let textContent = '';
                    targets.forEach(t => textContent += t.textContent.toLowerCase() + ' ');

                    if (textContent.includes(keyword)) {
                        row.classList.remove('d-none');
                        hasMatch = true;
                    } else {
                        row.classList.add('d-none');
                    }
                });

                if (noResultRow) {
                    if (!hasMatch && rows.length > 0) {
                        noResultRow.classList.remove('d-none');
                    } else {
                        noResultRow.classList.add('d-none');
                    }
                }
            });
        }

        // 2. Modal Data Binding
        document.querySelectorAll('.btn-bayar').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const nasabah = this.dataset.nasabah || '-';
                const kode = this.dataset.kode || '-';
                const tagihan = this.dataset.tagihan || 'Rp 0';
                
                document.getElementById('modalNasabah').textContent = nasabah;
                document.getElementById('modalKode').textContent = kode;
                document.getElementById('modalTagihan').textContent = tagihan;
                
                formBayar.action = "{{ url('pembayaran/bayar-langsung') }}/" + id;
            });
        });
    });
</script>
@endpush

@endsection