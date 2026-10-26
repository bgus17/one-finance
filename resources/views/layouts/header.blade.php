<!-- HEADER PUTIH WITH ENLARGED LKM & DATETIME WIDGET -->
<header class="header-saas bg-white px-3 px-md-4 py-2 border-bottom border-light">
    <div class="d-flex align-items-center justify-content-between gap-3">

        {{-- 1. TOGGLE BUTTON & BRANDING LOGO --}}
        <div class="d-flex align-items-center gap-3" style="min-width: 210px;">
            <button type="button" class="btn-toggle-saas" id="sidebarToggle" title="Toggle Sidebar">
                <i class="fas fa-bars"></i>
            </button>

            <a href="{{ url('/') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                <img src="{{ asset('assets/img/one-finance.png') }}" alt="Logo ONE FINANCE" class="brand-logo-img">
                <span class="brand-title-text">ONE FINANCE</span>
            </a>
        </div>

        {{-- 2. BADGE LKM & WIDGET WAKTU --}}
        <div class="d-none d-md-flex align-items-center gap-4 mx-auto">
            
            <!-- Badge Lembaga Keuangan Mikro -->
            <div class="lkm-badge-capsule d-flex align-items-center gap-2.5 px-3.5 py-2 rounded-pill shadow-sm">
                <i class="fas fa-university text-primary fs-15"></i>
                <span class="lkm-text-gradient fw-extrabold fs-13 text-uppercase tracking-wider">Lembaga Keuangan Mikro</span>
            </div>

            <!-- Separator Line -->
            <div class="vr opacity-20 d-none d-lg-block" style="height: 24px;"></div>

            <!-- Widget Hari, Tanggal, & Jam Realtime -->
            <div class="datetime-widget-capsule d-none d-lg-flex align-items-center gap-3 bg-light px-4 py-2 rounded-pill border">
                <div class="d-flex align-items-center gap-2">
                    <span class="live-pulse-indicator me-1"></span>
                    <i class="far fa-calendar-alt text-primary fs-14"></i>
                    <span id="header-day-date" class="fw-bold text-dark fs-13 ms-1">--</span>
                </div>
                
                <div class="vr opacity-25 mx-1" style="height: 16px;"></div>
                
                <div class="d-flex align-items-center gap-2">
                    <i class="far fa-clock text-primary fs-14"></i>
                    <span id="header-time-clock" class="fw-extrabold text-primary fs-13">00:00:00 WIB</span>
                </div>
            </div>

        </div>

        {{-- 3. MENU KANAN: NOTIFIKASI, BANTUAN & DROPDOWN PROFIL --}}
        <div class="d-flex align-items-center gap-3">

            {{-- NOTIFIKASI --}}
            <div class="dropdown">
                <button class="btn-icon-circle position-relative" type="button" id="dropdownNotif" data-bs-toggle="dropdown" aria-expanded="false" title="Notifikasi">
                    <i class="far fa-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                        <span class="visually-hidden">Notifikasi Baru</span>
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-3 mt-2" aria-labelledby="dropdownNotif" style="width: 280px;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="fw-bold mb-0 fs-13">Notifikasi</h6>
                        <span class="badge bg-primary bg-opacity-10 text-primary fs-11">2 Baru</span>
                    </div>
                    <hr class="my-2 text-muted opacity-25">
                    <div class="d-flex flex-column gap-2 fs-12">
                        <a href="#" class="text-decoration-none text-dark p-2 rounded-3 bg-light d-block">
                            <strong class="d-block text-primary">Pinjaman Disetujui</strong>
                            <span class="text-muted fs-11">Pengajuan An. Budi Utomo telah disetujui.</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- BANTUAN --}}
            <a href="#" class="btn-icon-circle text-decoration-none" title="Bantuan" onclick="alert('Pusat Bantuan & Panduan Penggunaan SIKOPIN'); return false;">
                <i class="far fa-question-circle"></i>
            </a>

            {{-- DROPDOWN PROFIL USER AKTIF --}}
            <div class="dropdown ms-1">
                <button class="btn p-0 border-0 d-flex align-items-center gap-2.5 shadow-none" type="button" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="avatar-capsule bg-primary text-white fw-bold shadow-sm">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="d-none d-md-flex flex-column text-start ms-0.5">
                        <span class="fs-13 fw-bold text-dark lh-1">{{ Auth::user()->name ?? 'Administrator' }}</span>
                        <span class="text-muted fs-11 mt-1">{{ ucfirst(Auth::user()->role ?? 'Kepala') }}</span>
                    </div>
                    <i class="fas fa-chevron-down text-muted fs-11 ms-1 d-none d-md-inline-block"></i>
                </button>

                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-2 mt-2 fs-13" aria-labelledby="dropdownUser" style="min-width: 220px;">
                    <li>
                        <div class="px-3 py-2 border-bottom mb-1">
                            <p class="fw-bold text-dark mb-0 fs-13">{{ Auth::user()->name ?? 'Administrator' }}</p>
                            <p class="text-muted fs-11 mb-0">{{ Auth::user()->email ?? 'admin@sikopin.com' }}</p>
                        </div>
                    </li>
                    <li>
                        <a class="dropdown-menu-item d-flex align-items-center gap-2 px-3 py-2 rounded-3 text-dark text-decoration-none" href="{{ route('profile.settings') }}">
                            <i class="far fa-user text-muted width-16"></i>
                            <span>Profil Saya</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-menu-item d-flex align-items-center gap-2 px-3 py-2 rounded-3 text-dark text-decoration-none" href="{{ route('profile.settings') }}">
                            <i class="fas fa-cog text-muted width-16"></i>
                            <span>Pengaturan & Password</span>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <form action="{{ url('/logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-menu-item d-flex align-items-center gap-2 px-3 py-2 rounded-3 text-danger border-0 bg-transparent w-100 text-start">
                                <i class="fas fa-power-off width-16"></i>
                                <span class="fw-bold">Sign Out</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>

        </div>

    </div>
