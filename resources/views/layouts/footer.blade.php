<!-- MODERN SAAS FOOTER (MATCHES SIDEBAR & HEADER) -->
<footer class="footer-saas border-top border-slate-200">
    <div class="container-fluid px-3 px-md-4 py-4 py-md-5">

        {{-- GRID UTAMA 4 KOLOM --}}
        <div class="footer-grid-saas">

            {{-- KOLOM 1: BRANDING & PERUSAHAAN --}}
            <div class="footer-brand-col">
                <a href="{{ url('/') }}" class="d-flex align-items-center gap-2 text-decoration-none mb-2.5">
                    <img src="{{ asset('assets/img/one-finance.png') }}" alt="Logo ONE FINANCE" class="footer-brand-logo">
                    <span class="footer-brand-title">ONE FINANCE</span>
                </a>
                <p class="text-muted fs-13 leading-relaxed mb-3 pe-md-3">
                    Sistem Digitalisasi Koperasi & Lembaga Keuangan Mikro Berstandar Enterprise. Terintegrasi, Aman & Realtime.
                </p>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="#" class="social-btn-saas" title="Facebook" target="_blank" rel="noopener noreferrer"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-btn-saas" title="Instagram" target="_blank" rel="noopener noreferrer"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-btn-saas" title="Twitter" target="_blank" rel="noopener noreferrer"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-btn-saas" title="YouTube" target="_blank" rel="noopener noreferrer"><i class="fab fa-youtube"></i></a>
                    <a href="https://wa.me/6281540964579" class="social-btn-saas" title="WhatsApp" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>

            {{-- KOLOM 2: QUICK LINKS --}}
            <div>
                <h6 class="footer-title-saas">PINTASAN NAVIGASI</h6>
                <ul class="footer-menu-saas">
                    <li><a href="{{ route('dashboard') }}"><i class="fas fa-chevron-right me-1.5 text-primary fs-10"></i> Dashboard Utama</a></li>
                    <li><a href="{{ route('nasabah.index') }}"><i class="fas fa-chevron-right me-1.5 text-primary fs-10"></i> Manajemen Nasabah</a></li>
                    <li><a href="{{ route('pinjaman.index') }}"><i class="fas fa-chevron-right me-1.5 text-primary fs-10"></i> Portofolio Pinjaman</a></li>
                    <li><a href="{{ route('pembayaran.index') }}"><i class="fas fa-chevron-right me-1.5 text-primary fs-10"></i> Transaksi Angsuran</a></li>
                    <li><a href="{{ route('laporan.index') }}"><i class="fas fa-chevron-right me-1.5 text-primary fs-10"></i> Audit & Laporan</a></li>
                </ul>
            </div>

            {{-- KOLOM 3: KONTAK KANTOR --}}
            <div>
                <h6 class="footer-title-saas">KONTAK OPERASIONAL</h6>
                <ul class="footer-menu-saas contact">
                    <li><i class="fas fa-phone-alt text-primary"></i> <span>(+62) 0815-4096-4579</span></li>
                    <li><i class="fas fa-phone-alt text-primary"></i> <span>(+62) 0857-6958-8302</span></li>
                    <li><i class="far fa-envelope text-primary"></i> <span>info@onefinance.com</span></li>
                    <li><i class="fas fa-map-marker-alt text-primary"></i> <span>Jl. Nusa Bakti, OKU Timur</span></li>
                </ul>
            </div>

            {{-- KOLOM 4: JAM KERJA & STATUS SERVER --}}
            <div>
                <h6 class="footer-title-saas">JAM KERJA OPERASIONAL</h6>
                <ul class="footer-menu-saas">
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">Senin - Jumat</span>
                        <strong class="text-dark">08:00 - 16:00</strong>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">Sabtu</span>
                        <strong class="text-dark">08:00 - 15:00</strong>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">Minggu</span>
                        <strong class="text-danger fw-bold">Tutup</strong>
                    </li>
                </ul>
                <div class="mt-3">
                    <div class="badge-status-saas">
                        <span class="status-pulse-dot me-2"></span>
                        Status Server Normal
                    </div>
                </div>
            </div>

        </div>

        {{-- COPYRIGHT BAR --}}
        <div class="footer-bottom-saas border-top border-slate-200 pt-3.5 mt-4">
            <div class="row align-items-center g-2 fs-12">
                <div class="col-12 col-md-6 text-center text-md-start text-muted">
                    &copy; {{ date('Y') }} <strong class="text-dark">CV. ONE FINANCE</strong>. All Rights Reserved.
                </div>
                <div class="col-12 col-md-6 text-center text-md-end text-muted">
                    <i class="fas fa-code me-1 text-primary"></i> Engine by <strong class="text-dark">APCODE</strong> | v{{ config('app.version', '1.0') }}
                </div>
            </div>
        </div>

    </div>
</footer>

<style>
    .footer-saas {
        background-color: #f8fafc !important;
        color: #334155;
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        position: relative;
        z-index: 5;
    }

    .footer-brand-logo {
        height: 28px;
        width: auto;
        object-fit: contain;
    }

    .footer-brand-title {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 800;
        font-size: 17px;
        color: #1865f2;
        letter-spacing: -0.4px;
    }

    .border-slate-200 {
        border-color: #e2e8f0 !important;
    }

    .footer-grid-saas {
        display: grid;
        grid-template-columns: 1.8fr 1fr 1.2fr 1.2fr;
        gap: 32px;
    }

    .footer-title-saas {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1.2px;
        color: #1e293b;
        margin-bottom: 14px;
        padding-bottom: 6px;
        border-bottom: 2px solid rgba(24, 101, 242, 0.3);
        display: inline-block;
        text-transform: uppercase;
    }

    .footer-menu-saas {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-menu-saas li {
        margin-bottom: 9px;
        font-size: 13px;
        color: #475569;
    }

    .footer-menu-saas a {
        color: #475569;
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-block;
    }

    .footer-menu-saas a:hover {
        color: #1865f2;
        transform: translateX(3px);
    }

    .footer-menu-saas.contact li {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .footer-menu-saas.contact li i {
        width: 16px;
        text-align: center;
    }

    .social-btn-saas {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #ffffff;
        color: #1865f2;
        font-size: 13px;
        text-decoration: none;
        transition: all 0.25s ease;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .social-btn-saas:hover {
        background: #1865f2;
        color: #ffffff;
        border-color: #1865f2;
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(24, 101, 242, 0.25);
    }

    .badge-status-saas {
        display: inline-flex;
        align-items: center;
        background: #ffffff;
        color: #0f172a;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        border: 1px solid #cbd5e1;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    }

    /* ANIMASI PULSING DOT HIJAU SERVER */
    .status-pulse-dot {
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
            box-shadow: 0 0 0 6px rgba(16, 185, 129, 0);
        }
        100% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
        }
    }

    @media (max-width: 991.98px) {
        .footer-grid-saas {
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }
    }

    @media (max-width: 575.98px) {
        .footer-grid-saas {
            grid-template-columns: 1fr;
            gap: 20px;
        }
    }
</style>