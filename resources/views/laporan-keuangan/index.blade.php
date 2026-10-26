@extends('layouts.app')

@section('title', 'Laporan Keuangan')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4">
        <div class="card-body text-white" style="background: linear-gradient(135deg, #0d6efd, #6610f2);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1">
                        <i class="fas fa-chart-line me-2"></i>
                        Laporan Keuangan
                    </h2>
                    <p class="mb-0 opacity-75">{{ $label ?? 'Periode' }}</p>
                    <p class="mb-0 small text-white-50">
                        <i class="fas fa-filter me-1"></i>
                        DC: <strong>{{ $filterLabel ?? 'Semua DC' }}</strong>
                    </p>
                </div>
                <div>
                    <a href="{{ route('laporan-keuangan.cetak', request()->all()) }}" 
                       target="_blank" 
                       class="btn btn-light">
                        <i class="fas fa-print me-1"></i> Cetak PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER PERIODE + DC --}}
    <div class="card border-0 shadow rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-2">
                    <select name="periode" class="form-select" onchange="this.form.submit()">
                        <option value="harian" {{ ($periode ?? '')=='harian'?'selected':'' }}>Harian</option>
                        <option value="mingguan" {{ ($periode ?? '')=='mingguan'?'selected':'' }}>Mingguan</option>
                        <option value="bulanan" {{ ($periode ?? '')=='bulanan'?'selected':'' }}>Bulanan</option>
                        <option value="tahunan" {{ ($periode ?? '')=='tahunan'?'selected':'' }}>Tahunan</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="tahun" class="form-select" onchange="this.form.submit()">
                        @foreach($listTahun ?? [] as $thn)
                            <option value="{{ $thn }}" {{ ($tahun ?? '')==$thn?'selected':'' }}>{{ $thn }}</option>
                        @endforeach
                    </select>
                </div>
                @if(($periode ?? '') == 'bulanan' || ($periode ?? '') == 'tahunan')
                <div class="col-md-2">
                    <select name="bulan" class="form-select" onchange="this.form.submit()">
                        @foreach($listBulan ?? [] as $key=>$val)
                            <option value="{{ $key }}" {{ ($bulan ?? '')==$key?'selected':'' }}>{{ $val }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                @if(($periode ?? '') == 'mingguan')
                <div class="col-md-2">
                    <select name="minggu" class="form-select" onchange="this.form.submit()">
                        @foreach($listMinggu ?? [] as $mg)
                            <option value="{{ $mg }}" {{ ($minggu ?? '')==$mg?'selected':'' }}>Minggu {{ $mg }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                @if(($periode ?? '') == 'harian')
                <div class="col-md-2">
                    <input type="date" name="tanggal" class="form-control" value="{{ $tanggal ?? date('Y-m-d') }}" onchange="this.form.submit()">
                </div>
                @endif
                {{-- 🔥 FILTER DC --}}
                <div class="col-md-2">
                    <select name="dc_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua DC</option>
                        @foreach($dcList ?? [] as $dc)
                            <option value="{{ $dc->id }}" {{ ($dcId ?? '')==$dc->id?'selected':'' }}>{{ $dc->nama_dc }}</option>
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

    {{-- 4 CARD UTAMA --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-primary text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Total Saldo Pinjaman</h6>
                    <h3 class="fw-bold">Rp {{ number_format($totalSaldoPinjaman ?? 0, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-info text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Total Angsuran/Minggu</h6>
                    <h3 class="fw-bold">Rp {{ number_format($totalAngsuranPerMinggu ?? 0, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-success text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Kas Masuk</h6>
                    <h3 class="fw-bold">Rp {{ number_format($kasMasuk ?? 0, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-danger text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Kas Keluar</h6>
                    <h3 class="fw-bold">Rp {{ number_format($kasKeluar ?? 0, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- SISA BUNGA & POKOK --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body text-center">
                    <h6 class="text-muted">Sisa Bunga</h6>
                    <h4 class="fw-bold text-warning">Rp {{ number_format($sisaBunga ?? 0, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body text-center">
                    <h6 class="text-muted">Sisa Pokok</h6>
                    <h4 class="fw-bold text-primary">Rp {{ number_format($sisaPokok ?? 0, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body text-center">
                    <h6 class="text-muted">Sisa Bunga + Pokok</h6>
                    <h4 class="fw-bold text-danger">Rp {{ number_format($sisaBungaPokok ?? 0, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- MACET & REAL --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-danger text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Total Macet</h6>
                    <h4 class="fw-bold">Rp {{ number_format($totalMacet ?? 0, 0, ',', '.') }}</h4>
                    <small>Pokok: {{ number_format($pokokMacet ?? 0, 0, ',', '.') }} | Margin: {{ number_format($marginMacet ?? 0, 0, ',', '.') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-warning text-dark">
                <div class="card-body">
                    <h6 class="text-dark-50">Real Margin</h6>
                    <h4 class="fw-bold">Rp {{ number_format($realMargin ?? 0, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-info text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Real Sisa Pokok</h6>
                    <h4 class="fw-bold">Rp {{ number_format($realSisaPokok ?? 0, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-success text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Real SPP</h6>
                    <h4 class="fw-bold">Rp {{ number_format($realSPP ?? 0, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL RINCIAN PER DC (hanya jika pilih Semua DC) --}}
    @if(!$dcId && count($rekapPerDc) > 0)
    <div class="card border-0 shadow-lg rounded-4 mt-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="fw-bold mb-0">
                <i class="fas fa-table me-2 text-primary"></i>
                Rincian Per DC
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nama DC</th>
                            <th class="text-end">Total Tagihan</th>
                            <th class="text-end">Total Bayar</th>
                            <th class="text-end">Sisa Tagihan</th>
                            <th class="text-center">Jumlah Nasabah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rekapPerDc as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item['dc']->nama_dc }}</td>
                            <td class="text-end">Rp {{ number_format($item['total_tagihan'], 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($item['total_bayar'], 0, ',', '.') }}</td>
                            <td class="text-end text-danger">Rp {{ number_format($item['total_sisa'], 0, ',', '.') }}</td>
                            <td class="text-center">{{ $item['jumlah_nasabah'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="2" class="text-end">TOTAL</td>
                            <td class="text-end">Rp {{ number_format(collect($rekapPerDc)->sum('total_tagihan'), 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format(collect($rekapPerDc)->sum('total_bayar'), 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format(collect($rekapPerDc)->sum('total_sisa'), 0, ',', '.') }}</td>
                            <td class="text-center">{{ collect($rekapPerDc)->sum('jumlah_nasabah') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- GRAFIK --}}
    <div class="card border-0 shadow-lg rounded-4 mt-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="fw-bold mb-0">
                <i class="fas fa-chart-bar me-2 text-primary"></i>
                Grafik Keuangan
            </h5>
        </div>
        <div class="card-body">
            <canvas id="grafikKeuangan" height="300"></canvas>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('grafikKeuangan').getContext('2d');
        const data = @json($grafik ?? []);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels || [],
                datasets: [{
                    label: 'Nominal (Rp)',
                    data: data.data || [],
                    backgroundColor: data.colors || [],
                    borderColor: data.colors || [],
                    borderWidth: 2,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    });
</script>

<style>
    .rounded-4 { border-radius: 1rem !important; }
    .card { border-radius: 1rem !important; overflow: hidden; }
</style>
@endsection