</header>

<style>
    .header-saas {
        background-color: #ffffff !important;
        position: relative;
        z-index: 10;
        height: 68px;
    }

    .btn-toggle-saas {
        width: 38px; height: 38px;
        border-radius: 10px;
        border: none;
        background: #f1f5f9;
        color: #1865f2;
        font-size: 15px;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-toggle-saas:hover {
        background: #1865f2;
        color: #ffffff;
    }

    .brand-logo-img {
        height: 32px;
        width: auto;
        object-fit: contain;
    }

    .brand-title-text {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 800;
        font-size: 19px;
        color: #1865f2;
        letter-spacing: -0.4px;
    }

    /* BADGE LKM KEREN DENGAN UKURAN LEBIH BESAR */
    .lkm-badge-capsule {
        background: linear-gradient(135deg, rgba(24, 101, 242, 0.08) 0%, rgba(24, 101, 242, 0.03) 100%);
        border: 1px solid rgba(24, 101, 242, 0.22);
        transition: all 0.3s ease;
    }

    .lkm-badge-capsule:hover {
        box-shadow: 0 4px 14px rgba(24, 101, 242, 0.15) !important;
        transform: translateY(-1px);
    }

    .lkm-text-gradient {
        background: linear-gradient(135deg, #1865f2 0%, #0c4a6e 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-family: 'Plus Jakarta Sans', sans-serif;
        letter-spacing: 0.8px;
    }

    /* DATETIME WIDGET CAPSULE LEBIH BESAR */
    .datetime-widget-capsule {
        background-color: #f8fafc !important;
        border-color: #e2e8f0 !important;
        font-family: 'Plus Jakarta Sans', sans-serif;
        box-shadow: 0 2px 6px rgba(0,0,0,0.02);
    }

    /* LIVE PULSE INDICATOR */
    .live-pulse-indicator {
        width: 8px;
        height: 8px;
        background-color: #10b981;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: pulse-green-ring 2s infinite;
    }

    @keyframes pulse-green-ring {
        0% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        }
        70% {
            transform: scale(1);
            box-shadow: 0 0 0 7px rgba(16, 185, 129, 0);
        }
        100% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
        }
    }

    .btn-icon-circle {
        width: 38px; height: 38px;
        border-radius: 50%;
        border: none;
        background: transparent;
        color: #64748b;
        font-size: 16px;
        display: flex; align-items: center; justify-content: center;
        transition: all 0.2s ease;
    }

    .btn-icon-circle:hover {
        background: #f1f5f9;
        color: #1865f2;
    }

    .avatar-capsule {
        width: 38px; height: 38px;
        border-radius: 50%;
        background-color: #1865f2 !important;
        display: flex; align-items: center; justify-content: center;
        font-size: 15px;
    }

    .dropdown-menu-item {
        transition: all 0.2s ease;
    }

    .dropdown-menu-item:hover {
        background-color: #f1f5f9;
    }

    .width-16 {
        width: 16px;
        text-align: center;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle Sidebar Logic
        const toggleBtn = document.getElementById('sidebarToggle');
        const overlay = document.getElementById('sidebarOverlay');

        if (window.innerWidth >= 992) {
            const isCollapsed = localStorage.getItem('sidebar_collapsed') === 'true';
            if (isCollapsed) {
                document.body.classList.add('sidebar-collapsed');
            }
        }

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (window.innerWidth >= 992) {
                    document.body.classList.toggle('sidebar-collapsed');
                    localStorage.setItem('sidebar_collapsed', document.body.classList.contains('sidebar-collapsed'));
                } else {
                    document.body.classList.toggle('sidebar-open-mobile');
                }
            });
        }

        if (overlay) {
            overlay.addEventListener('click', function() {
                document.body.classList.remove('sidebar-open-mobile');
            });
        }

        // Realtime Hari, Tanggal & Jam Bahasa Indonesia
        function updateHeaderDateTime() {
            const now = new Date();
            
            const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
            const formattedDate = now.toLocaleDateString('id-ID', options);
            
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const formattedTime = `${hours}:${minutes}:${seconds} WIB`;

            const dateElem = document.getElementById('header-day-date');
            const clockElem = document.getElementById('header-time-clock');

            if (dateElem) dateElem.textContent = formattedDate;
            if (clockElem) clockElem.textContent = formattedTime;
        }

        updateHeaderDateTime();
        setInterval(updateHeaderDateTime, 1000);
    });
</script>