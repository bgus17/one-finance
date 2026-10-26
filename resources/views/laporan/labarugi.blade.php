@extends('layouts.app')

@section('title', 'Laporan Laba Rugi')

@section('content')
<div class="container-fluid py-4">

    <div class="card border-0 shadow-lg rounded-4 mb-4">
        <div class="card-body text-white" style="background: linear-gradient(135deg, #0d6efd, #6610f2);">
            <h2 class="fw-bold mb-1"><i class="fas fa-chart-line me-2"></i>Laporan Laba Rugi</h2>
            <p class="mb-0 opacity-75">{{ $label ?? 'Periode' }}</p>
        </div>
    </div>

    {{-- FILTER (sama) --}}
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

    {{-- PENDAPATAN & BIAYA --}}
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card border-0 shadow rounded-4">
                <div class="card-header bg-success text-white"><strong>PENDAPATAN</strong></div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr><td>Pendapatan Bunga</td><td class="text-end">Rp {{ number_format($pendapatanBunga ?? 0,0,',','.') }}</td></tr>
                        <tr><td>Pendapatan Administrasi</td><td class="text-end">Rp {{ number_format($pendapatanAdmin ?? 0,0,',','.') }}</td></tr>
                        <tr><td>Pendapatan Denda</td><td class="text-end">Rp {{ number_format($pendapatanDenda ?? 0,0,',','.') }}</td></tr>
                        <tr class="fw-bold"><td>TOTAL PENDAPATAN</td><td class="text-end">Rp {{ number_format($totalPendapatan ?? 0,0,',','.') }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow rounded-4">
                <div class="card-header bg-danger text-white"><strong>BIAYA</strong></div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr><td>Biaya Operasional</td><td class="text-end">Rp {{ number_format($biayaOperasional ?? 0,0,',','.') }}</td></tr>
                        <tr><td>Gaji</td><td class="text-end">Rp {{ number_format($biayaGaji ?? 0,0,',','.') }}</td></tr>
                        <tr><td>Lain-lain</td><td class="text-end">Rp {{ number_format($biayaLain ?? 0,0,',','.') }}</td></tr>
                        <tr class="fw-bold"><td>TOTAL BIAYA</td><td class="text-end">Rp {{ number_format($totalBiaya ?? 0,0,',','.') }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- LABA/RUGI --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow rounded-4 bg-{{ $statusColor ?? 'primary' }} text-white">
                <div class="card-body text-center py-4">
                    <h4 class="fw-bold">
                        {{ $status ?? 'LABA' }}
                        <br>
                        <span style="font-size: 2rem;">Rp {{ number_format(abs($labaRugi ?? 0),0,',','.') }}</span>
                    </h4>
                    <p class="mb-0">Total Pendapatan - Total Biaya</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection