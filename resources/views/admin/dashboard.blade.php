@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
@php
    $maxRevenue = max(array_values($monthlyRevenue) ?: [0]) ?: 1;
    $roles = \App\Models\User::ROLES;
@endphp

<div class="container pb-4">

    <div class="page-header d-flex flex-wrap justify-content-between align-items-end gap-3">
        <div>
            <span class="eyebrow"><i class="bi bi-shield-lock me-1"></i> Administrator</span>
            <h1>System dashboard</h1>
            <p>{{ now()->format('l, d F Y') }} &middot; everything happening across the mortuary.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('manager.dashboard') }}" class="btn btn-pink-soft rounded-pill px-3">
                <i class="bi bi-clipboard-data me-1"></i> Operations view
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-primary rounded-pill px-3">
                <i class="bi bi-people me-1"></i> Manage users
            </a>
        </div>
    </div>

    @include('partials.flash')

    {{-- Summary cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-berry">
                <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
                <div class="stat-label">Total revenue</div>
                <div class="stat-value">{{ number_format($totalRevenue, 0, ',', ' ') }}</div>
                <div class="stat-sub">FCFA from {{ $totalPayments }} payment(s)</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-pink">
                <div class="stat-icon"><i class="bi bi-person-vcard"></i></div>
                <div class="stat-label">Deceased registered</div>
                <div class="stat-value">{{ $totalDeceased }}</div>
                <div class="stat-sub">{{ $totalSchedules }} pickup(s) scheduled</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-fuchsia">
                <div class="stat-icon"><i class="bi bi-people"></i></div>
                <div class="stat-label">User accounts</div>
                <div class="stat-value">{{ $totalUsers }}</div>
                <div class="stat-sub">
                    {{ $roleCounts['user'] ?? 0 }} families &middot;
                    {{ $roleCounts['staff'] ?? 0 }} staff &middot;
                    {{ ($roleCounts['manager'] ?? 0) + ($roleCounts['admin'] ?? 0) }} managers/admins
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-rose">
                <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
                <div class="stat-label">Pending payments</div>
                <div class="stat-value">{{ $pendingPayments }}</div>
                <div class="stat-sub">{{ $availableRooms }} of {{ $totalRooms }} rooms available</div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- Revenue chart --}}
        <div class="col-lg-8">
            <div class="panel">
                <div class="panel-header">
                    <h2 class="panel-title"><i class="bi bi-bar-chart"></i> Revenue — last 6 months</h2>
                    <span class="small text-muted">FCFA</span>
                </div>
                <div class="panel-body">
                    <div class="bar-chart">
                        @foreach($monthlyRevenue as $month => $amount)
                            <div class="bar-col">
                                <span class="bar-value">{{ $amount > 0 ? number_format($amount / 1000, 0) . 'k' : '0' }}</span>
                                <div class="bar" style="height: {{ max(2, round(($amount / $maxRevenue) * 100)) }}%"
                                     title="{{ $month }}: {{ number_format($amount, 0, ',', ' ') }} FCFA"></div>
                                <span class="bar-label">{{ \Illuminate\Support\Str::before($month, ' ') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Team by role --}}
        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-header">
                    <h2 class="panel-title"><i class="bi bi-diagram-3"></i> Team by role</h2>
                </div>
                <div class="panel-body">
                    @foreach($roles as $roleKey => $roleName)
                        @php($count = $roleCounts[$roleKey] ?? 0)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="fw-semibold">{{ $roleName }}</span>
                                <span class="text-pink fw-semibold">{{ $count }}</span>
                            </div>
                            <div class="progress" style="height:8px;">
                                <div class="progress-bar" style="width: {{ $totalUsers ? round($count / $totalUsers * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    @endforeach

                    <div class="row g-2 mt-2">
                        <div class="col-6">
                            <a href="{{ route('storage.index') }}" class="quick-action py-2">
                                <span class="qa-icon" style="width:32px;height:32px;font-size:.95rem;"><i class="bi bi-door-closed"></i></span>
                                <span class="qa-title">Rooms</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('deceased.index') }}" class="quick-action py-2">
                                <span class="qa-icon" style="width:32px;height:32px;font-size:.95rem;"><i class="bi bi-person-vcard"></i></span>
                                <span class="qa-title">Deceased</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('schedule.index') }}" class="quick-action py-2">
                                <span class="qa-icon" style="width:32px;height:32px;font-size:.95rem;"><i class="bi bi-calendar-event"></i></span>
                                <span class="qa-title">Schedule</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('geolocation.index') }}" class="quick-action py-2">
                                <span class="qa-icon" style="width:32px;height:32px;font-size:.95rem;"><i class="bi bi-geo-alt"></i></span>
                                <span class="qa-title">Mortuaries</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Recent payments --}}
        <div class="col-lg-7">
            <div class="panel">
                <div class="panel-header">
                    <h2 class="panel-title"><i class="bi bi-receipt"></i> Recent payments</h2>
                </div>
                <div class="panel-body p-0">
                    @if($recentPayments->isEmpty())
                        <div class="empty-state"><i class="bi bi-wallet2"></i>No payments yet.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Receipt</th>
                                        <th>Deceased</th>
                                        <th class="text-end">Amount</th>
                                        <th>Status</th>
                                        <th class="pe-4"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentPayments as $payment)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-semibold small">{{ $payment->receipt_number }}</div>
                                                <div class="small text-muted">by {{ $payment->user->name ?? 'unknown' }}</div>
                                            </td>
                                            <td class="small">{{ $payment->deceased->full_name ?? '—' }}</td>
                                            <td class="text-end fw-semibold small">{{ number_format($payment->amount, 0, ',', ' ') }}</td>
                                            <td>
                                                <span class="status-badge status-default status-{{ $payment->status }}">{{ $payment->status }}</span>
                                            </td>
                                            <td class="pe-4 text-end">
                                                @if($payment->status === 'pending')
                                                    <form method="POST" action="{{ route('payment.confirm', $payment->id) }}" class="d-inline">
                                                        @csrf
                                                        <button class="btn btn-sm btn-primary rounded-pill">Confirm</button>
                                                    </form>
                                                @else
                                                    <a href="{{ route('payments.show', $payment) }}" class="btn btn-sm btn-pink-soft rounded-pill">View</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Newest users --}}
        <div class="col-lg-5">
            <div class="panel">
                <div class="panel-header">
                    <h2 class="panel-title"><i class="bi bi-person-plus"></i> Newest users</h2>
                    <a href="{{ route('admin.users.index') }}" class="panel-link">All users</a>
                </div>
                <div class="panel-body">
                    @forelse($recentUsers as $member)
                        <div class="list-row">
                            <div class="lr-icon fw-bold">{{ strtoupper(mb_substr($member->name, 0, 1)) }}</div>
                            <div class="lr-main">
                                <div class="lr-title">{{ $member->name }}</div>
                                <div class="lr-sub">{{ $member->email }}</div>
                            </div>
                            <span class="status-badge status-default">{{ $member->roleLabel() }}</span>
                        </div>
                    @empty
                        <div class="empty-state"><i class="bi bi-people"></i>No users yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
