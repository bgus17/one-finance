<!-- SIDEBAR BIRU FULL FIXED HEIGHT -->
<aside class="sidebar-curved-blue" id="sidebar">
    <div class="d-flex flex-column h-100 p-3">

        {{-- TOMBOL KAPSUL PUTIH --}}
        <div class="px-1 pt-1 mb-3">
            <a href="{{ route('pinjaman.index') }}"
                class="btn btn-white-pill w-100 shadow-sm d-flex align-items-center justify-content-center gap-2 fw-bold text-primary py-2.5">
                <i class="fas fa-plus fs-13"></i>
                <span class="nav-text">Tambah Pinjaman</span>
            </a>
        </div>

        {{-- MENU NAVIGASI DENGAN INTERNAL SCROLLBAR JIKA MENU BANYAK --}}
        <div class="sidebar-menu-scroll flex-grow-1 px-1">
            <ul class="nav flex-column gap-1.5">

                <li class="nav-item">
                    <a href="/dashboard"
                        class="nav-link-saas {{ request()->routeIs('admin.dashboard') || request()->routeIs('kepala.dashboard') ? 'active' : '' }}"
                        title="Dashboard">
                        <i class="fas fa-chart-line nav-icon"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="/nasabah" class="nav-link-saas {{ request()->routeIs('nasabah*') ? 'active' : '' }}"
                        title="Data Nasabah">
                        <i class="fas fa-users nav-icon"></i>
                        <span class="nav-text">Data Nasabah</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="/dc"
                        class="nav-link-saas {{ request()->routeIs('dc*') && !request()->routeIs('dc.import*') ? 'active' : '' }}"
                        title="Data DC">
                        <i class="fas fa-user-tie nav-icon"></i>
                        <span class="nav-text">Data DC</span>
                    </a>
                </li>

                {{-- MENU IMPORT DC --}}
                <li class="nav-item">
                    <a href="{{ route('import.dc') }}"
                        class="nav-link-saas {{ request()->routeIs('import.dc*') ? 'active' : '' }}" title="Import DC">
                        <i class="fas fa-file-import nav-icon"></i>
                        <span class="nav-text">Import DC</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="/wilayah" class="nav-link-saas {{ request()->routeIs('wilayah*') ? 'active' : '' }}"
                        title="Wilayah">
                        <i class="fas fa-map-marked-alt nav-icon"></i>
                        <span class="nav-text">Wilayah</span>
                    </a>
                </li>

              {{-- 🔥 MENU KHUSUS KEPALA (Hanya Tampil Jika Login Sebagai Kepala) --}}
                @if(auth()->check() && auth()->user()->role === 'kepala')
                    <li class="nav-item">
                        <a href="/modal" class="nav-link-saas {{ request()->routeIs('modal*') ? 'active' : '' }}" title="Modal Kas">
                            <i class="fas fa-coins nav-icon"></i>
                            <span class="nav-text">Modal Kas</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/kas-keluar" class="nav-link-saas {{ request()->routeIs('kas-keluar*') ? 'active' : '' }}" title="Kas Keluar">
                            <i class="fas fa-arrow-circle-up nav-icon"></i>
                            <span class="nav-text">Kas Keluar</span>
                        </a>
                    </li>
                @endif

                <li class="nav-item">
                    <a href="/pinjaman" class="nav-link-saas {{ request()->routeIs('pinjaman*') ? 'active' : '' }}"
                        title="Pinjaman">
                        <i class="fas fa-hand-holding-usd nav-icon"></i>
                        <span class="nav-text">Pinjaman</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="/pembayaran" class="nav-link-saas {{ request()->routeIs('pembayaran*') ? 'active' : '' }}"
                        title="Pembayaran">
                        <i class="fas fa-money-bill-wave nav-icon"></i>
                        <span class="nav-text">Pembayaran</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="/dkh" class="nav-link-saas {{ request()->routeIs('dkh*') ? 'active' : '' }}"
                        title="DKH DC">
                        <i class="fas fa-file-invoice-dollar nav-icon"></i>
                        <span class="nav-text">DKH DC</span>
                    </a>
                </li>

                {{-- SUB-MENU LAPORAN --}}
                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#collapseLaporan"
                        class="nav-link-saas {{ request()->routeIs('laporan*') || request()->routeIs('laporan-keuangan*') ? '' : 'collapsed' }}"
                        aria-expanded="{{ request()->routeIs('laporan*') || request()->routeIs('laporan-keuangan*') ? 'true' : 'false' }}"
                        title="Laporan">
                        <i class="fas fa-chart-pie nav-icon"></i>
                        <span class="nav-text">Laporan</span>
                        <i class="fas fa-chevron-down caret-icon ms-auto"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('laporan*') || request()->routeIs('laporan-keuangan*') ? 'show' : '' }}"
                        id="collapseLaporan">
                        <ul class="nav flex-column ps-3 mt-1 gap-1 border-start border-white-20 ms-3">
                            <li>
                                <a href="{{ route('laporan.index') }}"
                                    class="sub-link-saas {{ request()->routeIs('laporan.index') ? 'active' : '' }}">
                                    Dashboard Laporan
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('laporan.kas') }}"
                                    class="sub-link-saas {{ request()->routeIs('laporan.kas') ? 'active' : '' }}">
                                    Laporan Kas
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('laporan.pinjaman') }}"
                                    class="sub-link-saas {{ request()->routeIs('laporan.pinjaman') ? 'active' : '' }}">
                                    Laporan Pinjaman
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('laporan.dkh') }}"
                                    class="sub-link-saas {{ request()->routeIs('laporan.dkh') ? 'active' : '' }}">
                                    Laporan DKH
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('laporan.labarugi') }}"
                                    class="sub-link-saas {{ request()->routeIs('laporan.labarugi') ? 'active' : '' }}">
                                    Laporan Laba Rugi
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('laporan-keuangan.index') }}"
                                    class="sub-link-saas {{ request()->routeIs('laporan-keuangan*') ? 'active' : '' }}">
                                    Laporan Lengkap
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

            </ul>
        </div>

        {{-- SIGN OUT AT BOTTOM --}}
        <div class="mt-auto pt-3 border-top border-white-20">
            <form action="{{ url('/logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="btn btn-logout-saas w-100 rounded-pill d-flex align-items-center justify-content-center gap-2 py-2 border-0"
                    title="Sign Out">
                    <i class="fas fa-power-off"></i>
                    <span class="nav-text fw-bold fs-13">Sign Out</span>
                </button>
            </form>
        </div>

    </div>
