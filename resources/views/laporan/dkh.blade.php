@extends('layouts.app')

@section('title', 'Laporan DKH')

@section('content')
<div class="container-fluid py-4">

    <div class="card border-0 shadow-lg rounded-4 mb-4">
        <div class="card-body text-white" style="background: linear-gradient(135deg, #0d6efd, #6610f2);">
            <h2 class="fw-bold mb-1"><i class="fas fa-file-invoice me-2"></i>Laporan DKH</h2>
            <p class="mb-0 opacity-75">{{ $label ?? 'Periode' }}</p>
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
                <div class="col-md-2">
                    <select name="dc_id" class="form-select">
                        <option value="">Semua DC</option>
                        @foreach($dcList ?? [] as $dc)
                            <option value="{{ $dc->id }}" {{ request('dc_id')==$dc->id?'selected':'' }}>{{ $dc->nama_dc }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i> Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-primary text-white"><div class="card-body"><h6>Total Tagihan</h6><h4 class="fw-bold">Rp {{ number_format($totalTagihan ?? 0,0,',','.') }}</h4></div></div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-warning text-dark"><div class="card-body"><h6>Total Nasabah</h6><h4 class="fw-bold">{{ $totalNasabah ?? 0 }}</h4></div></div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-danger text-white"><div class="card-body"><h6>Total JB</h6><h4 class="fw-bold">Rp {{ number_format($totalJb ?? 0,0,',','.') }}</h4></div></div>
        </div>
    </div>

    <div class="card border-0 shadow rounded-4">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>NO</th><th>Nomor DKH</th><th>DC</th><th>Tanggal</th><th class="text-end">Tagihan</th><th class="text-end">Nasabah</th><th class="text-end">JB</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @forelse($dkh ?? [] as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->nomor_dkh }}</td>
                        <td>{{ $item->dc->nama_dc ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal_dkh)->format('d/m/Y') }}</td>
                        <td class="text-end">Rp {{ number_format($item->total_tagihan,0,',','.') }}</td>
                        <td class="text-end">{{ $item->total_nasabah }}</td>
                        <td class="text-end">Rp {{ number_format($item->total_jb,0,',','.') }}</td>
                        <td>
                            @if($item->status=='draft')<span class="badge bg-warning">Draft</span>
                            @elseif($item->status=='dicetak')<span class="badge bg-primary">Dicetak</span>
                            @else<span class="badge bg-success">Selesai</span>@endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center">Tidak ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection