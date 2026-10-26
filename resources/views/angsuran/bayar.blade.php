@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Bayar Angsuran Minggu Ke-{{ $angsur->minggu_ke }}</h4>

    <div class="mb-3">
        <strong>Nasabah:</strong> {{ $angsur->pinjaman->nasabah->nama }}
    </div>
    <div class="mb-3">
        <strong>Jumlah Tagihan:</strong> {{ number_format($angsur->jumlah_tagihan) }}
    </div>
    <div class="mb-3">
        <strong>Sudah Bayar:</strong> {{ number_format($angsur->jumlah_bayar) }}
    </div>

    <form action="{{ route('angsuran.bayar.store', $angsur->id) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Jumlah Bayar</label>
            <input type="number" name="jumlah_bayar" class="form-control" required>
        </div>

        <button class="btn btn-primary">Bayar</button>
        <a href="{{ route('pinjaman.show', $angsur->pinjaman_id) }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection