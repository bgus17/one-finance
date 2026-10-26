@extends('layouts.app')

@section('title', 'Laporan Pinjaman')

@section('content')
<div class="container-fluid py-4">

    <div class="card border-0 shadow-lg rounded-4 mb-4">
        <div class="card-body text-white" style="background: linear-gradient(135deg, #0d6efd, #6610f2);">
            <h2 class="fw-bold mb-1"><i class="fas fa-hand-holding-usd me-2"></i>Laporan Pinjaman</h2>
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
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i> Filter</button>
                </div>
            </form>
        </div>
    </div>

    {{-- SUMMARY --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-primary text-white"><div class="card-body"><h6>Total Pinjaman</h6><h4 class="fw-bold">Rp {{ number_format($totalPinjaman ?? 0,0,',','.') }}</h4></div></div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-success text-white"><div class="card-body"><h6>Total Dicairkan</h6><h4 class="fw-bold">Rp {{ number_format($totalDicairkan ?? 0,0,',','.') }}</h4></div></div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-danger text-white"><div class="card-body"><h6>Sisa Tagihan</h6><h4 class="fw-bold">Rp {{ number_format($totalSisa ?? 0,0,',','.') }}</h4></div></div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-warning text-dark"><div class="card-body"><h6>Total Admin</h6><h4 class="fw-bold">Rp {{ number_format($totalAdmin ?? 0,0,',','.') }}</h4></div></div>
        </div>
    </div>

    {{-- TABEL --}}
    <div class="card border-0 shadow rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>#</th><th>Nasabah</th><th>Paket</th><th class="text-end">Pinjaman</th><th class="text-end">Dicairkan</th><th class="text-end">Sisa</th><th>Tenor</th><th>Tgl Cair</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @forelse($pinjaman ?? [] as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->nasabah->nama ?? '-' }}</td>
                            <td>{{ $item->paketPinjaman->nama_paket ?? '-' }}</td>
                            <td class="text-end">Rp {{ number_format($item->jumlah_pinjam,0,',','.') }}</td>
                            <td class="text-end">Rp {{ number_format($item->nominal_dicairkan,0,',','.') }}</td>
                            <td class="text-end">Rp {{ number_format($item->sisa_tagihan,0,',','.') }}</td>
                            <td>{{ $item->tenor }} Mg</td>
                            <td>{{ date('d/m/Y', strtotime($item->tanggal_cair)) }}</td>
                            <td>
                                @if($item->status=='aktif')<span class="badge bg-primary">Aktif</span>
                                @elseif($item->status=='lunas')<span class="badge bg-success">Lunas</span>
                                @elseif($item->status=='ditolak')<span class="badge bg-danger">Ditolak</span>
                                @else<span class="badge bg-warning">Menunggu</span>@endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="text-center">Tidak ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection