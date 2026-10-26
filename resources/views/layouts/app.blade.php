<!DOCTYPE html>
<html lang="id">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <title>@yield('title', 'ONE FINANCE - Sistem Informasi Keuangan')</title>

    <link rel="icon" href="{{ asset('assets/img/kaiadmin/favicon.ico') }}" type="image/x-icon" />

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <script src="{{ asset('assets/js/plugin/webfont/webfont.min.js') }}"></script>
    <script>
        WebFont.load({
            custom: {
                families: [
                    "Font Awesome 5 Solid",
                    "Font Awesome 5 Regular",
                    "Font Awesome 5 Brands",
                    "simple-line-icons"
                ],
                urls: ["{{ asset('assets/css/fonts.min.css') }}"]
            },
            active: function() {
                sessionStorage.fonts = true;
            }
        });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/kaiadmin.min.css') }}">

    <style>
        :root {
            --bg-outer-blue: #1865f2;
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 80px;
            --header-height: 65px;
        }

        /* 1. KUNCI SELURUH WINDOW AGAR TIDAK TER-SCROLL SAMA SEKALI */
        html, body {
            height: 100vh !important;
            max-height: 100vh !important;
            overflow: hidden !important;
            margin: 0;
            padding: 12px;
            background-color: var(--bg-outer-blue) !important;
            font-family: 'Plus Jakarta Sans', 'Public Sans', sans-serif !important;
        }

        /* 2. WRAPPER UTAMA SAMA DENGAN TINGGI SCREEN */
        .app-window-wrapper {
            background-color: #ffffff;
            border-radius: 24px;
            height: calc(100vh - 24px) !important;
            max-height: calc(100vh - 24px) !important;
            overflow: hidden !important;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.25);
            display: flex;
            flex-direction: column;
        }

        /* 3. CONTAINER BODY LAYOUT */
        .app-body-container {
            display: flex;
            flex-grow: 1;
            height: calc(100% - var(--header-height)) !important;
            position: relative;
            background-color: #f8fafc;
            overflow: hidden !important;
        }

        /* 4. MAIN PANEL DIPISAHKAN AGAR DI-SCROLL MANDIRI DI KANAN */
        .main-panel-saas {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            width: calc(100% - var(--sidebar-width));
            height: 100% !important;
            overflow-y: auto !important; /* HANYA KONTEN KANAN YANG DI-SCROLL */
            background-color: #ffffff;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body.sidebar-collapsed .main-panel-saas {
            width: calc(100% - var(--sidebar-collapsed-width));
        }

        .page-inner-saas {
            padding: 24px;
            flex-grow: 1;
        }

        /* RESPONSIVE MOBILE */
        @media (max-width: 991.98px) {
            html, body {
                padding: 0 !important;
            }

            .app-window-wrapper {
                border-radius: 0 !important;
                height: 100vh !important;
            }

            .main-panel-saas {
                width: 100% !important;
            }

            .page-inner-saas {
                padding: 14px 10px !important;
            }
        }
    </style>
</head>

<body>

    <div class="app-window-wrapper">

        {{-- 1. HEADER --}}
        @include('layouts.header')

        {{-- 2. BODY CONTAINER --}}
        <div class="app-body-container">

            {{-- SIDEBAR FIXED DENGAN CONTAINER TERKUNCI --}}
            @include('layouts.sidebar')

            {{-- MAIN PANEL CONTENT KHUSUS SCROLLBAR KANAN --}}
            <div class="main-panel-saas">
                <div class="container-fluid p-0">
                    <div class="page-inner-saas">

                        @yield('page-header')

                        @yield('content')

                    </div>
                </div>

                @include('layouts.footer')
            </div>

        </div>

    </div>

    <!-- Core JS Files -->
    <script src="{{ asset('assets/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>

    <!-- jQuery Scrollbar -->
    <script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>

    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Chart JS & Datatables -->
    <script src="{{ asset('assets/js/plugin/chart.js/chart.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/sweetalert/sweetalert.min.js') }}"></script>

    <!-- KaiAdmin JS -->
    <script src="{{ asset('assets/js/kaiadmin.min.js') }}"></script>

    @stack('scripts')

</body>

</html>