@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">

        {{-- HEADER --}}
        <div class="card border-0 shadow-lg mb-4 rounded-4 overflow-hidden">
            <div class="card-body text-white position-relative"
                style="background: linear-gradient(135deg, #28a745, #20c997);">
                <div class="position-absolute top-0 end-0 opacity-10" style="font-size: 120px; right: 20px; top: -20px;">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="d-flex justify-content-between align-items-center position-relative">
                    <div>
                        <h2 class="fw-bold mb-1">
                            <i class="fas fa-money-bill-wave me-2"></i>
                            Data Pembayaran
                        </h2>
                        <p class="mb-0 opacity-75">
                            <i class="fas fa-database me-1"></i>
                            Total {{ $global['total_transaksi'] ?? 0 }} pembayaran tercatat
                        </p>
                    </div>
                    <div>
                        <span class="badge bg-light text-dark rounded-pill px-4 py-2 me-2">
                            <i class="fas fa-calendar-alt me-1"></i>
                            {{ $label ?? 'Hari ini' }}
                        </span>
                        @if ($metode && $metode != 'semua')
                            <span
                                class="badge {{ $metode == 'kas' ? 'bg-success' : 'bg-primary' }} rounded-pill px-4 py-2 me-2">
                                <i class="fas {{ $metode == 'kas' ? 'fa-coins' : 'fa-university' }} me-1"></i>
                                {{ ucfirst($metode) }}
                            </span>
                        @endif
                        <a href="{{ route('pembayaran.riwayat') }}"
                            class="btn btn-light fw-semibold shadow-sm rounded-pill px-4 py-2 hover-scale">
                            <i class="fas fa-history me-2"></i>
                            Riwayat
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ALERT --}}
        @if (session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger border-0 shadow-sm rounded-3 alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- STATISTIK CARD --}}
        <div class="row g-3 mb-4">
            <div class="col-md-2 col-6">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small">Total Bayar</span>
                                <h4 class="fw-bold mb-0 text-success">Rp
                                    {{ number_format($global['total_bayar'] ?? 0, 0, ',', '.') }}</h4>
                            </div>
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-money-bill-wave text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small">💰 Kas</span>
                                <h4 class="fw-bold mb-0 text-success">Rp
                                    {{ number_format($global['total_kas'] ?? 0, 0, ',', '.') }}</h4>
                            </div>
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-coins text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small">🏦 Transfer</span>
                                <h4 class="fw-bold mb-0 text-primary">Rp
                                    {{ number_format($global['total_transfer'] ?? 0, 0, ',', '.') }}</h4>
                            </div>
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-university text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small">Transaksi</span>
                                <h4 class="fw-bold mb-0 text-primary">{{ $global['total_transaksi'] ?? 0 }}</h4>
                            </div>
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-receipt text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small">Nasabah</span>
                                <h4 class="fw-bold mb-0 text-warning">{{ $global['total_nasabah'] ?? 0 }}</h4>
                            </div>
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-users text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small">Total DC</span>
                                <h4 class="fw-bold mb-0 text-info">{{ $global['total_dc'] ?? 0 }}</h4>
                            </div>
                            <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-building text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTER CARD --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body">
                <form action="{{ route('pembayaran.index') }}" method="GET" id="filterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted">
                                <i class="fas fa-calendar-alt me-1"></i> Periode
                            </label>
                            <select name="periode" class="form-select form-select-sm rounded-3"
                                onchange="this.form.submit()">
                                <option value="harian" {{ $periode == 'harian' ? 'selected' : '' }}>📅 Harian</option>
                                <option value="mingguan" {{ $periode == 'mingguan' ? 'selected' : '' }}>📆 Mingguan
                                </option>
                                <option value="bulanan" {{ $periode == 'bulanan' ? 'selected' : '' }}>📊 Bulanan</option>
                                <option value="tahunan" {{ $periode == 'tahunan' ? 'selected' : '' }}>📈 Tahunan</option>
                            </select>
                        </div>

                        @if ($periode == 'harian')
                            <div class="col-md-2">
                                <label class="form-label small fw-bold text-muted">
                                    <i class="fas fa-calendar-day me-1"></i> Tanggal
                                </label>
                                <input type="date" name="tanggal" class="form-control form-control-sm rounded-3"
                                    value="{{ $tanggal }}" onchange="this.form.submit()">
                            </div>
                        @endif

                        @if (in_array($periode, ['mingguan', 'bulanan', 'tahunan']))
                            <div class="col-md-2">
                                <label class="form-label small fw-bold text-muted">
                                    <i class="fas fa-calendar-alt me-1"></i> Tahun
                                </label>
                                <select name="tahun" class="form-select form-select-sm rounded-3"
                                    onchange="this.form.submit()">
                                    @foreach ($listTahun as $t)
                                        <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>
                                            {{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        @if ($periode == 'mingguan')
                            <div class="col-md-2">
                                <label class="form-label small fw-bold text-muted">
                                    <i class="fas fa-calendar-week me-1"></i> Minggu
                                </label>
                                <select name="minggu" class="form-select form-select-sm rounded-3"
                                    onchange="this.form.submit()">
                                    @foreach ($listMinggu as $m)
                                        <option value="{{ $m }}" {{ $minggu == $m ? 'selected' : '' }}>Minggu
                                            {{ $m }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        @if ($periode == 'bulanan')
                            <div class="col-md-2">
                                <label class="form-label small fw-bold text-muted">
                                    <i class="fas fa-calendar-month me-1"></i> Bulan
                                </label>
                                <select name="bulan" class="form-select form-select-sm rounded-3"
                                    onchange="this.form.submit()">
                                    @foreach ($listBulan as $key => $value)
                                        <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>
                                            {{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted">
                                <i class="fas fa-store me-1"></i> DC
                            </label>
                            <select name="dc_id" class="form-select form-select-sm rounded-3"
                                onchange="this.form.submit()">
                                <option value="">Semua DC</option>
                                @foreach ($dcList ?? [] as $dc)
                                    <option value="{{ $dc->id }}" {{ ($dcId ?? '') == $dc->id ? 'selected' : '' }}>
                                        {{ $dc->nama_dc }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- FILTER METODE --}}
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted">
                                <i class="fas fa-filter me-1"></i> Metode
                            </label>
                            <select name="metode" class="form-select form-select-sm rounded-3"
                                onchange="this.form.submit()">
                                <option value="semua" {{ ($metode ?? 'semua') == 'semua' ? 'selected' : '' }}>📊 Semua
                                </option>
                                <option value="kas" {{ ($metode ?? 'semua') == 'kas' ? 'selected' : '' }}>💰 Kas
                                </option>
                                <option value="transfer" {{ ($metode ?? 'semua') == 'transfer' ? 'selected' : '' }}>🏦
                                    Transfer</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            @if ($dcId || ($metode && $metode != 'semua'))
                                <a href="{{ route('pembayaran.index', array_merge(request()->except(['dc_id', 'metode']), ['dc_id' => '', 'metode' => 'semua'])) }}"
                                    class="btn btn-outline-danger btn-sm rounded-3 w-100">
                                    <i class="fas fa-times me-1"></i> Reset Filter
                                </a>
                            @else
                                <button type="submit" class="btn btn-primary btn-sm rounded-3 w-100">
                                    <i class="fas fa-search me-1"></i> Filter
                                </button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- BADGE FILTER AKTIF --}}
        @if ($metode && $metode != 'semua')
            <div class="alert alert-info border-0 shadow-sm rounded-3 mb-3">
                <i class="fas fa-info-circle me-2"></i>
                Menampilkan data dengan metode:
                <span class="badge {{ $metode == 'kas' ? 'bg-success' : 'bg-primary' }} px-3 py-2">
                    <i class="fas {{ $metode == 'kas' ? 'fa-coins' : 'fa-university' }} me-1"></i>
                    {{ ucfirst($metode) }}
                </span>
                @if ($dcId)
                    <span class="mx-1">|</span>
                    <span class="badge bg-secondary px-3 py-2">
                        <i class="fas fa-store me-1"></i>
                        {{ $dcList->where('id', $dcId)->first()->nama_dc ?? 'DC' }}
                    </span>
                @endif
                <a href="{{ route('pembayaran.index', array_merge(request()->except(['dc_id', 'metode']), ['dc_id' => '', 'metode' => 'semua'])) }}"
                    class="btn btn-sm btn-outline-secondary ms-2">
                    <i class="fas fa-times"></i> Hapus Filter
                </a>
            </div>
        @endif

        {{-- DATA PER DC --}}
        @forelse($dataPerDc as $dcData)
            <div class="card border-0 shadow-lg rounded-4 mb-4">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 fw-bold text-success">
                            <i class="fas fa-store-alt me-2"></i>
                            {{ $dcData['dc']->nama_dc ?? 'DC' }}
                            @if (isset($dcData['dc']->kode_dc))
                                <span class="badge bg-secondary rounded-pill px-3 py-2 ms-2">
                                    {{ $dcData['dc']->kode_dc }}
                                </span>
                            @endif
                            @if ($dcId)
                                <span class="badge bg-primary rounded-pill px-3 py-2 ms-2">
                                    <i class="fas fa-filter me-1"></i> Filtered
                                </span>
                            @endif
                            @if ($metode && $metode != 'semua')
                                <span
                                    class="badge {{ $metode == 'kas' ? 'bg-success' : 'bg-primary' }} rounded-pill px-3 py-2 ms-2">
                                    <i class="fas {{ $metode == 'kas' ? 'fa-coins' : 'fa-university' }} me-1"></i>
                                    {{ ucfirst($metode) }}
                                </span>
                            @endif
                        </h5>
                        @if (isset($dcData['total_kas']) && isset($dcData['total_transfer']))
                            <div class="mt-1">
                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-1">
                                    <i class="fas fa-coins me-1"></i>
                                    Kas: Rp {{ number_format($dcData['total_kas'] ?? 0, 0, ',', '.') }}
                                </span>
                                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 ms-1">
                                    <i class="fas fa-university me-1"></i>
                                    Transfer: Rp {{ number_format($dcData['total_transfer'] ?? 0, 0, ',', '.') }}
                                </span>
                            </div>
                        @endif
                    </div>
                    <div>
                        <span class="badge bg-success rounded-pill px-3 py-2 me-1">
                            <i class="fas fa-money-bill me-1"></i>
                            Rp {{ number_format($dcData['total_bayar'] ?? 0, 0, ',', '.') }}
                        </span>
                        <span class="badge bg-info rounded-pill px-3 py-2">
                            <i class="fas fa-users me-1"></i>
                            {{ $dcData['total_nasabah'] ?? 0 }} Nasabah
                        </span>
                        <span class="badge bg-secondary rounded-pill px-3 py-2 ms-1">
                            <i class="fas fa-receipt me-1"></i>
                            {{ $dcData['total_transaksi'] ?? 0 }} Transaksi
                        </span>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3" width="50">#</th>
                                    <th width="120">Tanggal</th>
                                    <th width="180">Nasabah</th>
                                    <th width="150">Alamat</th>
                                    <th class="text-end" width="150">Nominal</th>
                                    <th class="text-end" width="130">Pokok</th>
                                    <th class="text-end" width="130">Margin</th>
                                    <th class="text-center" width="100">Metode</th>
                                    <th width="150">Keterangan</th>
                                    <th class="text-center" width="100">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dcData['pembayaran'] as $key => $item)
                                    @php
                                        // Hitung pokok & margin
                                        $pinjaman = $item->angsuran->pinjaman ?? null;
                                        $pokok = 0;
                                        $margin = 0;
                                        $nasabah = $pinjaman->nasabah ?? null;

                                        if ($pinjaman && $pinjaman->paketPinjaman) {
                                            $totalBunga =
                                                ($pinjaman->jumlah_pinjam * $pinjaman->paketPinjaman->bunga) / 100;
                                            $totalTagihan = $pinjaman->jumlah_pinjam + $totalBunga;

                                            if ($totalTagihan > 0) {
                                                $proporsiPokok = $pinjaman->jumlah_pinjam / $totalTagihan;
                                                $proporsiBunga = $totalBunga / $totalTagihan;

                                                $pokok = $item->jumlah_bayar * $proporsiPokok;
                                                $margin = $item->jumlah_bayar * $proporsiBunga;
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td class="ps-3">
                                            <span class="fw-bold text-success">{{ $loop->iteration }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold">
                                                {{ \Carbon\Carbon::parse($item->tanggal_bayar)->format('d/m/Y') }}
                                            </span>
                                            <br>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($item->tanggal_bayar)->format('H:i') }}
                                            </small>
                                        </td>
                                        <td>
                                            @if ($nasabah)
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2"
                                                        style="width: 32px; height: 32px;">
                                                        <span
                                                            class="fw-bold text-success">{{ substr($nasabah->nama ?? 'N', 0, 1) }}</span>
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
                                            @if ($nasabah)
                                                <small class="text-muted">
                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                    {{ Str::limit($nasabah->alamat ?? '-', 30) }}
                                                </small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-bold text-success">
                                                Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}
                                            </span>
                                            @if ($pinjaman)
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>
                                                    Sisa: Rp {{ number_format($pinjaman->sisa_tagihan ?? 0, 0, ',', '.') }}
                                                </small>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-bold text-primary">
                                                Rp {{ number_format($pokok, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-bold text-success">
                                                Rp {{ number_format($margin, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if ($item->metode_bayar == 'kas')
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
                                        <td>
                                            <small>{{ Str::limit($item->keterangan ?? '-', 25) }}</small>
                                            @if ($item->angsuran)
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar-check me-1"></i>
                                                    Angsuran ke-{{ $item->angsuran->minggu_ke ?? '?' }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('pembayaran.detail', $item->id) }}"
                                                    class="btn btn-info btn-sm text-white" title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button class="btn btn-sm btn-outline-warning rounded-3"
                                                    onclick="window.print()" data-bs-toggle="tooltip" title="Cetak">
                                                    <i class="fas fa-print"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fas fa-inbox fa-4x mb-3 d-block opacity-25"></i>
                                                <h5 class="fw-bold">Belum Ada Data Pembayaran</h5>
                                                <p class="mb-0">Belum ada pembayaran yang tercatat pada periode ini.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if ($dcData['pembayaran']->isNotEmpty())
                                @php
                                    $totalPokok = 0;
                                    $totalMargin = 0;
                                    foreach ($dcData['pembayaran'] as $item) {
                                        $pinjaman = $item->angsuran->pinjaman ?? null;
                                        if ($pinjaman && $pinjaman->paketPinjaman) {
                                            $totalBunga =
                                                ($pinjaman->jumlah_pinjam * $pinjaman->paketPinjaman->bunga) / 100;
                                            $totalTagihan = $pinjaman->jumlah_pinjam + $totalBunga;
                                            if ($totalTagihan > 0) {
                                                $proporsiPokok = $pinjaman->jumlah_pinjam / $totalTagihan;
                                                $proporsiBunga = $totalBunga / $totalTagihan;
                                                $totalPokok += $item->jumlah_bayar * $proporsiPokok;
                                                $totalMargin += $item->jumlah_bayar * $proporsiBunga;
                                            }
                                        }
                                    }
                                @endphp
                                <tfoot>
                                    <tr class="table-light">
                                        <td colspan="4" class="ps-3 fw-bold">
                                            <i class="fas fa-calculator me-2"></i>
                                            TOTAL {{ $dcData['dc']->nama_dc ?? 'DC' }}
                                        </td>
                                        <td class="text-end fw-bold text-success">
                                            Rp {{ number_format($dcData['total_bayar'] ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end fw-bold text-primary">
                                            Rp {{ number_format($totalPokok, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end fw-bold text-success">
                                            Rp {{ number_format($totalMargin, 0, ',', '.') }}
                                        </td>
                                        <td colspan="3">
                                            <span class="badge bg-info rounded-pill px-3 py-2 me-1">
                                                <i class="fas fa-users me-1"></i>
                                                {{ $dcData['total_nasabah'] ?? 0 }} Nasabah
                                            </span>
                                            <span class="badge bg-secondary rounded-pill px-3 py-2">
                                                <i class="fas fa-receipt me-1"></i>
                                                {{ $dcData['total_transaksi'] ?? 0 }} Transaksi
                                            </span>
                                        </td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>

                <div class="card-footer bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Menampilkan {{ $dcData['pembayaran']->count() }} data
                        </small>
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>
                            Terakhir diperbarui: {{ now()->format('d/m/Y H:i:s') }}
                        </small>
                    </div>
                </div>
            </div>
        @empty
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body text-center py-5">
                    <div class="text-muted">
                        <i class="fas fa-database fa-4x mb-3 d-block opacity-25"></i>
                        <h5 class="fw-bold">Tidak Ada Data</h5>
                        <p class="mb-0">Belum ada data pembayaran yang tersedia pada periode yang dipilih.</p>
                    </div>
                </div>
            </div>
        @endforelse

        {{-- PAGINATION --}}
        @if (isset($pembayaran) && $pembayaran->hasPages())
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="d-flex justify-content-center">
                        {{ $pembayaran->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        @endif

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

        .table> :not(caption)>*>* {
            padding: 12px 8px;
            vertical-align: middle;
        }

        .btn-group-sm>.btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        .btn-group {
            gap: 4px;
        }

        .btn-group>.btn {
            border-radius: 0.375rem !important;
            border: 1px solid #e0e0e0;
        }

        .btn-group>.btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .badge {
            font-weight: 600;
        }

        .card {
            border: none !important;
        }

        .card-header {
            border-bottom: 1px solid #f0f0f0 !important;
        }

        .form-select-sm,
        .form-control-sm {
            border-color: #e0e0e0;
        }

        .form-select-sm:focus,
        .form-control-sm:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.15);
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
        }

        .rounded-4 {
            border-radius: 1rem !important;
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

        // Auto submit form on select change
        document.querySelectorAll('select[onchange]').forEach(function(select) {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        });

        // Auto submit form on date change
        document.querySelectorAll('input[onchange]').forEach(function(input) {
            input.addEventListener('change', function() {
                this.form.submit();
            });
        });
    </script>
@endpush
