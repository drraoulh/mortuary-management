<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mortuary System – @yield('title', 'Dashboard')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --pink-50:  #fdf2f8;
            --pink-100: #fce7f3;
            --pink-200: #fbcfe8;
            --pink-300: #f9a8d4;
            --pink-400: #f472b6;
            --pink-500: #ec4899;
            --pink-600: #db2777;
            --pink-700: #be185d;
            --pink-800: #9d174d;
            --pink-900: #831843;

            --text-main:  #1f2937;
            --text-muted: #6b7280;
            --card-border: #f5d0e6;

            --bs-primary: var(--pink-600);
            --bs-primary-rgb: 219, 39, 119;
            --bs-link-color: var(--pink-600);
            --bs-link-color-rgb: 219, 39, 119;
            --bs-link-hover-color: var(--pink-800);
            --bs-link-hover-color-rgb: 157, 23, 77;
            --bs-body-font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(180deg, var(--pink-50) 0%, #fff 60%);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        main { flex: 1; }

        /* ── Bootstrap colour overrides so every page picks up the pink theme ── */
        .btn-primary {
            --bs-btn-bg: var(--pink-600);
            --bs-btn-border-color: var(--pink-600);
            --bs-btn-hover-bg: var(--pink-700);
            --bs-btn-hover-border-color: var(--pink-700);
            --bs-btn-active-bg: var(--pink-800);
            --bs-btn-active-border-color: var(--pink-800);
            --bs-btn-disabled-bg: var(--pink-400);
            --bs-btn-disabled-border-color: var(--pink-400);
        }
        .btn-outline-primary {
            --bs-btn-color: var(--pink-600);
            --bs-btn-border-color: var(--pink-600);
            --bs-btn-hover-bg: var(--pink-600);
            --bs-btn-hover-border-color: var(--pink-600);
            --bs-btn-active-bg: var(--pink-700);
            --bs-btn-active-border-color: var(--pink-700);
        }
        .btn-pink-soft {
            background: var(--pink-100);
            color: var(--pink-700);
            border: 1px solid var(--pink-200);
        }
        .btn-pink-soft:hover { background: var(--pink-200); color: var(--pink-800); }

        .bg-primary { background-color: var(--pink-600) !important; }
        .text-primary { color: var(--pink-600) !important; }
        .text-pink { color: var(--pink-600) !important; }
        .bg-pink-soft { background: var(--pink-100) !important; color: var(--pink-800) !important; }

        .form-control:focus, .form-select:focus {
            border-color: var(--pink-400);
            box-shadow: 0 0 0 .25rem rgba(236, 72, 153, .2);
        }
        .form-check-input:checked { background-color: var(--pink-600); border-color: var(--pink-600); }

        .card { border-radius: 16px; border-color: var(--card-border); }
        .table > :not(caption) > * > * { padding: .7rem .75rem; }
        .table thead th {
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: var(--pink-800);
            background: var(--pink-50);
            border-bottom-color: var(--pink-200);
        }

        /* ── Top navigation ── */
        .app-nav {
            background: linear-gradient(90deg, var(--pink-800) 0%, var(--pink-600) 55%, var(--pink-500) 100%);
            box-shadow: 0 4px 18px rgba(190, 24, 93, .25);
        }
        .app-nav .navbar-brand {
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: .55rem;
        }
        .app-nav .brand-icon {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: rgba(255, 255, 255, .2);
            display: inline-flex; align-items: center; justify-content: center;
        }
        .app-nav .nav-link {
            color: rgba(255, 255, 255, .85);
            font-weight: 500;
            font-size: .9rem;
            border-radius: 8px;
            padding: .45rem .8rem !important;
            display: flex; align-items: center; gap: .4rem;
        }
        .app-nav .nav-link:hover,
        .app-nav .nav-link:focus { color: #fff; background: rgba(255, 255, 255, .12); }
        .app-nav .nav-link.active { color: #fff; background: rgba(255, 255, 255, .22); }
        .app-nav .dropdown-menu {
            border: 1px solid var(--pink-200);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(131, 24, 67, .15);
            padding: .4rem;
        }
        .app-nav .dropdown-item { border-radius: 8px; font-size: .9rem; padding: .5rem .75rem; }
        .app-nav .dropdown-item:hover { background: var(--pink-50); color: var(--pink-700); }
        .app-nav .dropdown-item.active,
        .app-nav .dropdown-item:active { background: var(--pink-600); color: #fff; }

        .user-avatar {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: #fff;
            color: var(--pink-700);
            font-weight: 700;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: .85rem;
        }
        .role-chip {
            font-size: .68rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .04em;
            padding: .15rem .5rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, .22);
            color: #fff;
        }

        /* ── Dashboard building blocks ── */
        .page-header {
            padding: 2rem 0 1.25rem;
        }
        .page-header h1 { font-size: 1.75rem; font-weight: 700; margin-bottom: .25rem; }
        .page-header p { color: var(--text-muted); margin: 0; }
        .page-header .eyebrow {
            display: inline-block;
            font-size: .75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--pink-600);
            background: var(--pink-100);
            border-radius: 999px;
            padding: .2rem .7rem;
            margin-bottom: .6rem;
        }

        .stat-card {
            border-radius: 18px;
            padding: 1.25rem 1.35rem;
            color: #fff;
            height: 100%;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(190, 24, 93, .18);
        }
        .stat-card::after {
            content: '';
            position: absolute;
            right: -30px; top: -30px;
            width: 120px; height: 120px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .12);
        }
        .stat-card .stat-icon {
            width: 44px; height: 44px;
            border-radius: 12px;
            background: rgba(255, 255, 255, .22);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.25rem;
            margin-bottom: .9rem;
        }
        .stat-card .stat-label { font-size: .82rem; opacity: .9; }
        .stat-card .stat-value { font-size: 1.9rem; font-weight: 700; line-height: 1.15; }
        .stat-card .stat-sub { font-size: .78rem; opacity: .85; margin-top: .35rem; }

        .stat-pink    { background: linear-gradient(135deg, #be185d 0%, #ec4899 100%); }
        .stat-rose    { background: linear-gradient(135deg, #e11d48 0%, #fb7185 100%); }
        .stat-fuchsia { background: linear-gradient(135deg, #a21caf 0%, #e879f9 100%); }
        .stat-berry   { background: linear-gradient(135deg, #831843 0%, #db2777 100%); }
        .stat-violet  { background: linear-gradient(135deg, #7c3aed 0%, #c084fc 100%); }
        .stat-blush   { background: linear-gradient(135deg, #f472b6 0%, #f9a8d4 100%); color: #500724; }
        .stat-blush .stat-icon { background: rgba(255, 255, 255, .45); }

        .panel {
            background: #fff;
            border: 1px solid var(--card-border);
            border-radius: 18px;
            box-shadow: 0 4px 16px rgba(190, 24, 93, .06);
            height: 100%;
        }
        .panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--pink-100);
        }
        .panel-title {
            font-size: .95rem;
            font-weight: 600;
            margin: 0;
            display: flex; align-items: center; gap: .5rem;
        }
        .panel-title i { color: var(--pink-600); }
        .panel-body { padding: 1rem 1.25rem; }
        .panel-link { font-size: .82rem; font-weight: 500; text-decoration: none; }

        .quick-action {
            display: flex;
            align-items: center;
            gap: .8rem;
            padding: .85rem 1rem;
            border-radius: 14px;
            border: 1px solid var(--pink-100);
            background: var(--pink-50);
            color: var(--text-main);
            text-decoration: none;
            transition: transform .15s, background .15s, border-color .15s;
            height: 100%;
        }
        .quick-action:hover {
            background: var(--pink-100);
            border-color: var(--pink-300);
            color: var(--pink-800);
            transform: translateY(-2px);
        }
        .quick-action .qa-icon {
            width: 40px; height: 40px;
            flex-shrink: 0;
            border-radius: 12px;
            background: var(--pink-600);
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem;
        }
        .quick-action .qa-title { font-weight: 600; font-size: .9rem; }
        .quick-action .qa-sub { font-size: .76rem; color: var(--text-muted); }

        .list-row {
            display: flex;
            align-items: center;
            gap: .8rem;
            padding: .7rem 0;
            border-bottom: 1px solid var(--pink-50);
        }
        .list-row:last-child { border-bottom: none; }
        .list-row .lr-icon {
            width: 36px; height: 36px;
            flex-shrink: 0;
            border-radius: 10px;
            background: var(--pink-100);
            color: var(--pink-700);
            display: flex; align-items: center; justify-content: center;
        }
        .list-row .lr-main { flex: 1; min-width: 0; }
        .list-row .lr-title { font-weight: 600; font-size: .9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .list-row .lr-sub { font-size: .78rem; color: var(--text-muted); }

        .empty-state {
            text-align: center;
            padding: 1.75rem 1rem;
            color: var(--text-muted);
            font-size: .88rem;
        }
        .empty-state i { font-size: 1.6rem; color: var(--pink-300); display: block; margin-bottom: .4rem; }

        .status-badge {
            font-size: .72rem;
            font-weight: 600;
            padding: .25rem .6rem;
            border-radius: 999px;
            text-transform: capitalize;
        }
        .status-default    { background: var(--pink-100); color: var(--pink-800); }
        .status-pending    { background: #fef3c7; color: #92400e; }
        .status-confirmed,
        .status-successful,
        .status-available  { background: #dcfce7; color: #166534; }
        .status-failed,
        .status-occupied,
        .status-full       { background: #fee2e2; color: #991b1b; }

        .progress { background: var(--pink-100); border-radius: 999px; }
        .progress-bar { background: linear-gradient(90deg, var(--pink-600), var(--pink-400)); }

        .bar-chart {
            display: flex;
            align-items: flex-end;
            gap: .75rem;
            height: 180px;
            padding-top: 1rem;
        }
        .bar-chart .bar-col { flex: 1; display: flex; flex-direction: column; align-items: center; gap: .4rem; height: 100%; justify-content: flex-end; }
        .bar-chart .bar {
            width: 100%;
            max-width: 46px;
            border-radius: 10px 10px 4px 4px;
            background: linear-gradient(180deg, var(--pink-400), var(--pink-700));
            min-height: 4px;
        }
        .bar-chart .bar-label { font-size: .72rem; color: var(--text-muted); }
        .bar-chart .bar-value { font-size: .7rem; font-weight: 600; color: var(--pink-800); }

        .site-footer {
            text-align: center;
            padding: 1.25rem;
            color: var(--text-muted);
            font-size: .8rem;
            border-top: 1px solid var(--pink-100);
            margin-top: 2.5rem;
            background: #fff;
        }
    </style>

    @stack('styles')
</head>
<body>

@php($currentUser = auth()->user())

<nav class="navbar navbar-expand-lg navbar-dark app-nav sticky-top">
    <div class="container-fluid px-3 px-lg-4">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <span class="brand-icon"><i class="bi bi-flower1"></i></span>
            Mortuary System
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            @if($currentUser)
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-lg-1">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard', 'family.dashboard', 'staff.dashboard', 'manager.dashboard', 'admin.dashboard') ? 'active' : '' }}"
                           href="{{ route('dashboard') }}">
                            <i class="bi bi-grid-1x2"></i> {{ $currentUser->isClient() ? 'My Space' : 'Dashboard' }}
                        </a>
                    </li>

                    @unless($currentUser->isClient())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('deceased.index', 'deceased.create', 'deceased.show', 'deceased.edit') ? 'active' : '' }}"
                               href="{{ route('deceased.index') }}">
                                <i class="bi bi-person-vcard"></i> Deceased
                            </a>
                        </li>
                    @endunless

                    @unless($currentUser->isClient())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('storage.*') ? 'active' : '' }}"
                               href="{{ route('storage.index') }}">
                                <i class="bi bi-door-closed"></i> Storage Rooms
                            </a>
                        </li>
                    @endunless

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('payments.*') ? 'active' : '' }}"
                           href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-wallet2"></i> Payments
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('payments.index') }}">
                                    <i class="bi bi-list-ul me-2"></i> My Payments
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('payments.create') }}">
                                    <i class="bi bi-plus-circle me-2"></i> Make Payment
                                </a>
                            </li>
                        </ul>
                    </li>

                    @unless($currentUser->isClient())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('schedule.*') ? 'active' : '' }}"
                               href="{{ route('schedule.index') }}">
                                <i class="bi bi-calendar-event"></i> Schedule
                            </a>
                        </li>
                    @endunless

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('deceased.verify*', 'faire-part.*', 'geolocation.*') ? 'active' : '' }}"
                           href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-stars"></i> Services
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('deceased.verify-form') }}">
                                    <i class="bi bi-shield-check me-2"></i> Verify Deceased
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('faire-part.create') }}">
                                    <i class="bi bi-envelope-paper-heart me-2"></i> Faire-part
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('geolocation.index') }}">
                                    <i class="bi bi-geo-alt me-2"></i> Find a Mortuary
                                </a>
                            </li>
                        </ul>
                    </li>

                    @if($currentUser->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                               href="{{ route('admin.users.index') }}">
                                <i class="bi bi-people"></i> Users
                            </a>
                        </li>
                    @endif
                </ul>

                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center gap-2 text-white text-decoration-none dropdown-toggle"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="user-avatar">{{ strtoupper(mb_substr($currentUser->name, 0, 1)) }}</span>
                        <span class="d-flex flex-column lh-sm">
                            <span style="font-size:.88rem;font-weight:600;">{{ $currentUser->name }}</span>
                            <span class="role-chip mt-1 align-self-start">{{ $currentUser->roleLabel() }}</span>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person-circle me-2"></i> My Profile
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
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
            @endif
        </div>
    </div>
</nav>

<main>
    @isset($header)
        <div class="container py-4">{{ $header }}</div>
    @endisset

    @yield('content')

    {{-- Pages written as <x-app-layout> pass their content as a slot instead of a section. --}}
    @isset($slot)
        {{ $slot }}
    @endisset
</main>

<footer class="site-footer">
    &copy; {{ date('Y') }} Mortuary System. All rights reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
