@extends('layouts.app')

@section('title', 'Dashboard Kepala')

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

    // ============================================
    // 2. DATA KAS MASUK & KELUAR (BULAN INI)
    // ============================================
    $bulanIni = Carbon::now()->startOfMonth();
    $bulanDepan = Carbon::now()->endOfMonth();

    $totalKasMasuk = KasMasuk::whereBetween('tanggal', [$bulanIni, $bulanDepan])->sum('nominal');
    $totalKasKeluar = KasKeluar::whereBetween('tanggal', [$bulanIni, $bulanDepan])->sum('nominal');

    // ============================================
    // 3. DATA NASABAH
    // ============================================
    $totalNasabah = Nasabah::count();
    $totalNasabahAktif = Nasabah::where('status', 'aktif')->count();

    // ============================================
    // 4. DATA PINJAMAN
    // ============================================
    $totalPinjaman = Pinjaman::count();
    $pinjamanAktif = Pinjaman::where('status', 'aktif')->count();
    $pinjamanLunas = Pinjaman::where('status', 'lunas')->count();
    $pinjamanMacet = Pinjaman::where('status', 'macet')->count();

    // ============================================
    // 5. TOTAL NOMINAL PINJAMAN
    // ============================================
    $totalNominalPinjaman = Pinjaman::sum('jumlah_pinjam');
    $totalSisaTagihan = Pinjaman::sum('sisa_tagihan');
    $totalAngsuranTerbayar = Angsuran::sum('jumlah_bayar');

    // ============================================
    // 6. ANGSURAN JATUH TEMPO
    // ============================================
    $jatuhTempoMingguIni = Angsuran::with(['pinjaman.nasabah'])
        ->where('status', 'belum_bayar')
        ->whereBetween('tanggal_jatuh_tempo', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
        ->orderBy('tanggal_jatuh_tempo')
        ->limit(5)
        ->get();

    // ============================================
    // 7. TRANSAKSI TERBARU
    // ============================================
    $kasMasukTerbaru = KasMasuk::with('modal')->orderBy('created_at', 'desc')->limit(5)->get();
    $kasKeluarTerbaru = KasKeluar::with('modal')->orderBy('created_at', 'desc')->limit(5)->get();

    // ============================================
    // 8. SELISIH KAS
    // ============================================
    $totalKasMasukAll = KasMasuk::sum('nominal');
    $totalKasKeluarAll = KasKeluar::sum('nominal');
    $selisihKas = $totalKasMasukAll - $totalKasKeluarAll;

    // ============================================
    // 9. GRAFIK 7 HARI
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
@endphp

<div class="container-fluid px-2 px-md-4 py-2">

    {{-- ============================================ --}}
    {{-- HERO BANNER KEPALA COMPACT & LUXURY GLOW      --}}
    {{-- ============================================ --}}
    <div class="card border-0 text-white overflow-hidden shadow-sm mb-4" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 50%, #0c4a6e 100%); border-radius: 18px;">
        <div class="card-body p-3 p-md-3.5 position-relative">
            
            <!-- Background Glow Animation -->
            <div class="header-glow-orb-ice"></div>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 position-relative" style="z-index: 2;">
                
                {{-- KIRI: BADGE + JUDUL KEPALA --}}
                <div>
                    <div class="d-inline-flex align-items-center gap-2 px-2.5 py-1 rounded-pill bg-white bg-opacity-10 backdrop-blur border border-white border-opacity-10 mb-1.5">
                        <span class="pulse-cyan-dot"></span>
                        <span class="fs-10 fw-bold text-uppercase tracking-wider text-white">EXECUTIVE FINANCIAL MONITORING</span>
                    </div>

                    <h3 class="fw-extrabold text-white mb-0.5 fs-22 lh-1 tracking-tight">
                        <i class="fas fa-chart-line me-2 text-cyan-light fs-20"></i>Dashboard Kepala
                    </h3>
                    
                    <p class="text-white-80 fs-12 mb-0">
                        Selamat datang kembali, <strong class="text-white">{{ Auth::user()->name }}</strong>. Ringkasan keuangan koperasi Anda.
                    </p>
                </div>

                {{-- KANAN: WIDGET JAM SERVER GLASSMORPHISM --}}
                <div class="glass-time-card-ice px-3 py-2 rounded-3 text-start">
                    <span class="fs-10 fw-extrabold text-white-70 text-uppercase tracking-wider d-block mb-0.5">WAKTU SERVER</span>
                    <div class="d-flex align-items-baseline gap-1.5">
                        <span id="hero-live-clock-kepala" class="fs-16 fw-black text-white font-monospace lh-1">{{ now()->format('H:i:s') }}</span>
                        <span class="fs-10 fw-bold text-cyan-light">WIB</span>
                    </div>
                    <div class="d-flex align-items-center gap-1 mt-1 text-white-80 fs-11">
                        <i class="far fa-calendar-alt text-cyan-light"></i>
                        <span>{{ now()->translatedFormat('l, d F Y') }}</span>
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- 4 CARD UTAMA FINANSIAL --}}
    <div class="row g-3 mb-4">
        {{-- SALDO KAS --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 bg-glass-card h-100 card-hover-glow">
                <div class="card-body p-3.5">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-slate-muted fs-12 fw-extrabold text-uppercase tracking-wider">Saldo Kas</span>
                        <div class="icon-box-sm bg-emerald-subtle text-emerald rounded-3">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                    <h3 class="fw-black text-ocean-dark mb-0 fs-20">Rp {{ number_format($saldoSekarang, 0, ',', '.') }}</h3>
                    <small class="text-emerald fs-11 fw-bold mt-1 d-block"><i class="fas fa-check-circle me-1"></i>Status Kas Aktif</small>
                </div>
            </div>
        </div>

        {{-- KAS MASUK BULAN INI --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 bg-glass-card h-100 card-hover-glow">
                <div class="card-body p-3.5">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-slate-muted fs-12 fw-extrabold text-uppercase tracking-wider">Kas Masuk (Bulan Ini)</span>
                        <div class="icon-box-sm bg-ocean-subtle text-ocean-blue rounded-3">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                    </div>
                    <h3 class="fw-black text-ocean-dark mb-0 fs-20">Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</h3>
                    <small class="text-ocean-blue fs-11 fw-bold mt-1 d-block"><i class="fas fa-calendar-check me-1"></i>Periode {{ now()->format('M Y') }}</small>
                </div>
            </div>
        </div>

        {{-- KAS KELUAR BULAN INI --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 bg-glass-card h-100 card-hover-glow">
                <div class="card-body p-3.5">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-slate-muted fs-12 fw-extrabold text-uppercase tracking-wider">Kas Keluar (Bulan Ini)</span>
                        <div class="icon-box-sm bg-rose-subtle text-rose rounded-3">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                    </div>
                    <h3 class="fw-black text-ocean-dark mb-0 fs-20">Rp {{ number_format($totalKasKeluar, 0, ',', '.') }}</h3>
                    <small class="text-rose fs-11 fw-bold mt-1 d-block"><i class="fas fa-arrow-circle-up me-1"></i>Pengeluaran Operasional</small>
                </div>
            </div>
        </div>

        {{-- SELISIH KAS --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 bg-glass-card h-100 card-hover-glow">
                <div class="card-body p-3.5">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-slate-muted fs-12 fw-extrabold text-uppercase tracking-wider">Selisih Kas</span>
                        <div class="icon-box-sm bg-sky-subtle text-sky rounded-3">
                            <i class="fas fa-calculator"></i>
                        </div>
                    </div>
                    <h3 class="fw-black text-ocean-dark mb-0 fs-20">Rp {{ number_format($selisihKas, 0, ',', '.') }}</h3>
                    <small class="text-sky fs-11 fw-bold mt-1 d-block"><i class="fas fa-chart-pie me-1"></i>Akumulasi Net Kas</small>
                </div>
            </div>
        </div>
    </div>

    {{-- STATISTIK PORTFOLIO PINJAMAN --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-glass-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-circle bg-ocean-subtle text-ocean-blue">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div>
                        <div class="text-slate-muted fs-11 fw-bold text-uppercase">Total Pinjaman</div>
                        <div class="fw-black text-ocean-dark fs-18">{{ $totalPinjaman }} <small class="fs-12 text-slate-muted fw-semibold">Berkas</small></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-glass-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-circle bg-sky-subtle text-sky">
                        <i class="fas fa-running"></i>
                    </div>
                    <div>
                        <div class="text-slate-muted fs-11 fw-bold text-uppercase">Pinjaman Aktif</div>
                        <div class="fw-black text-ocean-dark fs-18">{{ $pinjamanAktif }} <small class="fs-12 text-slate-muted fw-semibold">Berjalan</small></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-glass-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-circle bg-emerald-subtle text-emerald">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <div>
                        <div class="text-slate-muted fs-11 fw-bold text-uppercase">Pinjaman Lunas</div>
                        <div class="fw-black text-emerald fs-18">{{ $pinjamanLunas }} <small class="fs-12 text-slate-muted fw-semibold">Selesai</small></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-glass-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-circle bg-rose-subtle text-rose">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div>
                        <div class="text-slate-muted fs-11 fw-bold text-uppercase">Pinjaman Macet</div>
                        <div class="fw-black text-rose fs-18">{{ $pinjamanMacet }} <small class="fs-12 text-slate-muted fw-semibold">Kolektibilitas</small></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- NASABAH & NOMINAL METRICS --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-glass-card p-3.5">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-slate-muted fs-12 fw-bold text-uppercase">Total Nasabah</span>
                    <i class="fas fa-users text-ocean-blue fs-16"></i>
                </div>
                <h4 class="fw-black text-ocean-dark mb-1 fs-22">{{ $totalNasabah }}</h4>
                <div class="badge bg-emerald-subtle text-emerald rounded-pill px-2.5 py-1 fs-11 fw-bold">
                    <i class="fas fa-user-check me-1"></i> Nasabah Aktif: {{ $totalNasabahAktif }}
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-glass-card p-3.5">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-slate-muted fs-12 fw-bold text-uppercase">Total Nominal Pinjaman</span>
                    <i class="fas fa-hand-holding-usd text-ocean-blue fs-16"></i>
                </div>
                <h4 class="fw-black text-ocean-dark mb-1 fs-22">Rp {{ number_format($totalNominalPinjaman, 0, ',', '.') }}</h4>
                <small class="text-slate-muted fs-11 fw-semibold">Total Kredit Disalurkan</small>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-glass-card p-3.5">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-slate-muted fs-12 fw-bold text-uppercase">Angsuran Terbayar</span>
                    <i class="fas fa-money-bill-wave text-emerald fs-16"></i>
                </div>
                <h4 class="fw-black text-emerald mb-1 fs-22">Rp {{ number_format($totalAngsuranTerbayar, 0, ',', '.') }}</h4>
                <small class="text-slate-muted fs-11 fw-semibold">Total Pengembalian Pokok & Bunga</small>
            </div>
        </div>
    </div>

    {{-- GRAFIK KAS 7 HARI --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 bg-glass-card">
                <div class="card-header bg-transparent border-bottom border-ice-glow p-3.5">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="fw-extrabold text-ocean-dark mb-0 fs-16">
                            <i class="fas fa-chart-bar me-2 text-ocean-blue"></i>
                            Grafik Arus Kas (7 Hari Terakhir)
                        </h5>
                        <span class="badge bg-ocean-subtle text-ocean-blue rounded-pill px-3 py-1 fs-11 fw-bold">Live Cashflow</span>
                    </div>
                </div>
                <div class="card-body p-3.5">
                    <div style="height: 320px; position: relative;">
                        <canvas id="grafikKas"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ANGSURAN JATUH TEMPO --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 bg-glass-card">
                <div class="card-header bg-transparent border-bottom border-ice-glow p-3.5">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="fw-extrabold text-amber mb-0 fs-16">
                            <i class="fas fa-clock me-2"></i>
                            Angsuran Jatuh Tempo Minggu Ini
                        </h5>
                        <span class="badge bg-amber-subtle text-amber rounded-pill px-3 py-1 fs-11 fw-bold">Priority Notice</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($jatuhTempoMingguIni->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 custom-table-ice">
                                <thead>
                                    <tr>
                                        <th class="ps-3.5">Nasabah</th>
                                        <th>Minggu Ke</th>
                                        <th>Jatuh Tempo</th>
                                        <th class="pe-3.5 text-end">Jumlah Tagihan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jatuhTempoMingguIni as $item)
                                        <tr>
                                            <td class="ps-3.5">
                                                <div class="fw-bold text-ocean-dark fs-13">{{ $item->pinjaman->nasabah->nama ?? '-' }}</div>
                                            </td>
                                            <td>
                                                <span class="badge bg-sky-subtle text-sky rounded-pill px-2.5 py-1 fs-11 fw-bold">
                                                    Minggu {{ $item->minggu_ke }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-amber-subtle text-amber rounded-3 px-2.5 py-1 fs-12 fw-bold">
                                                    <i class="fas fa-calendar-alt me-1"></i>
                                                    {{ \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->format('d/m/Y') }}
                                                </span>
                                            </td>
                                            <td class="pe-3.5 text-end fw-black text-ocean-dark fs-14">
                                                Rp {{ number_format($item->jumlah_tagihan, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="icon-circle bg-emerald-subtle text-emerald mx-auto mb-2" style="width: 48px; height: 48px;">
                                <i class="fas fa-check-circle fs-20"></i>
                            </div>
                            <p class="text-slate-muted fw-bold mb-0 fs-13">Tidak ada angsuran yang jatuh tempo minggu ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- TRANSAKSI TERBARU --}}
    <div class="row g-3">
        {{-- KAS MASUK TERBARU --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 bg-glass-card h-100">
                <div class="card-header bg-transparent border-bottom border-ice-glow p-3.5">
                    <h5 class="fw-extrabold text-emerald mb-0 fs-15">
                        <i class="fas fa-arrow-circle-down me-2"></i>
                        Kas Masuk Terbaru
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($kasMasukTerbaru->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 custom-table-ice">
                                <thead>
                                    <tr>
                                        <th class="ps-3.5">Tanggal</th>
                                        <th>Jenis Transaksi</th>
                                        <th class="pe-3.5 text-end">Nominal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($kasMasukTerbaru as $item)
                                        <tr>
                                            <td class="ps-3.5 text-slate-muted fs-12 fw-semibold">
                                                {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                                            </td>
                                            <td>
                                                <span class="fw-bold text-ocean-dark fs-13">{{ $item->jenis }}</span>
                                            </td>
                                            <td class="pe-3.5 text-end text-emerald fw-black fs-13">
                                                + Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-slate-muted fs-13">Belum ada transaksi masuk.</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- KAS KELUAR TERBARU --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 bg-glass-card h-100">
                <div class="card-header bg-transparent border-bottom border-ice-glow p-3.5">
                    <h5 class="fw-extrabold text-rose mb-0 fs-15">
                        <i class="fas fa-arrow-circle-up me-2"></i>
                        Kas Keluar Terbaru
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($kasKeluarTerbaru->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 custom-table-ice">
                                <thead>
                                    <tr>
                                        <th class="ps-3.5">Tanggal</th>
                                        <th>Jenis Transaksi</th>
                                        <th class="pe-3.5 text-end">Nominal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($kasKeluarTerbaru as $item)
                                        <tr>
                                            <td class="ps-3.5 text-slate-muted fs-12 fw-semibold">
                                                {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                                            </td>
                                            <td>
                                                <span class="fw-bold text-ocean-dark fs-13">{{ $item->jenis }}</span>
                                            </td>
                                            <td class="pe-3.5 text-end text-rose fw-black fs-13">
                                                - Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-slate-muted fs-13">Belum ada transaksi keluar.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

{{-- STYLES TEMA LUXURY ICE CYAN & BANNER COMPACT --}}
<style>
    .fs-10 { font-size: 10px; }
    .fs-11 { font-size: 11px; }
    .fs-12 { font-size: 12px; }
    .fs-13 { font-size: 13px; }
    .fs-14 { font-size: 14px; }
    .fs-15 { font-size: 15px; }
    .fs-16 { font-size: 16px; }
    .fs-20 { font-size: 20px; }
    .fs-22 { font-size: 22px; }
    .tracking-tight { letter-spacing: -0.8px; }
    .tracking-wider { letter-spacing: 1px; }

    .bg-glass-card {
        background: rgba(255, 255, 255, 0.85) !important;
        backdrop-filter: blur(12px);
        border: 1px solid rgba(186, 230, 253, 0.7) !important;
    }

    .border-ice-glow { border-color: rgba(186, 230, 253, 0.8) !important; }
    .text-ocean-dark { color: #0c4a6e !important; }
    .text-ocean-blue { color: #0284c7 !important; }
    .text-slate-muted { color: #64748b !important; }
    .text-cyan-light { color: #38bdf8 !important; }

    /* COMPACT GLASS TIME CARD ICE */
    .glass-time-card-ice {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        min-width: 165px;
    }

    .header-glow-orb-ice {
        position: absolute;
        top: -40px; right: -20px;
        width: 220px; height: 220px;
        background: radial-gradient(circle, rgba(56, 189, 248, 0.35) 0%, rgba(2, 132, 199, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .pulse-cyan-dot {
        width: 7px; height: 7px;
        background-color: #38bdf8;
        border-radius: 50%;
        box-shadow: 0 0 0 0 rgba(56, 189, 248, 0.7);
        animation: pulseCyanGlow 1.8s infinite;
    }

    @keyframes pulseCyanGlow {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(56, 189, 248, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(56, 189, 248, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(56, 189, 248, 0); }
    }

    /* ACCENT COLORS */
    .text-emerald { color: #10b981 !important; }
    .bg-emerald-subtle { background-color: rgba(16, 185, 129, 0.12) !important; }

    .text-rose { color: #f43f5e !important; }
    .bg-rose-subtle { background-color: rgba(244, 63, 94, 0.12) !important; }

    .text-sky { color: #0284c7 !important; }
    .bg-sky-subtle { background-color: rgba(2, 132, 199, 0.12) !important; }

    .text-amber { color: #d97706 !important; }
    .bg-amber-subtle { background-color: rgba(217, 119, 6, 0.12) !important; }

    .bg-ocean-subtle { background-color: rgba(2, 132, 199, 0.12) !important; }

    /* UTILITY CLASSES */
    .backdrop-blur { backdrop-filter: blur(8px); }
    .text-white-80 { color: rgba(255, 255, 255, 0.85); }
    .text-white-70 { color: rgba(255, 255, 255, 0.7); }

    .icon-box-sm {
        width: 38px; height: 38px;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px;
    }

    .icon-circle {
        width: 42px; height: 42px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 17px;
    }

    .card-hover-glow {
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .card-hover-glow:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px -6px rgba(2, 132, 199, 0.18) !important;
        border-color: #0284c7 !important;
    }

    /* CUSTOM TABLE ICE */
    .custom-table-ice thead th {
        background-color: rgba(240, 249, 255, 0.7);
        color: #0369a1;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding-top: 10px;
        padding-bottom: 10px;
        border-bottom: 1px solid rgba(186, 230, 253, 0.8);
    }

    .custom-table-ice tbody td {
        padding-top: 12px;
        padding-bottom: 12px;
        border-bottom: 1px solid rgba(226, 232, 240, 0.6);
    }

    .custom-table-ice tbody tr:last-child td { border-bottom: none; }

    .custom-table-ice tbody tr:hover td {
        background-color: rgba(240, 249, 255, 0.5);
    }
</style>

{{-- SCRIPT CHART JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Live Server Clock
        function updateKepalaServerClock() {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            const clockEl = document.getElementById('hero-live-clock-kepala');
            if(clockEl) clockEl.textContent = `${h}:${m}:${s}`;
        }
        updateKepalaServerClock();
        setInterval(updateKepalaServerClock, 1000);

        // Chart JS Implementation
        const ctx = document.getElementById('grafikKas').getContext('2d');

        // Gradient Kas Masuk
        const gradientMasuk = ctx.createLinearGradient(0, 0, 0, 300);
        gradientMasuk.addColorStop(0, 'rgba(16, 185, 129, 0.85)');
        gradientMasuk.addColorStop(1, 'rgba(16, 185, 129, 0.2)');

        // Gradient Kas Keluar
        const gradientKeluar = ctx.createLinearGradient(0, 0, 0, 300);
        gradientKeluar.addColorStop(0, 'rgba(244, 63, 94, 0.85)');
        gradientKeluar.addColorStop(1, 'rgba(244, 63, 94, 0.2)');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($grafikLabels),
                datasets: [{
                    label: 'Kas Masuk',
                    data: @json($grafikMasuk),
                    backgroundColor: gradientMasuk,
                    borderColor: '#10b981',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false
                }, {
                    label: 'Kas Keluar',
                    data: @json($grafikKeluar),
                    backgroundColor: gradientKeluar,
                    borderColor: '#f43f5e',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8,
                            padding: 20,
                            font: {
                                family: 'Public Sans',
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0c4a6e',
                        padding: 12,
                        titleFont: { size: 13, weight: 'bold' },
                        bodyFont: { size: 13 },
                        cornerRadius: 10,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rp ' + context.raw.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { family: 'Public Sans', size: 11, weight: 'bold' },
                            color: '#64748b'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(186, 230, 253, 0.4)' },
                        ticks: {
                            font: { family: 'Public Sans', size: 11 },
                            color: '#64748b',
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
@endsection