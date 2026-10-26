@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="card shadow-lg border-0 rounded-4">
        
        <!-- Header -->
        <div class="card-header border-0 text-white rounded-top-4"
             style="background: linear-gradient(135deg, #0d6efd, #6610f2);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0 fw-bold">
                        <i class="fas fa-clipboard-list me-2"></i>Daftar DKH
                    </h3>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <small class="opacity-75">
                            Minggu ke-{{ $minggu }} - Tahun {{ $tahun }}
                        </small>
                        @if(isset($periode))
                            <span class="badge bg-white bg-opacity-25 text-white border border-white border-opacity-25 rounded-pill px-2 py-1">
                                <i class="fas fa-calendar-alt me-1"></i>{{ $periode['label'] }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="d-flex gap-2">
                    {{-- 🔥 TOMBOL SINKRONISASI --}}
                    <form action="{{ route('dkh.generate') }}" method="POST">
                        @csrf
                        <input type="hidden" name="minggu" value="{{ $minggu }}">
                        <input type="hidden" name="tahun" value="{{ $tahun }}">
                        <button type="submit" class="btn btn-light fw-semibold shadow-sm" title="Sinkronkan DKH dengan pencairan pinjaman terbaru">
                            <i class="fas fa-sync-alt me-1 text-primary"></i>
                            Sinkronisasi DKH
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="card-body">

            {{-- ALERT NOTIFIKASI --}}
            @foreach (['success' => 'check-circle', 'error' => 'exclamation-circle', 'info' => 'info-circle', 'warning' => 'exclamation-triangle'] as $msg => $icon)
                @if(session($msg))
                    <div class="alert alert-{{ $msg == 'error' ? 'danger' : $msg }} border-0 shadow-sm rounded-3 alert-dismissible fade show mb-3">
                        <i class="fas fa-{{ $icon }} me-2"></i>{{ session($msg) }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            @endforeach

            {{-- 🔥 FILTER MINGGU --}}
            <form action="{{ route('dkh.index') }}" method="GET" class="row g-2 mb-3">
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Minggu</label>
                    <select name="minggu" class="form-select form-select-sm rounded-3" onchange="this.form.submit()">
                        @foreach($listMinggu as $m)
                        <option value="{{ $m }}" {{ $minggu == $m ? 'selected' : '' }}>Minggu {{ $m }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Tahun</label>
                    <select name="tahun" class="form-select form-select-sm rounded-3" onchange="this.form.submit()">
                        @foreach($listTahun as $t)
                        <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm rounded-3 w-100">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                </div>
            </form>

            {{-- TABLE --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="80">No</th>
                            <th>Nama DC</th>
                            <th>Minggu</th>
                            <th>Total Tagihan</th>
                            <th width="150">Status</th>
                            <th width="120" class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($dkh as $item)
                        <tr>
                            <td>
                                <span class="fw-bold text-primary">
                                    {{ $loop->iteration }}
                                </span>
                            </td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $item->dc->nama_dc }}
                                </div>
                            </td>

                            <td>
                                <span class="badge bg-info">
                                    Minggu {{ $item->minggu_ke }}
                                </span>
                            </td>

                            <td>
                                <span class="fw-bold text-success">
                                    Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}
                                </span>
                            </td>

                            <td>
                                @if($item->status == 'selesai')
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i> Selesai
                                    </span>
                                @elseif($item->status == 'dicetak')
                                    <span class="badge bg-primary">
                                        <i class="fas fa-print me-1"></i> Dicetak
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-pencil-alt me-1"></i> Draft
                                    </span>
                                @endif
                            </td>

                            <td class="text-center">
                                <a href="{{ route('dkh.show', $item->id) }}"
                                   class="btn btn-primary btn-sm rounded-pill px-3">
                                    <i class="fas fa-eye me-1"></i>
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-folder-open fa-2x mb-2"></i>
                                    <p class="mb-0">Belum ada DKH untuk minggu ini</p>
                                    <small>Silakan generate DKH terlebih dahulu</small>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>
</div>
@endsection