<!DOCTYPE html>
<html lang="en">
<head>
 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mortuary System – @yield('title', 'Dashboard')</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        /* ── Root tokens ───────────────────────────────────────────── */
        :root {
            --bg-dark:       #0d1117;
            --bg-card:       rgba(255,255,255,0.07);
            --bg-card-hover: rgba(255,255,255,0.11);
            --border:        rgba(255,255,255,0.10);
            --text-primary:  #e6edf3;
            --text-muted:    #8b949e;
            --accent-blue:   #2563eb;
            --accent-green:  #16a34a;
            --accent-gold:   #b45309;
            --accent-red:    #b91c1c;
            --nav-height:    64px;
        }

        /* ── Base ──────────────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-primary);
            min-height: 100vh;
        }

        /* ── Top-nav ───────────────────────────────────────────────── */
        .top-nav {
            position: sticky;
            top: 0;
            z-index: 1000;
            height: var(--nav-height);
            background: rgba(13,17,23,0.90);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 2rem;
            gap: 2rem;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            font-weight: 700;
            font-size: 1.05rem;
            color: var(--text-primary);
            text-decoration: none;
            white-space: nowrap;
        }
        .nav-brand i { font-size: 1.25rem; opacity: .85; }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            list-style: none;
            flex: 1;
        }
        .nav-links a {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.45rem 0.9rem;
            border-radius: 6px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: color .15s, background .15s;
        }
        .nav-links a:hover,
        .nav-links a.active {
            color: var(--text-primary);
            background: rgba(255,255,255,0.08);
        }
        .nav-links a.active {
            border-bottom: 2px solid var(--accent-blue);
            border-radius: 6px 6px 0 0;
        }

        .nav-user {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            cursor: pointer;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            transition: background .15s;
        }
        .nav-user:hover { background: rgba(255,255,255,0.06); }
        .nav-avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: rgba(255,255,255,0.15);
            display: flex; align-items: center; justify-content: center;
            font-size: .85rem;
        }

        /* ── Page hero / header ────────────────────────────────────── */
        .page-hero {
            position: relative;
            background:
                linear-gradient(to bottom, rgba(13,17,23,0.55) 0%, rgba(13,17,23,0.92) 100%),
                url('{{ asset("images/hero-bg.jpg") }}') center/cover no-repeat;
            padding: 3rem 2rem 2.5rem;
            min-height: 180px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }
        .page-hero h1 {
            font-size: 2.4rem;
            font-weight: 700;
            line-height: 1.15;
            margin-bottom: .35rem;
        }
        .page-hero p { color: var(--text-muted); font-size: .9rem; }

        /* ── Main container ────────────────────────────────────────── */
        .main-container { padding: 2rem; }

        /* ── Glass card ────────────────────────────────────────────── */
        .glass-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            backdrop-filter: blur(8px);
            transition: background .2s;
        }
        .glass-card:hover { background: var(--bg-card-hover); }

        /* ── Stat cards ────────────────────────────────────────────── */
        .stat-card {
            border-radius: 12px;
            padding: 1.4rem 1.5rem;
            color: #fff;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 150px;
        }
        .stat-card .stat-icon {
            width: 52px; height: 52px;
            border-radius: 50%;
            background: rgba(255,255,255,0.20);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
            margin-bottom: .75rem;
        }
        .stat-card .stat-label { font-size: .82rem; opacity: .85; margin-bottom: .2rem; }
        .stat-card .stat-value { font-size: 2.1rem; font-weight: 700; line-height: 1; }
        .stat-card .stat-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 1rem;
            font-size: .8rem;
            opacity: .75;
            border-top: 1px solid rgba(255,255,255,.2);
            padding-top: .75rem;
        }
        .stat-card .stat-footer a { color: inherit; text-decoration: none; }

        .stat-blue   { background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); }
        .stat-green  { background: linear-gradient(135deg, #14532d 0%, #16a34a 100%); }
        .stat-gold   { background: linear-gradient(135deg, #78350f 0%, #d97706 100%); }
        .stat-red    { background: linear-gradient(135deg, #7f1d1d 0%, #dc2626 100%); }

        /* ── Section cards ─────────────────────────────────────────── */
        .section-card { padding: 1.4rem 1.5rem; }
        .section-title {
            font-size: .95rem;
            font-weight: 600;
            margin-bottom: 1.2rem;
            display: flex;
            align-items: center;
            gap: .5rem;
        }
        .section-title i { font-size: 1rem; }

        /* ── System overview list ───────────────────────────────────── */
        .overview-item {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .6rem 0;
            border-bottom: 1px solid var(--border);
        }
        .overview-item:last-child { border-bottom: none; }
        .overview-item .oi-icon {
            width: 32px; height: 32px;
            border-radius: 8px;
            background: rgba(255,255,255,0.08);
            display: flex; align-items: center; justify-content: center;
            font-size: .9rem;
            flex-shrink: 0;
        }
        .overview-item .oi-label { flex: 1; font-size: .875rem; }
        .overview-item .oi-value { font-weight: 700; font-size: .95rem; }

        /* ── Activity feed ──────────────────────────────────────────── */
        .activity-item {
            display: flex;
            align-items: center;
            gap: .8rem;
            padding: .6rem 0;
            border-bottom: 1px solid var(--border);
        }
        .activity-item:last-child { border-bottom: none; }
        .activity-dot {
            width: 10px; height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .dot-green  { background: #22c55e; }
        .dot-blue   { background: #3b82f6; }
        .dot-yellow { background: #eab308; }
        .dot-red    { background: #ef4444; }
        .dot-purple { background: #a855f7; }

        .activity-item .ai-text { flex: 1; font-size: .875rem; }
        .activity-item .ai-time { font-size: .78rem; color: var(--text-muted); white-space: nowrap; }

        .view-all-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 1rem;
            padding-top: .85rem;
            border-top: 1px solid var(--border);
            color: var(--text-muted);
            text-decoration: none;
            font-size: .85rem;
            transition: color .15s;
        }
        .view-all-link:hover { color: var(--text-primary); }

        /* ── Quick actions ──────────────────────────────────────────── */
        .quick-action-item {
            display: flex;
            align-items: center;
            gap: .8rem;
            padding: .7rem .9rem;
            border-radius: 8px;
            text-decoration: none;
            color: var(--text-primary);
            transition: background .15s;
            border-bottom: 1px solid var(--border);
        }
        .quick-action-item:last-child { border-bottom: none; }
        .quick-action-item:hover { background: rgba(255,255,255,0.06); color: var(--text-primary); }
        .qa-icon {
            width: 34px; height: 34px;
            border-radius: 8px;
            background: rgba(255,255,255,0.08);
            display: flex; align-items: center; justify-content: center;
            font-size: .95rem;
            flex-shrink: 0;
        }
        .qa-label { flex: 1; font-size: .875rem; font-weight: 500; }

        /* ── Footer ─────────────────────────────────────────────────── */
        .site-footer {
            text-align: center;
            padding: 1.5rem;
            color: var(--text-muted);
            font-size: .78rem;
            border-top: 1px solid var(--border);
            margin-top: 2rem;
        }

        /* ── Responsive ─────────────────────────────────────────────── */
        @media (max-width: 768px) {
            .nav-links { display: none; }
            .page-hero h1 { font-size: 1.6rem; }
            .main-container { padding: 1rem; }
        }
        
body{
    background: linear-gradient(
        135deg,
        #0f172a,
        #1e293b,
        #334155
    );
    min-height:100vh;
}

.card{
    border-radius:15px;
}

    </style>

    @stack('styles')
</head>
<body>

{{-- ─── TOP NAV ─────────────────────────────────────────────────────── --}}
<nav class="top-nav">
    <a href="{{ route('admin.dashboard') }}" class="nav-brand">
        <i class="bi bi-bank"></i>
        Mortuary System
    </a>

    <ul class="nav-links">
        <li>
    <a href="{{ route('admin.dashboard') }}">
        Dashboard
    </a>
</li>

<li>
    <a href="{{ route('deceased.index') }}">
        Deceased
    </a>
</li>

@if(auth()->user()->role == 'admin')

<li>
<a href="{{ route('storage.index') }}">
Storage Rooms
</a>
</li>

@endif

<div class="col-md-4 mb-3">
    <div class="card shadow text-center">
        <div class="card-body">
            <h5>Add Payment</h5>

           <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle"
       href="#"
       id="paymentsDropdown"
       role="button"
       data-bs-toggle="dropdown"
       aria-expanded="false">
        Payments
    </a>

    <ul class="dropdown-menu" aria-labelledby="paymentsDropdown">

        <li>
            <a class="dropdown-item"
               href="{{ route('payments.index') }}">
                My Payments
            </a>
        </li>

        <li>
            <a class="dropdown-item"
               href="{{ route('payments.create') }}">
                Make Payment
            </a>
        </li>

    </ul>
</li>
        </div>
    </div>
</div>

<div class="col-md-4 mb-3">
    <div class="card shadow text-center">
        <div class="card-body">
            <h5>Add Schedule</h5>

            <a href="{{ route('schedule.create') }}"
               class="btn btn-primary">
                Add Schedule
            </a>
        </div>
    </div>
</div>

    </ul>

    <div class="nav-user dropdown">
        <div class="nav-user" data-bs-toggle="dropdown" aria-expanded="false">
            <div class="nav-avatar"><i class="bi bi-person-fill"></i></div>
            <span style="font-size:.875rem;font-weight:500;">
                {{ auth()->user()->name ?? 'Admin' }}
            </span>
            <i class="bi bi-chevron-down" style="font-size:.7rem;opacity:.6;"></i>
        </div>
        <ul class="dropdown-menu dropdown-menu-end"
            style="background:#1c2333;border:1px solid var(--border);min-width:160px;">
            
            <li><hr class="dropdown-divider border-secondary"></li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>
</nav>

{{-- ─── PAGE CONTENT ────────────────────────────────────────────────── --}}
@yield('content')

{{-- ─── FOOTER ──────────────────────────────────────────────────────── --}}
<footer class="site-footer">
    &copy; {{ date('Y') }} Mortuary System. All rights reserved.
</footer>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>