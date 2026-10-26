@extends('layouts.app')

@section('title', 'Laporan Keuangan Per DKH')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4">
        <div class="card-body text-white" style="background: linear-gradient(135deg, #0d6efd, #6610f2);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1">
                        <i class="fas fa-chart-line me-2"></i>
                        Laporan Keuangan Per DKH
                    </h2>
                    <p class="mb-0 opacity-75">{{ $label ?? 'Periode' }}</p>
                </div>
                <a href="{{ route('laporan-keuangan.cetak', request()->all()) }}" 
                   target="_blank" 
                   class="btn btn-light">
                    <i class="fas fa-print me-1"></i> Cetak PDF
                </a>
            </div>
        </div>
    </div>

    {{-- FILTER --}}
    <div class="card border-0 shadow rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-2">
                    <select name="periode" class="form-select" onchange="this.form.submit()">
                        <option value="harian" {{ $periode=='harian'?'selected':'' }}>Harian</option>
                        <option value="mingguan" {{ $periode=='mingguan'?'selected':'' }}>Mingguan</option>
                        <option value="bulanan" {{ $periode=='bulanan'?'selected':'' }}>Bulanan</option>
                        <option value="tahunan" {{ $periode=='tahunan'?'selected':'' }}>Tahunan</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="tahun" class="form-select" onchange="this.form.submit()">
                        @foreach($listTahun as $thn)
                            <option value="{{ $thn }}" {{ $tahun==$thn?'selected':'' }}>{{ $thn }}</option>
                        @endforeach
                    </select>
                </div>
                @if($periode == 'bulanan' || $periode == 'tahunan')
                <div class="col-md-2">
                    <select name="bulan" class="form-select" onchange="this.form.submit()">
                        @foreach($listBulan as $key=>$val)
                            <option value="{{ $key }}" {{ $bulan==$key?'selected':'' }}>{{ $val }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                @if($periode == 'mingguan')
                <div class="col-md-2">
                    <select name="minggu" class="form-select" onchange="this.form.submit()">
                        @foreach($listMinggu as $mg)
                            <option value="{{ $mg }}" {{ $minggu==$mg?'selected':'' }}>Minggu {{ $mg }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                @if($periode == 'harian')
                <div class="col-md-2">
                    <input type="date" name="tanggal" class="form-control" value="{{ $tanggal }}" onchange="this.form.submit()">
                </div>
                @endif
                <div class="col-md-2">
                    <select name="dc_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua DC</option>
                        @foreach($dcList as $dc)
                            <option value="{{ $dc->id }}" {{ $dcId==$dc->id?'selected':'' }}>{{ $dc->nama_dc }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- GLOBAL SUMMARY --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4 bg-primary text-white">
        <div class="card-body">
            <h5 class="fw-bold mb-3">
                <i class="fas fa-globe me-2"></i>
                GLOBAL (Semua DKH)
            </h5>
            <div class="row">
                <div class="col-md-3">
                    <small class="text-white-50">Total Tagihan</small>
                    <h4 class="fw-bold">Rp {{ number_format($global['total_tagihan'] ?? 0, 0, ',', '.') }}</h4>
                </div>
                <div class="col-md-3">
                    <small class="text-white-50">Total Bayar</small>
                    <h4 class="fw-bold">Rp {{ number_format($global['total_bayar'] ?? 0, 0, ',', '.') }}</h4>
                </div>
                <div class="col-md-3">
                    <small class="text-white-50">Sisa Tagihan</small>
                    <h4 class="fw-bold text-warning">Rp {{ number_format($global['total_sisa'] ?? 0, 0, ',', '.') }}</h4>
                </div>
                <div class="col-md-3">
                    <small class="text-white-50">Total Nasabah</small>
                    <h4 class="fw-bold">{{ $global['total_nasabah'] ?? 0 }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- REKAP PER DC --}}
    @forelse($rekapPerDc as $dcId => $dcData)
    <div class="card border-0 shadow-lg rounded-4 mb-4">
        <div class="card-header bg-white border-0 pt-4">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">
                    <i class="fas fa-user-tie me-2 text-primary"></i>
                    {{ $dcData['nama_dc'] }}
                </h5>
                <span class="badge bg-primary rounded-pill px-3 py-2">
                    <i class="fas fa-file-invoice me-1"></i>
                    {{ count($dcData['detail_dkh']) }} DKH
                </span>
            </div>
        </div>
        <div class="card-body">
            {{-- Summary per DC --}}
            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <div class="card bg-light border-0 shadow-sm rounded-3">
                        <div class="card-body text-center">
                            <small class="text-muted">Total Tagihan</small>
                            <h6 class="fw-bold text-primary">Rp {{ number_format($dcData['total_tagihan'], 0, ',', '.') }}</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light border-0 shadow-sm rounded-3">
                        <div class="card-body text-center">
                            <small class="text-muted">Total Bayar</small>
                            <h6 class="fw-bold text-success">Rp {{ number_format($dcData['total_bayar'], 0, ',', '.') }}</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light border-0 shadow-sm rounded-3">
                        <div class="card-body text-center">
                            <small class="text-muted">Sisa Tagihan</small>
                            <h6 class="fw-bold text-danger">Rp {{ number_format($dcData['total_sisa'], 0, ',', '.') }}</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light border-0 shadow-sm rounded-3">
                        <div class="card-body text-center">
                            <small class="text-muted">Total Nasabah</small>
                            <h6 class="fw-bold">{{ $dcData['total_nasabah'] }}</h6>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detail per DKH --}}
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nomor DKH</th>
                            <th>Tanggal</th>
                            <th class="text-end">Tagihan</th>
                            <th class="text-end">Bayar</th>
                            <th class="text-end">Sisa</th>
                            <th class="text-center">Nasabah</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dcData['detail_dkh'] as $index => $dkhItem)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <a href="{{ route('dkh.show', $dkhItem['dkh']->id) }}" target="_blank">
                                    {{ $dkhItem['dkh']->nomor_dkh }}
                                </a>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($dkhItem['dkh']->tanggal_dkh)->format('d/m/Y') }}</td>
                            <td class="text-end">Rp {{ number_format($dkhItem['total_tagihan'], 0, ',', '.') }}</td>
                            <td class="text-end text-success">Rp {{ number_format($dkhItem['total_bayar'], 0, ',', '.') }}</td>
                            <td class="text-end text-danger">Rp {{ number_format($dkhItem['total_sisa'], 0, ',', '.') }}</td>
                            <td class="text-center">{{ $dkhItem['dkh']->total_nasabah }}</td>
                            <td class="text-center">
                                @if($dkhItem['dkh']->status == 'draft')
                                    <span class="badge bg-warning text-dark">Draft</span>
                                @elseif($dkhItem['dkh']->status == 'dicetak')
                                    <span class="badge bg-primary">Dicetak</span>
                                @else
                                    <span class="badge bg-success">Selesai</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="3" class="text-end">TOTAL</td>
                            <td class="text-end">Rp {{ number_format($dcData['total_tagihan'], 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($dcData['total_bayar'], 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($dcData['total_sisa'], 0, ',', '.') }}</td>
                            <td class="text-center">{{ $dcData['total_nasabah'] }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @empty
    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-body text-center py-5">
            <i class="fas fa-inbox fa-4x mb-3 d-block opacity-25"></i>
            <h5 class="fw-bold">Tidak Ada Data DKH</h5>
            <p class="text-muted">Belum ada DKH pada periode yang dipilih.</p>
        </div>
    </div>
    @endforelse

</div>
@endsection