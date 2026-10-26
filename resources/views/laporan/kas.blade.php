@extends('layouts.app')

@section('title', 'Laporan Kas')

@section('content')
<div class="container-fluid py-4">

    <div class="card border-0 shadow-lg rounded-4 mb-4">
        <div class="card-body text-white" style="background: linear-gradient(135deg, #0d6efd, #6610f2);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1"><i class="fas fa-money-bill-wave me-2"></i>Laporan Kas</h2>
                    <p class="mb-0 opacity-75">{{ $label ?? 'Periode' }}</p>
                </div>
                <a href="#" class="btn btn-light" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Cetak
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
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i> Filter</button>
                </div>
            </form>
        </div>
    </div>

    {{-- SUMMARY --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-success text-white"><div class="card-body"><h6>Kas Masuk</h6><h3 class="fw-bold">Rp {{ number_format($totalMasuk ?? 0,0,',','.') }}</h3></div></div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-danger text-white"><div class="card-body"><h6>Kas Keluar</h6><h3 class="fw-bold">Rp {{ number_format($totalKeluar ?? 0,0,',','.') }}</h3></div></div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-primary text-white"><div class="card-body"><h6>Selisih</h6><h3 class="fw-bold">Rp {{ number_format($selisih ?? 0,0,',','.') }}</h3></div></div>
        </div>
    </div>

    {{-- TABEL --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card border-0 shadow rounded-4">
                <div class="card-header bg-success text-white"><strong>Kas Masuk</strong></div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Tanggal</th><th>Jenis</th><th class="text-end">Nominal</th></tr></thead>
                        <tbody>
                            @forelse($kasMasuk ?? [] as $item)
                            <tr>
                                <td>{{ date('d/m/Y', strtotime($item->tanggal)) }}</td>
                                <td>{{ $item->jenis }}</td>
                                <td class="text-end">Rp {{ number_format($item->nominal,0,',','.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center">Tidak ada data</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot><tr class="fw-bold"><td colspan="2">Total</td><td class="text-end">Rp {{ number_format($totalMasuk ?? 0,0,',','.') }}</td></tr></tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow rounded-4">
                <div class="card-header bg-danger text-white"><strong>Kas Keluar</strong></div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Tanggal</th><th>Jenis</th><th class="text-end">Nominal</th></tr></thead>
                        <tbody>
                            @forelse($kasKeluar ?? [] as $item)
                            <tr>
                                <td>{{ date('d/m/Y', strtotime($item->tanggal)) }}</td>
                                <td>{{ $item->jenis }}</td>
                                <td class="text-end">Rp {{ number_format($item->nominal,0,',','.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center">Tidak ada data</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot><tr class="fw-bold"><td colspan="2">Total</td><td class="text-end">Rp {{ number_format($totalKeluar ?? 0,0,',','.') }}</td></tr></tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection