<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mortuary System')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Source+Serif+4:opsz,wght@8..60,500;8..60,700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --ink: #14213d;
            --muted: #5c6b7a;
            --paper: #f4f7fb;
            --panel: #ffffff;
            --line: #d7e0ea;
            --accent: #0f6a5a;
            --accent-2: #1d4f91;
            --danger: #9b2226;
            --shadow: 0 12px 30px rgba(20, 33, 61, 0.08);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Manrope', sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at top right, rgba(15, 106, 90, 0.12), transparent 28%),
                linear-gradient(160deg, #eef3f8 0%, #f7fafc 45%, #e8eef5 100%);
        }

        .app-nav {
            position: sticky;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(10px);
            background: rgba(255,255,255,0.92);
            border-bottom: 1px solid var(--line);
        }

        .app-nav .navbar-brand {
            font-family: 'Source Serif 4', serif;
            font-weight: 700;
            color: var(--ink);
            letter-spacing: 0.02em;
        }

        .app-nav .nav-link {
            color: var(--muted);
            font-weight: 600;
            border-radius: 999px;
            padding: 0.45rem 0.9rem !important;
        }

        .app-nav .nav-link:hover,
        .app-nav .nav-link.active {
            color: var(--accent);
            background: rgba(15, 106, 90, 0.08);
        }

        .page-shell {
            width: min(1120px, calc(100% - 2rem));
            margin: 1.5rem auto 3rem;
        }

        .page-hero {
            margin-bottom: 1.25rem;
        }

        .page-hero h1,
        .page-title {
            font-family: 'Source Serif 4', serif;
            font-weight: 700;
            margin: 0 0 0.35rem;
            color: var(--ink);
        }

        .page-hero p {
            margin: 0;
            color: var(--muted);
        }

        .surface-card {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 18px;
            box-shadow: var(--shadow);
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
        }

        .stat-card {
            padding: 1.25rem 1.35rem;
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 18px;
            box-shadow: var(--shadow);
        }

        .stat-card .label {
            color: var(--muted);
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .stat-card .value {
            font-family: 'Source Serif 4', serif;
            font-size: 2rem;
            font-weight: 700;
            margin-top: 0.35rem;
        }

        .btn-accent {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
            font-weight: 600;
        }

        .btn-accent:hover {
            background: #0c574a;
            border-color: #0c574a;
            color: #fff;
        }

        .btn-soft {
            background: rgba(29, 79, 145, 0.08);
            border: 1px solid rgba(29, 79, 145, 0.18);
            color: var(--accent-2);
            font-weight: 600;
        }

        .table thead th {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--muted);
            border-bottom-color: var(--line);
        }

        .photo-preview {
            width: 140px;
            height: 140px;
            object-fit: cover;
            border-radius: 14px;
            border: 1px solid var(--line);
            display: none;
        }

        .lang-toggle .btn {
            min-width: 72px;
            font-weight: 700;
        }

        @media (max-width: 768px) {
            .stat-grid { grid-template-columns: 1fr; }
            .page-shell { width: calc(100% - 1rem); margin-top: 1rem; }
        }

        @media print {
            .app-nav, .no-print, footer { display: none !important; }
            body { background: #fff; }
            .page-shell { width: 100%; margin: 0; }
            .surface-card { box-shadow: none; border: none; }
        }
    </style>
    @stack('styles')
</head>
<body>
<nav class="navbar navbar-expand-lg app-nav">
    <div class="container-fluid px-3 px-lg-4">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            Mortuary System
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            @auth
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-lg-1">
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('deceased.*') ? 'active' : '' }}" href="{{ route('deceased.index') }}">Deceased</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('storage.*') ? 'active' : '' }}" href="{{ route('storage.index') }}">Storage</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('payments.*') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">Payments</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('payments.index') }}">My payments</a></li>
                            <li><a class="dropdown-item" href="{{ route('payments.create') }}">Make payment</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('schedule.*') ? 'active' : '' }}" href="{{ route('schedule.index') }}">Schedule</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('ai.*') || request()->routeIs('faire-part.*') ? 'active' : '' }}" href="{{ route('ai.index') }}">AI</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('geolocation.*') ? 'active' : '' }}" href="{{ route('geolocation.index') }}">Geo</a></li>
                    @if(auth()->user()->role === 'admin')
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Admin</a></li>
                    @endif
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted small d-none d-md-inline">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-sm btn-outline-danger" type="submit">Logout</button>
                    </form>
                </div>
            @endauth
        </div>
    </div>
</nav>

<main class="page-shell">
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm">{{ session('error') }}</div>
    @endif

    @yield('content')
</main>

<footer class="text-center text-muted pb-4 small no-print">
    &copy; {{ date('Y') }} Mortuary System
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
