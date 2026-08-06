<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
        <meta name="vapid-public-key" content="{{ config('webpush.VAPID.publicKey') }}">
    @endauth
    <link rel="icon" type="image/png" href="{{ asset('assets/image/dinsos_logo.png') }}">
    <title>@yield('title', 'SIPEKA')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/bootstrap-icons/bootstrap-icons.css') }}">

    <style>
        :root {
            --sp-navy: #1e3c72;
            --sp-navy-2: #202f5b;
            --sp-blue: #2a5298;
            --sp-accent: #3498db;
        }
        body {
            background-color: #f4f6fb;
            font-family: 'Poppins', 'Segoe UI', Tahoma, sans-serif;
        }

        /* ===== Sidebar ===== */
        .sidebar {
            width: 250px; height: 100vh; position: fixed; top: 0; left: 0; z-index: 1045;
            background: linear-gradient(180deg, var(--sp-navy-2) 0%, #16213e 100%);
            color: #fff; padding-top: 22px; overflow-y: auto;
            transition: transform .25s ease;
        }
        .sidebar-brand img { border-radius: 8px; background: #fff; padding: 2px; }
        .sidebar-nav a {
            color: #c7cbe0; text-decoration: none; display: block;
            padding: 10px 18px; border-radius: 10px; margin: 3px 10px; font-size: 14.5px;
            transition: background .15s ease, color .15s ease;
        }
        .sidebar-nav a:hover { background: rgba(255,255,255,.08); color: #fff; }
        .sidebar-nav a.active { background: var(--sp-accent); color: #fff; font-weight: 600; }
        .sidebar-nav .submenu a { padding-left: 34px; font-size: 13.5px; }
        .sidebar-nav i { width: 20px; display: inline-block; text-align: center; }

        .main-content { margin-left: 250px; min-height: 100vh; transition: margin-left .25s ease; }

        /* ===== Navbar ===== */
        .app-navbar {
            background: linear-gradient(135deg, var(--sp-navy) 0%, var(--sp-blue) 100%);
            padding: 14px 22px; box-shadow: 0 2px 10px rgba(0,0,0,.12);
        }
        .btn-icon-nav {
            background: rgba(255,255,255,.12); border: none; color: #fff;
            border-radius: 10px; padding: 6px 12px;
        }
        .btn-icon-nav:hover { background: rgba(255,255,255,.22); color: #fff; }

        /* ===== Cards ===== */
        .card { border: none; border-radius: 14px; box-shadow: 0 4px 14px rgba(20,30,60,.07); }
        .card-header {
            border-radius: 14px 14px 0 0 !important; font-weight: 600;
            background: linear-gradient(135deg, var(--sp-navy) 0%, var(--sp-blue) 100%);
            color: #fff; border: none;
        }
        .card-header.bg-success { background: linear-gradient(135deg, #1c8a4a, #27ae60) !important; }
        .btn-primary {
            background: linear-gradient(135deg, var(--sp-navy) 0%, var(--sp-blue) 100%);
            border: none; border-radius: 10px;
        }
        .btn-primary:hover { filter: brightness(1.08); }
        .btn { border-radius: 10px; }
        .badge { font-weight: 500; }

        .stat-icon {
            width: 46px; height: 46px; border-radius: 12px; display: flex;
            align-items: center; justify-content: center; font-size: 20px;
            background: rgba(52,152,219,.12); color: var(--sp-accent);
        }

        /* ===== Notif toast ===== */
        .notif-wrapper { position: fixed; top: 18px; left: 50%; transform: translateX(-50%); z-index: 1080; }
        .notif-wrapper .alert { min-width: 320px; text-align: center; border-radius: 10px; border: none; box-shadow: 0 6px 18px rgba(0,0,0,.15); }

        /* ===== Sidebar overlay (mobile) ===== */
        .sidebar-backdrop {
            display: none; position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 1040;
        }
        .sidebar-backdrop.show { display: block; }

        /* Breakpoint disamakan dengan Bootstrap `lg` (992px) supaya konsisten dengan
           tombol burger yang pakai class d-lg-none di navbar. */
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); box-shadow: 6px 0 20px rgba(0,0,0,.25); }
            .main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

@if (session('notif'))
    <div class="notif-wrapper">
        <div class="alert alert-{{ session('notif.type') }} alert-dismissible fade show auto-close" role="alert">
            {{ session('notif.message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
@endif

@auth
    @include('partials.sidebar')
    <div class="main-content">
        @include('partials.navbar')
        <div class="page-body p-3 p-md-4">
            @yield('content')
        </div>
    </div>
@else
    @yield('content')
@endauth

<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
<script>const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;</script>

@auth
<script src="{{ asset('js/push.js') }}" defer></script>
<script>
    function sipekaUpdateClock() {
        const el = document.getElementById('currentDateTime');
        if (!el) return;
        const now = new Date();
        const date = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        const time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        el.innerHTML = `<i class="bi bi-clock"></i> ${date} | ${time}`;
    }
    sipekaUpdateClock();
    setInterval(sipekaUpdateClock, 60 * 1000);

    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(function () {
            document.querySelectorAll('.auto-close').forEach(function (el) {
                bootstrap.Alert.getOrCreateInstance(el).close();
            });
        }, 3000);

        // ===== Toggle sidebar (mobile / tablet) =====
        const sidebar = document.querySelector('.sidebar');
        const toggleBtn = document.getElementById('sidebarToggle');

        if (sidebar && toggleBtn) {
            const backdrop = document.createElement('div');
            backdrop.className = 'sidebar-backdrop';
            document.body.appendChild(backdrop);

            const closeSidebar = () => {
                sidebar.classList.remove('show');
                backdrop.classList.remove('show');
                toggleBtn.setAttribute('aria-expanded', 'false');
            };
            const openSidebar = () => {
                sidebar.classList.add('show');
                backdrop.classList.add('show');
                toggleBtn.setAttribute('aria-expanded', 'true');
            };

            toggleBtn.addEventListener('click', function () {
                sidebar.classList.contains('show') ? closeSidebar() : openSidebar();
            });
            backdrop.addEventListener('click', closeSidebar);

            // Tutup otomatis begitu memilih menu (bukan toggle submenu), supaya sidebar
            // tidak menutupi halaman tujuan di layar kecil.
            sidebar.querySelectorAll('a:not([data-bs-toggle])').forEach(function (link) {
                link.addEventListener('click', function () {
                    if (window.innerWidth < 992) closeSidebar();
                });
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth >= 992) closeSidebar();
            });
        }

        // ===== Buka kembali modal yang formnya baru saja gagal validasi =====
        // Setiap form di dalam modal (mis. Tambah/Edit Undangan, Tambah/Edit Kegiatan)
        // menyertakan <input type="hidden" name="_modal_target" value="idModalnya">.
        // Tanpa ini, kalau validasi gagal, halaman reload dan modal tertutup begitu
        // saja tanpa pesan error apa pun -- makanya sebelumnya terkesan "tidak bisa input".
        @if ($errors->any())
            (function () {
                const modalTarget = @json(old('_modal_target'));
                if (!modalTarget) return;
                const modalEl = document.getElementById(modalTarget);
                if (modalEl) {
                    bootstrap.Modal.getOrCreateInstance(modalEl).show();
                }
            })();
        @endif
    });
</script>
@endauth
@stack('scripts')
</body>
</html>
