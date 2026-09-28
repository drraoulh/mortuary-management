@extends('layouts.app')

@section('title', 'Staff Dashboard')

@section('content')
<div class="page-hero d-flex flex-wrap justify-content-between align-items-end gap-3">
    <div>
        <span class="eyebrow"><i class="bi bi-person-badge me-1"></i> Mortuary Staff</span>
        <h1>Welcome back, {{ auth()->user()->name }}</h1>
        <p>{{ now()->format('l, d F Y') }} &middot; here is your work at a glance.</p>
    </div>
    <a href="{{ route('deceased.create') }}" class="btn btn-accent rounded-pill px-4">
        <i class="bi bi-plus-lg me-1"></i> Register a body
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-pink">
            <div class="stat-icon"><i class="bi bi-person-vcard"></i></div>
            <div class="stat-label">Bodies I registered</div>
            <div class="stat-value">{{ $myDeceasedCount }}</div>
            <div class="stat-sub">{{ $myDeceasedThisMonth }} this month</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-fuchsia">
            <div class="stat-icon"><i class="bi bi-cash-coin"></i></div>
            <div class="stat-label">Payments I collected</div>
            <div class="stat-value">{{ number_format((float) $myPaidTotal, 0, ',', ' ') }}</div>
            <div class="stat-sub">FCFA paid</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-rose">
            <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
            <div class="stat-label">My pending payments</div>
            <div class="stat-value">{{ $myPendingPayments }}</div>
            <div class="stat-sub">Awaiting confirmation</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-blush">
            <div class="stat-icon"><i class="bi bi-door-open"></i></div>
            <div class="stat-label">Available rooms</div>
            <div class="stat-value">{{ $availableRooms }}</div>
            <div class="stat-sub">{{ $todayPickups }} pickup(s) today</div>
        </div>
    </div>
</div>

<div class="panel mb-4">
    <div class="panel-header">
        <h2 class="panel-title"><i class="bi bi-lightning-charge"></i> Quick actions</h2>
    </div>
    <div class="panel-body">
        <div class="row g-3">
            <div class="col-sm-6 col-lg-4">
                <a href="{{ route('deceased.create') }}" class="quick-action">
                    <span class="qa-icon"><i class="bi bi-person-plus"></i></span>
                    <span>
                        <span class="qa-title d-block">Register deceased</span>
                        <span class="qa-sub">Admit a new body</span>
                    </span>
                </a>
            </div>
            <div class="col-sm-6 col-lg-4">
                <a href="{{ route('deceased.index') }}" class="quick-action">
                    <span class="qa-icon"><i class="bi bi-search"></i></span>
                    <span>
                        <span class="qa-title d-block">Search records</span>
                        <span class="qa-sub">Find or update a deceased</span>
                    </span>
                </a>
            </div>
            <div class="col-sm-6 col-lg-4">
                <a href="{{ route('payments.create') }}" class="quick-action">
                    <span class="qa-icon"><i class="bi bi-phone"></i></span>
                    <span>
                        <span class="qa-title d-block">Record payment</span>
                        <span class="qa-sub">MTN / Orange Mobile Money</span>
                    </span>
                </a>
            </div>
            <div class="col-sm-6 col-lg-4">
                <a href="{{ route('schedule.create') }}" class="quick-action">
                    <span class="qa-icon"><i class="bi bi-calendar-plus"></i></span>
                    <span>
                        <span class="qa-title d-block">Schedule pickup</span>
                        <span class="qa-sub">Plan a release or burial</span>
                    </span>
                </a>
            </div>
            <div class="col-sm-6 col-lg-4">
                <a href="{{ route('deceased.verify.form') }}" class="quick-action">
                    <span class="qa-icon"><i class="bi bi-shield-check"></i></span>
                    <span>
                        <span class="qa-title d-block">Verify a family</span>
                        <span class="qa-sub">Check a verification key</span>
                    </span>
                </a>
            </div>
            <div class="col-sm-6 col-lg-4">
                <a href="{{ route('ai.index') }}" class="quick-action">
                    <span class="qa-icon"><i class="bi bi-robot"></i></span>
                    <span>
                        <span class="qa-title d-block">AI assistant</span>
                        <span class="qa-sub">Faire-part, condolences, SMS</span>
                    </span>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="panel">
            <div class="panel-header">
                <h2 class="panel-title"><i class="bi bi-calendar-event"></i> Upcoming pickups</h2>
                <a href="{{ route('schedule.index') }}" class="panel-link">View all</a>
            </div>
            <div class="panel-body">
                @forelse($upcomingPickups as $schedule)
                    <div class="list-row">
                        <div class="lr-icon"><i class="bi bi-truck"></i></div>
                        <div class="lr-main">
                            <div class="lr-title">{{ $schedule->deceased->full_name ?? 'Unknown' }}</div>
                            <div class="lr-sub">
                                {{ \Illuminate\Support\Carbon::parse($schedule->pickup_date)->format('D d M') }}
                                @if($schedule->pickup_time) &middot; {{ substr($schedule->pickup_time, 0, 5) }} @endif
                            </div>
                        </div>
                        <span class="status-badge status-{{ $schedule->status ?? 'pending' }}">
                            {{ $schedule->status ?? 'pending' }}
                        </span>
                    </div>
                @empty
                    <div class="empty-state"><i class="bi bi-calendar2-check"></i>No upcoming pickups.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="panel">
            <div class="panel-header">
                <h2 class="panel-title"><i class="bi bi-person-lines-fill"></i> My recent registrations</h2>
                <a href="{{ route('deceased.index') }}" class="panel-link">View all</a>
            </div>
            <div class="panel-body">
                @forelse($recentDeceased as $deceased)
                    <a href="{{ route('deceased.show', $deceased) }}" class="list-row text-decoration-none text-reset">
                        <div class="lr-icon"><i class="bi bi-person"></i></div>
                        <div class="lr-main">
                            <div class="lr-title">{{ $deceased->full_name }}</div>
                            <div class="lr-sub">
                                {{ $deceased->identifier ?? 'No identifier' }}
                                @if($deceased->room_name) &middot; {{ $deceased->room_name }} @endif
                            </div>
                        </div>
                        <span class="lr-sub">{{ $deceased->created_at?->diffForHumans() }}</span>
                    </a>
                @empty
                    <div class="empty-state"><i class="bi bi-person-plus"></i>You have not registered anyone yet.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="panel">
            <div class="panel-header">
                <h2 class="panel-title"><i class="bi bi-receipt"></i> My recent payments</h2>
                <a href="{{ route('payments.index') }}" class="panel-link">View all</a>
            </div>
            <div class="panel-body">
                @forelse($recentPayments as $payment)
                    <a href="{{ route('payments.show', $payment) }}" class="list-row text-decoration-none text-reset">
                        <div class="lr-icon"><i class="bi bi-cash"></i></div>
                        <div class="lr-main">
                            <div class="lr-title">{{ number_format((float) $payment->amount, 0, ',', ' ') }} FCFA</div>
                            <div class="lr-sub">{{ $payment->deceased->full_name ?? '—' }}</div>
                        </div>
                        <span class="status-badge status-{{ $payment->status }}">{{ $payment->status }}</span>
                    </a>
                @empty
                    <div class="empty-state"><i class="bi bi-wallet2"></i>No payments recorded yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