</aside>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<style>
    /* SIDEBAR KUNCI TINGGI 100% CONTAINER PAS DI LAYAR */
    .sidebar-curved-blue {
        width: var(--sidebar-width, 250px);
        background-color: #1865f2 !important;
        border-top-right-radius: 40px;
        height: 100% !important;
        /* Terkunci 100% sesuai tinggi app-body-container */
        flex-shrink: 0;
        z-index: 100;
        overflow: hidden;
        transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .sidebar-menu-scroll {
        overflow-y: auto;
        overflow-x: hidden;
    }

    .sidebar-menu-scroll::-webkit-scrollbar {
        width: 4px;
    }

    .sidebar-menu-scroll::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.25);
        border-radius: 10px;
    }

    .btn-white-pill {
        background-color: #ffffff !important;
        color: #1865f2 !important;
        border-radius: 30px !important;
        font-size: 13.5px;
        border: none !important;
        transition: all 0.25s ease;
    }

    .btn-white-pill:hover {
        background-color: #f8fafc !important;
        transform: translateY(-2px);
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.15) !important;
    }

    .nav-link-saas {
        display: flex;
        align-items: center;
        padding: 10px 16px;
        color: rgba(255, 255, 255, 0.85);
        border-radius: 12px;
        text-decoration: none;
        font-size: 13.5px;
        font-weight: 600;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .nav-link-saas .nav-icon {
        width: 24px;
        font-size: 15px;
        color: rgba(255, 255, 255, 0.75);
    }

    .nav-link-saas:hover {
        background-color: rgba(255, 255, 255, 0.12);
        color: #ffffff;
    }

    .nav-link-saas.active {
        background-color: rgba(255, 255, 255, 0.22) !important;
        color: #ffffff !important;
        font-weight: 800;
    }

    .nav-link-saas.active .nav-icon {
        color: #ffffff !important;
    }

    .nav-link-saas .caret-icon {
        font-size: 11px;
        transition: transform 0.3s ease;
    }

    .nav-link-saas.collapsed .caret-icon {
        transform: rotate(-90deg);
    }

    .sub-link-saas {
        display: block;
        padding: 6px 12px;
        color: rgba(255, 255, 255, 0.75);
        font-size: 12.5px;
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .sub-link-saas:hover,
    .sub-link-saas.active {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.15);
        font-weight: 700;
    }

    .border-white-20 {
        border-color: rgba(255, 255, 255, 0.15) !important;
    }

    .btn-logout-saas {
        background-color: rgba(255, 255, 255, 0.12);
        color: #ffffff;
        transition: all 0.2s ease;
    }

    .btn-logout-saas:hover {
        background-color: #ef4444;
        color: #ffffff;
    }

    /* DESKTOP COLLAPSED */
    @media (min-width: 992px) {
        body.sidebar-collapsed .sidebar-curved-blue {
            width: var(--sidebar-collapsed-width, 80px) !important;
        }

        body.sidebar-collapsed .sidebar-curved-blue .nav-text,
        body.sidebar-collapsed .sidebar-curved-blue .caret-icon,
        body.sidebar-collapsed .sidebar-curved-blue .collapse {
            display: none !important;
        }

        body.sidebar-collapsed .sidebar-curved-blue .btn-white-pill {
            padding: 10px 0 !important;
            border-radius: 50% !important;
            width: 42px !important;
            height: 42px !important;
            margin: 0 auto;
        }

        body.sidebar-collapsed .sidebar-curved-blue .nav-link-saas {
            justify-content: center;
            padding: 12px 0;
        }

        body.sidebar-collapsed .sidebar-curved-blue .nav-link-saas .nav-icon {
            margin: 0;
        }
    }

    /* MOBILE VIEW */
    @media (max-width: 991.98px) {
        .sidebar-curved-blue {
            position: fixed !important;
            top: 0;
            left: 0;
            bottom: 0;
            height: 100vh !important;
            transform: translateX(-100%);
            border-top-right-radius: 0;
            z-index: 1045;
        }

        body.sidebar-open-mobile .sidebar-curved-blue {
            transform: translateX(0) !important;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(4px);
            z-index: 1040;
        }

        body.sidebar-open-mobile .sidebar-overlay {
            display: block !important;
        }
    }
</style>
