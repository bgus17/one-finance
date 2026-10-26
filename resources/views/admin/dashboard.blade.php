@extends('layouts.app')

@section('content')

    @php
        use App\Models\Modal;
        use App\Models\Nasabah;
        use App\Models\Wilayah;
        use App\Models\Pinjaman;
        use App\Models\Angsuran;
        use App\Models\KasMasuk;
        use App\Models\KasKeluar;
        use Carbon\Carbon;

        // ============================================
        // 1. DATA MODAL / SALDO
        // ============================================
        $modal = Modal::where('status', 'aktif')->first();
        $saldoSekarang = $modal ? $modal->saldo : 0;
        $totalModal = Modal::sum('nominal');

        // ============================================
        // 2. DATA KAS MASUK & KELUAR (BULAN INI)
        // ============================================
        $bulanIni = Carbon::now()->startOfMonth();
        $bulanDepan = Carbon::now()->endOfMonth();

        $totalKasMasuk = KasMasuk::whereBetween('tanggal', [$bulanIni, $bulanDepan])->sum('nominal');
        $totalKasKeluar = KasKeluar::whereBetween('tanggal', [$bulanIni, $bulanDepan])->sum('nominal');

        // ============================================
        // 3. DATA KAS MASUK & KELUAR (HARI INI)
        // ============================================
        $hariIni = Carbon::now()->startOfDay();
        $hariDepan = Carbon::now()->endOfDay();

        $kasMasukHariIni = KasMasuk::whereBetween('tanggal', [$hariIni, $hariDepan])->sum('nominal');
        $kasKeluarHariIni = KasKeluar::whereBetween('tanggal', [$hariIni, $hariDepan])->sum('nominal');

        // ============================================
        // 4. DATA NASABAH & WILAYAH
        // ============================================
        $totalNasabah = Nasabah::count();
        $totalWilayah = Wilayah::count();

        // ============================================
        // 5. DATA PINJAMAN
        // ============================================
        $totalPinjaman = Pinjaman::count();
        $pinjamanAktif = Pinjaman::where('status', 'aktif')->count();
        $pinjamanLunas = Pinjaman::where('status', 'lunas')->count();
        $pinjamanMacet = Pinjaman::where('status', 'macet')->count();

        // ============================================
        // 6. TOTAL NOMINAL PINJAMAN
        // ============================================
        $totalNominalPinjaman = Pinjaman::sum('jumlah_pinjam');
        $totalSisaTagihan = Pinjaman::sum('sisa_tagihan');
        $totalAngsuranTerbayar = Angsuran::sum('jumlah_bayar');

        // ============================================
        // 7. ANGSURAN JATUH TEMPO MINGGU INI
        // ============================================
        $jatuhTempoMingguIni = Angsuran::with(['pinjaman.nasabah'])
            ->where('status', 'belum_bayar')
            ->whereBetween('tanggal_jatuh_tempo', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->orderBy('tanggal_jatuh_tempo')
            ->limit(5)
            ->get();

        // ============================================
        // 8. TRANSAKSI TERBARU
        // ============================================
        $kasMasukTerbaru = KasMasuk::with('modal')->orderBy('created_at', 'desc')->limit(5)->get();
        $kasKeluarTerbaru = KasKeluar::with('modal')->orderBy('created_at', 'desc')->limit(5)->get();

        // ============================================
        // 9. GRAFIK KAS 7 HARI TERAKHIR
        // ============================================
        $grafikLabels = [];
        $grafikMasuk = [];
        $grafikKeluar = [];

        for ($i = 6; $i >= 0; $i--) {
            $tanggal = Carbon::now()->subDays($i);
            $grafikLabels[] = $tanggal->format('d M');

            $grafikMasuk[] = (int) KasMasuk::whereDate('tanggal', $tanggal->format('Y-m-d'))->sum('nominal');
            $grafikKeluar[] = (int) KasKeluar::whereDate('tanggal', $tanggal->format('Y-m-d'))->sum('nominal');
        }

        // ============================================
        // 10. SELISIH KAS
        // ============================================
        $totalKasMasukAll = KasMasuk::sum('nominal');
        $totalKasKeluarAll = KasKeluar::sum('nominal');
        $selisihKas = $totalKasMasukAll - $totalKasKeluarAll;
    @endphp

    <div class="container-fluid px-2 px-md-4 py-2">

        {{-- ============================================ --}}
        {{-- HERO BANNER RINGKAS & RAMPING (COMPACT GLOW)  --}}
        {{-- ============================================ --}}
        <div class="card border-0 text-white overflow-hidden shadow-sm mb-4" style="background: linear-gradient(135deg, #0f172a 0%, #1865f2 50%, #1e1b4b 100%); border-radius: 18px;">
            <div class="card-body p-3 p-md-3.5 position-relative">
                
                <!-- Animasi Background Glow Mesh -->
                <div class="header-glow-orb"></div>

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 position-relative" style="z-index: 2;">
                    
                    {{-- KIRI: BADGE + JUDUL ADMIN --}}
                    <div>
                        <div class="d-inline-flex align-items-center gap-2 px-2.5 py-1 rounded-pill bg-white bg-opacity-10 backdrop-blur border border-white border-opacity-10 mb-1.5">
                            <span class="pulse-status-dot"></span>
                            <span class="fs-10 fw-bold text-uppercase tracking-wider text-white">SYSTEM OVERVIEW & LIVE MONITORING</span>
                        </div>

                        <h3 class="fw-extrabold text-white mb-0.5 fs-22 lh-1 tracking-tight">
                            Dashboard Admin
                        </h3>
                        
                        <p class="text-white-50 fs-12 mb-0">
                            Selamat datang kembali, <strong class="text-white">{{ Auth::user()->name }}</strong>.
                        </p>
                    </div>

                    {{-- KANAN: WIDGET JAM SERVER GLASSMORPHISM --}}
                    <div class="glass-time-card px-3 py-2 rounded-3 text-start">
                        <span class="fs-10 fw-extrabold text-white-50 text-uppercase tracking-wider d-block mb-0.5">WAKTU SERVER</span>
                        <div class="d-flex align-items-baseline gap-1.5">
                            <span id="hero-live-clock" class="fs-16 fw-black text-white font-monospace lh-1">{{ now()->format('H:i:s') }}</span>
                            <span class="fs-10 fw-bold text-info">WIB</span>
                        </div>
                        <div class="d-flex align-items-center gap-1 mt-1 text-white-50 fs-11">
                            <i class="far fa-calendar-alt text-info"></i>
                            <span>{{ now()->translatedFormat('l, d F Y') }}</span>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        {{-- ============================================ --}}
        {{-- METRIKS UTAMA (4 METRIC GLOW CARDS)          --}}
        {{-- ============================================ --}}
        <div class="row g-3 mb-4">
            {{-- SALDO KAS --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 rounded-4 h-100 luxury-stat-card card-glow-emerald">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="icon-shape bg-emerald-subtle text-emerald rounded-3">
                                <i class="fas fa-wallet fa-lg"></i>
                            </div>
                            <span class="badge bg-emerald-subtle text-emerald rounded-pill px-2 py-1 fs-11 font-monospace">LIVE</span>
                        </div>
                        <span class="text-muted fw-bold text-uppercase fs-11 tracking-wider d-block mb-1">Saldo Kas Aktif</span>
                        <h3 class="fw-bold text-dark mb-2 tracking-tight">Rp {{ number_format($saldoSekarang, 0, ',', '.') }}</h3>
                        <div class="d-flex align-items-center text-muted fs-11">
                            <i class="fas fa-history me-1 text-emerald"></i>
                            <span>Update: {{ now()->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KAS MASUK BULAN INI --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 rounded-4 h-100 luxury-stat-card card-glow-indigo">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="icon-shape bg-indigo-subtle text-indigo rounded-3">
                                <i class="fas fa-arrow-circle-down fa-lg"></i>
                            </div>
                            <span class="badge bg-indigo-subtle text-indigo rounded-pill px-2 py-1 fs-11">BULAN INI</span>
                        </div>
                        <span class="text-muted fw-bold text-uppercase fs-11 tracking-wider d-block mb-1">Total Kas Masuk</span>
                        <h3 class="fw-bold text-indigo mb-2 tracking-tight">Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</h3>
                        <div class="d-flex align-items-center text-muted fs-11">
                            <i class="fas fa-calendar-check me-1 text-indigo"></i>
                            <span>Periode: {{ Carbon::now()->translatedFormat('F Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KAS KELUAR BULAN INI --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 rounded-4 h-100 luxury-stat-card card-glow-rose">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="icon-shape bg-rose-subtle text-rose rounded-3">
                                <i class="fas fa-arrow-circle-up fa-lg"></i>
                            </div>
                            <span class="badge bg-rose-subtle text-rose rounded-pill px-2 py-1 fs-11">BULAN INI</span>
                        </div>
                        <span class="text-muted fw-bold text-uppercase fs-11 tracking-wider d-block mb-1">Total Kas Keluar</span>
                        <h3 class="fw-bold text-rose mb-2 tracking-tight">Rp {{ number_format($totalKasKeluar, 0, ',', '.') }}</h3>
                        <div class="d-flex align-items-center text-muted fs-11">
                            <i class="fas fa-calendar-minus me-1 text-rose"></i>
                            <span>Periode: {{ Carbon::now()->translatedFormat('F Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SELISIH KAS --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 rounded-4 h-100 luxury-stat-card card-glow-amber">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="icon-shape bg-amber-subtle text-amber rounded-3">
                                <i class="fas fa-calculator fa-lg"></i>
                            </div>
                            <span class="badge bg-amber-subtle text-amber rounded-pill px-2 py-1 fs-11">NET FLOW</span>
                        </div>
                        <span class="text-muted fw-bold text-uppercase fs-11 tracking-wider d-block mb-1">Selisih Arus Kas</span>
                        <h3 class="fw-bold text-dark mb-2 tracking-tight">Rp {{ number_format($selisihKas, 0, ',', '.') }}</h3>
                        <div class="d-flex align-items-center text-muted fs-11">
                            <i class="fas fa-balance-scale me-1 text-amber"></i>
                            <span>Total Masuk − Total Keluar</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- MINI MASTER METRICS (4 CARDS COMPACT)       --}}
        {{-- ============================================ --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 rounded-4 shadow-sm p-3 bg-white luxury-hover">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-shape-sm bg-primary text-white rounded-3">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <span class="text-muted fs-11 fw-bold text-uppercase d-block">Nasabah</span>
                            <h4 class="fw-bold text-dark mb-0">{{ number_format($totalNasabah) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card border-0 rounded-4 shadow-sm p-3 bg-white luxury-hover">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-shape-sm bg-warning text-dark rounded-3">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                        <div>
                            <span class="text-muted fs-11 fw-bold text-uppercase d-block">Pinjaman</span>
                            <h4 class="fw-bold text-dark mb-0">{{ number_format($totalPinjaman) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card border-0 rounded-4 shadow-sm p-3 bg-white luxury-hover">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-shape-sm bg-dark text-white rounded-3">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                        <div>
                            <span class="text-muted fs-11 fw-bold text-uppercase d-block">Wilayah</span>
                            <h4 class="fw-bold text-dark mb-0">{{ number_format($totalWilayah) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card border-0 rounded-4 shadow-sm p-3 bg-white luxury-hover">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-shape-sm bg-info text-white rounded-3">
                            <i class="fas fa-coins"></i>
                        </div>
                        <div>
                            <span class="text-muted fs-11 fw-bold text-uppercase d-block">Total Modal</span>
                            <h4 class="fw-bold text-dark mb-0 fs-15">Rp {{ number_format($totalModal, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- PORTOFOLIO STATUS PINJAMAN                   --}}
        {{-- ============================================ --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card border-0 rounded-4 shadow-sm bg-white p-3 border-start border-primary border-4 luxury-hover">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fw-bold fs-11 text-uppercase d-block">Pinjaman Aktif</span>
                            <h3 class="fw-bold text-primary mb-0">{{ $pinjamanAktif }}</h3>
                            <span class="text-muted fs-12"><i class="fas fa-sync fa-spin text-primary me-1"></i>Angsuran Berjalan</span>
                        </div>
                        <div class="p-3 rounded-circle bg-primary bg-opacity-10 text-primary">
                            <i class="fas fa-file-contract fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card border-0 rounded-4 shadow-sm bg-white p-3 border-start border-success border-4 luxury-hover">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fw-bold fs-11 text-uppercase d-block">Pinjaman Lunas</span>
                            <h3 class="fw-bold text-success mb-0">{{ $pinjamanLunas }}</h3>
                            <span class="text-muted fs-12"><i class="fas fa-check-circle text-success me-1"></i>Selesai Tuntas</span>
                        </div>
                        <div class="p-3 rounded-circle bg-success bg-opacity-10 text-success">
                            <i class="fas fa-check-double fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card border-0 rounded-4 shadow-sm bg-white p-3 border-start border-danger border-4 luxury-hover">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fw-bold fs-11 text-uppercase d-block">Pinjaman Macet</span>
                            <h3 class="fw-bold text-danger mb-0">{{ $pinjamanMacet }}</h3>
                            <span class="text-muted fs-12"><i class="fas fa-exclamation-triangle text-danger me-1"></i>Perlu Penanganan DC</span>
                        </div>
                        <div class="p-3 rounded-circle bg-danger bg-opacity-10 text-danger">
                            <i class="fas fa-user-times fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- RINGKASAN NOMINAL FINANSIAL                  --}}
        {{-- ============================================ --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card border-0 rounded-4 shadow-sm p-3 bg-white border-top border-primary border-3">
                    <span class="text-muted fw-bold fs-11 text-uppercase d-block mb-1">Total Nominal Disalurkan</span>
                    <h4 class="fw-bold text-primary mb-0">Rp {{ number_format($totalNominalPinjaman, 0, ',', '.') }}</h4>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card border-0 rounded-4 shadow-sm p-3 bg-white border-top border-warning border-3">
                    <span class="text-muted fw-bold fs-11 text-uppercase d-block mb-1">Sisa Tagihan Outstanding</span>
                    <h4 class="fw-bold text-warning mb-0">Rp {{ number_format($totalSisaTagihan, 0, ',', '.') }}</h4>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card border-0 rounded-4 shadow-sm p-3 bg-white border-top border-success border-3">
                    <span class="text-muted fw-bold fs-11 text-uppercase d-block mb-1">Total Angsuran Terbayar</span>
                    <h4 class="fw-bold text-success mb-0">Rp {{ number_format($totalAngsuranTerbayar, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- GRAFIK ANALITIK ARUS KAS                     --}}
        {{-- ============================================ --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 rounded-4 shadow-sm bg-white">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="fw-bold text-dark mb-0">
                                    <i class="fas fa-chart-line text-indigo me-2"></i>Tren Arus Kas 7 Hari Terakhir
                                </h5>
                                <p class="text-muted fs-12 mb-0">Perbandingan pergerakan Kas Masuk & Kas Keluar Harian</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div style="height: 320px; position: relative;">
                            <canvas id="grafikKas"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- ANGSURAN JATUH TEMPO MINGGU INI              --}}
        {{-- ============================================ --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 rounded-4 shadow-sm bg-white overflow-hidden">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="fw-bold text-dark mb-0">
                                    <i class="fas fa-exclamation-circle text-amber me-2"></i>Angsuran Jatuh Tempo Minggu Ini
                                </h5>
                                <p class="text-muted fs-12 mb-0">Daftar tagihan yang harus ditagih dalam rentang minggu berjalan</p>
                            </div>
                            <span class="badge bg-amber-subtle text-amber px-3 py-2 rounded-pill fs-12 fw-bold">
                                {{ $jatuhTempoMingguIni->count() }} Tagihan Pending
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if ($jatuhTempoMingguIni->count() > 0)
                            <div class="table-responsive">
                                <table class="table align-middle custom-luxury-table mb-0 fs-13">
                                    <thead>
                                        <tr>
                                            <th class="ps-4">NASABAH</th>
                                            <th class="text-center">ANGSURAN KE</th>
                                            <th class="text-center">JATUH TEMPO</th>
                                            <th class="text-end">NOMINAL TAGIHAN</th>
                                            <th class="text-center pe-4" width="130">AKSI</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($jatuhTempoMingguIni as $item)
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="fw-bold text-dark">{{ $item->pinjaman->nasabah->nama ?? '-' }}</div>
                                                    <small class="text-muted fs-11"><i class="fas fa-id-card me-1"></i>{{ $item->pinjaman->nasabah->kode_nasabah ?? 'Nasabah' }}</small>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-light text-dark border font-monospace">Minggu #{{ $item->minggu_ke }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-rose-subtle text-rose px-3 py-1 rounded-pill font-monospace fw-bold">
                                                        {{ \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->translatedFormat('d M Y') }}
                                                    </span>
                                                </td>
                                                <td class="text-end fw-bold text-dark font-monospace">
                                                    Rp {{ number_format($item->jumlah_tagihan, 0, ',', '.') }}
                                                </td>
                                                <td class="text-center pe-4">
                                                    <a href="{{ url('angsuran/bayar/' . $item->id) }}" class="btn btn-sm btn-emerald-gradient rounded-pill px-3 shadow-sm text-white">
                                                        <i class="fas fa-money-bill-wave me-1"></i> Bayar
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="empty-icon-box mx-auto mb-3 text-emerald bg-emerald-subtle rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="fas fa-check-double fa-2x"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Semua Tagihan Aman</h6>
                                <p class="text-muted fs-12 mb-0">Tidak ada angsuran jatuh tempo untuk minggu ini.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- TRANSAKSI TERBARU (MASUK & KELUAR)          --}}
        {{-- ============================================ --}}
        <div class="row g-4">
            {{-- KAS MASUK TERBARU --}}
            <div class="col-12 col-lg-6">
                <div class="card border-0 rounded-4 shadow-sm bg-white h-100 overflow-hidden">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                        <h5 class="fw-bold text-emerald mb-0">
                            <i class="fas fa-arrow-down me-2"></i>Kas Masuk Terbaru
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @if ($kasMasukTerbaru->count() > 0)
                            <div class="table-responsive">
                                <table class="table align-middle custom-luxury-table mb-0 fs-13">
                                    <thead>
                                        <tr>
                                            <th class="ps-4">TANGGAL</th>
                                            <th>JENIS</th>
                                            <th class="text-end pe-4">NOMINAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($kasMasukTerbaru as $item)
                                            <tr>
                                                <td class="ps-4 text-muted font-monospace">
                                                    {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                                                </td>
                                                <td class="fw-bold text-dark">{{ $item->jenis }}</td>
                                                <td class="text-end pe-4 text-emerald fw-bold font-monospace">
                                                    + Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center py-4 mb-0 fs-13">Belum ada transaksi kas masuk.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- KAS KELUAR TERBARU --}}
            <div class="col-12 col-lg-6">
                <div class="card border-0 rounded-4 shadow-sm bg-white h-100 overflow-hidden">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                        <h5 class="fw-bold text-rose mb-0">
                            <i class="fas fa-arrow-up me-2"></i>Kas Keluar Terbaru
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @if ($kasKeluarTerbaru->count() > 0)
                            <div class="table-responsive">
                                <table class="table align-middle custom-luxury-table mb-0 fs-13">
                                    <thead>
                                        <tr>
                                            <th class="ps-4">TANGGAL</th>
                                            <th>JENIS</th>
                                            <th class="text-end pe-4">NOMINAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($kasKeluarTerbaru as $item)
                                            <tr>
                                                <td class="ps-4 text-muted font-monospace">
                                                    {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                                                </td>
                                                <td class="fw-bold text-dark">{{ $item->jenis }}</td>
                                                <td class="text-end pe-4 text-rose fw-bold font-monospace">
                                                    - Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center py-4 mb-0 fs-13">Belum ada transaksi kas keluar.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ============================================ -->
    <!-- STYLES & COMPACT BANNER FINISHING            -->
    <!-- ============================================ -->
    <style>
        .fs-10 { font-size: 10px; }
        .fs-11 { font-size: 11px; }
        .fs-12 { font-size: 12px; }
        .fs-13 { font-size: 13px; }
        .fs-14 { font-size: 14px; }
        .fs-15 { font-size: 15px; }
        .fs-16 { font-size: 16px; }
        .fs-22 { font-size: 22px; }
        .letter-spacing-tight { letter-spacing: -0.8px; }
        .tracking-wide { letter-spacing: 0.8px; }
        .tracking-wider { letter-spacing: 1px; }

        /* PALET WARNA */
        :root {
            --emerald: #059669;
            --emerald-subtle: rgba(5, 150, 105, 0.1);
            --indigo: #4f46e5;
            --indigo-subtle: rgba(79, 70, 229, 0.1);
            --rose: #e11d48;
            --rose-subtle: rgba(225, 29, 72, 0.1);
            --amber: #d97706;
            --amber-subtle: rgba(217, 119, 6, 0.1);
        }

        .text-emerald { color: var(--emerald) !important; }
        .bg-emerald-subtle { background-color: var(--emerald-subtle) !important; }
        .text-indigo { color: var(--indigo) !important; }
        .bg-indigo-subtle { background-color: var(--indigo-subtle) !important; }
        .text-rose { color: var(--rose) !important; }
        .bg-rose-subtle { background-color: var(--rose-subtle) !important; }
        .text-amber { color: var(--amber) !important; }
        .bg-amber-subtle { background-color: var(--amber-subtle) !important; }

        /* HERO BANNER COMPACT STYLES */
        .glass-time-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            min-width: 165px;
        }

        .header-glow-orb {
            position: absolute;
            top: -40px; right: -20px;
            width: 220px; height: 220px;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.25) 0%, rgba(24, 101, 242, 0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .pulse-status-dot {
            width: 7px; height: 7px;
            background-color: #34d399;
            border-radius: 50%;
            box-shadow: 0 0 0 0 rgba(52, 211, 153, 0.7);
            animation: pulseGlow 1.8s infinite;
        }

        @keyframes pulseGlow {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(52, 211, 153, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(52, 211, 153, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(52, 211, 153, 0); }
        }

        /* STAT CARDS */
        .luxury-stat-card {
            background: #ffffff;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(226, 232, 240, 0.8) !important;
        }

        .luxury-stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05) !important;
        }

        .card-glow-emerald:hover { border-color: var(--emerald) !important; }
        .card-glow-indigo:hover { border-color: var(--indigo) !important; }
        .card-glow-rose:hover { border-color: var(--rose) !important; }
        .card-glow-amber:hover { border-color: var(--amber) !important; }

        .icon-shape {
            width: 48px; height: 48px;
            display: flex; align-items: center; justify-content: center;
        }

        .icon-shape-sm {
            width: 40px; height: 40px;
            display: flex; align-items: center; justify-content: center;
        }

        .luxury-hover {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .luxury-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05) !important;
        }

        /* CUSTOM LUXURY TABLE */
        .custom-luxury-table thead th {
            background-color: #f8fafc;
            color: #64748b;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.8px;
            padding: 12px 16px;
            border-bottom: 1px solid #e2e8f0;
        }

        .custom-luxury-table tbody td {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
        }

        .custom-luxury-table tbody tr:last-child td {
            border-bottom: none;
        }

        .custom-luxury-table tbody tr:hover {
            background-color: #f8fafc;
        }

        .btn-emerald-gradient {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            transition: all 0.2s ease;
        }
        .btn-emerald-gradient:hover {
            transform: scale(1.03);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .empty-icon-box {
            width: 64px; height: 64px;
        }
    </style>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Live Server Clock
            function updateServerClock() {
                const now = new Date();
                const h = String(now.getHours()).padStart(2, '0');
                const m = String(now.getMinutes()).padStart(2, '0');
                const s = String(now.getSeconds()).padStart(2, '0');
                const clockEl = document.getElementById('hero-live-clock');
                if(clockEl) clockEl.textContent = `${h}:${m}:${s}`;
            }
            updateServerClock();
            setInterval(updateServerClock, 1000);

            // Chart JS Implementation
            const ctx = document.getElementById('grafikKas').getContext('2d');

            const gradientMasuk = ctx.createLinearGradient(0, 0, 0, 300);
            gradientMasuk.addColorStop(0, 'rgba(16, 185, 129, 0.35)');
            gradientMasuk.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

            const gradientKeluar = ctx.createLinearGradient(0, 0, 0, 300);
            gradientKeluar.addColorStop(0, 'rgba(225, 29, 72, 0.35)');
            gradientKeluar.addColorStop(1, 'rgba(225, 29, 72, 0.0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($grafikLabels),
                    datasets: [
                        {
                            label: 'Kas Masuk',
                            data: @json($grafikMasuk),
                            borderColor: '#10b981',
                            backgroundColor: gradientMasuk,
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#10b981'
                        },
                        {
                            label: 'Kas Keluar',
                            data: @json($grafikKeluar),
                            borderColor: '#e11d48',
                            backgroundColor: gradientKeluar,
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#e11d48'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 8,
                                font: { family: 'Public Sans', size: 12, weight: 'bold' }
                            }
                        }
                    },
                    scales: {
                        x: { 
                            grid: { display: false },
                            ticks: { font: { family: 'Public Sans', size: 11 } }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { borderDash: [4, 4], color: '#f1f5f9' },
                            ticks: {
                                font: { family: 'Public Sans', size: 11 },
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
@endpush