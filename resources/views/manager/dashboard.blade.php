@extends('layouts.app')

@section('title', 'Staff Manager Dashboard')

@section('content')

<div class="container pb-4">

    <div class="page-header d-flex flex-wrap justify-content-between align-items-end gap-3">
        <div>
            <span class="eyebrow"><i class="bi bi-clipboard-data me-1"></i> Staff Manager</span>
            <h1>Operations overview</h1>
            <p>{{ now()->format('l, d F Y') }} &middot; supervise rooms, payments, pickups and your team.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('schedule.create') }}" class="btn btn-pink-soft rounded-pill px-3">
                <i class="bi bi-calendar-plus me-1"></i> New pickup
            </a>
            <a href="{{ route('deceased.create') }}" class="btn btn-primary rounded-pill px-3">
                <i class="bi bi-plus-lg me-1"></i> Register a body
            </a>
        </div>
    </div>

    @include('partials.flash')

    {{-- Summary cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-pink">
                <div class="stat-icon"><i class="bi bi-people"></i></div>
                <div class="stat-label">Bodies in the system</div>
                <div class="stat-value">{{ $totalDeceased }}</div>
                <div class="stat-sub">{{ $admittedThisMonth }} admitted this month</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-berry">
                <div class="stat-icon"><i class="bi bi-door-closed"></i></div>
                <div class="stat-label">Room occupancy</div>
                <div class="stat-value">{{ $occupancyRate }}%</div>
                <div class="stat-sub">{{ $availableRooms }} of {{ $totalRooms }} rooms available</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-fuchsia">
                <div class="stat-icon"><i class="bi bi-graph-up-arrow"></i></div>
                <div class="stat-label">Revenue this month</div>
                <div class="stat-value">{{ number_format($revenueThisMonth, 0, ',', ' ') }}</div>
                <div class="stat-sub">FCFA collected</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-rose">
                <div class="stat-icon"><i class="bi bi-bell"></i></div>
                <div class="stat-label">Needs your attention</div>
                <div class="stat-value">{{ $pendingPaymentsCount + $pendingSchedules->count() }}</div>
                <div class="stat-sub">{{ $pendingPaymentsCount }} payment(s) &middot; {{ $todayPickups }} pickup(s) today</div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- Payments awaiting confirmation --}}
        <div class="col-lg-6">
            <div class="panel">
                <div class="panel-header">
                    <h2 class="panel-title"><i class="bi bi-cash-stack"></i> Payments awaiting confirmation</h2>
                    <span class="badge rounded-pill bg-pink-soft">{{ $pendingPaymentsCount }}</span>
                </div>
                <div class="panel-body">
                    @forelse($pendingPayments as $payment)
                        <div class="list-row">
                            <div class="lr-icon"><i class="bi bi-cash"></i></div>
                            <div class="lr-main">
                                <div class="lr-title">
                                    {{ number_format($payment->amount, 0, ',', ' ') }} FCFA
                                    &middot; {{ $payment->deceased->full_name ?? '—' }}
                                </div>
                                <div class="lr-sub">
                                    {{ $payment->receipt_number }}
                                    &middot; by {{ $payment->user->name ?? 'unknown' }}
                                    &middot; {{ $payment->created_at?->diffForHumans() }}
                                </div>
                            </div>
                            <form method="POST" action="{{ route('payment.confirm', $payment->id) }}">
                                @csrf
                                <button class="btn btn-sm btn-primary rounded-pill px-3">Confirm</button>
                            </form>
                        </div>
                    @empty
                        <div class="empty-state"><i class="bi bi-check2-all"></i>All payments are confirmed.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Pickups awaiting approval --}}
        <div class="col-lg-6">
            <div class="panel">
                <div class="panel-header">
                    <h2 class="panel-title"><i class="bi bi-calendar-check"></i> Pickups awaiting approval</h2>
                    <a href="{{ route('schedule.index') }}" class="panel-link">View schedule</a>
                </div>
                <div class="panel-body">
                    @forelse($pendingSchedules as $schedule)
                        <div class="list-row">
                            <div class="lr-icon"><i class="bi bi-truck"></i></div>
                            <div class="lr-main">
                                <div class="lr-title">{{ $schedule->deceased->full_name ?? 'Unknown' }}</div>
                                <div class="lr-sub">
                                    Pickup {{ \Illuminate\Support\Carbon::parse($schedule->pickup_date)->format('D d M') }}
                                    @if($schedule->pickup_time) at {{ substr($schedule->pickup_time, 0, 5) }} @endif
                                    @if($schedule->burial_date)
                                        &middot; burial {{ \Illuminate\Support\Carbon::parse($schedule->burial_date)->format('d M') }}
                                    @endif
                                </div>
                            </div>
                            <form method="POST" action="{{ route('schedule.confirm', $schedule->id) }}">
                                @csrf
                                <button class="btn btn-sm btn-primary rounded-pill px-3">Approve</button>
                            </form>
                        </div>
                    @empty
                        <div class="empty-state"><i class="bi bi-calendar2-check"></i>No pickups waiting for approval.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Storage rooms --}}
        <div class="col-lg-5">
            <div class="panel">
                <div class="panel-header">
                    <h2 class="panel-title"><i class="bi bi-door-open"></i> Storage rooms</h2>
                    <a href="{{ route('storage.index') }}" class="panel-link">Manage rooms</a>
                </div>
                <div class="panel-body">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">Occupancy</span>
                        <span class="fw-semibold text-pink">{{ $occupancyRate }}%</span>
                    </div>
                    <div class="progress mb-3" style="height:10px;">
                        <div class="progress-bar" role="progressbar" style="width: {{ $occupancyRate }}%"
                             aria-valuenow="{{ $occupancyRate }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>

                    @forelse($rooms as $room)
                        <div class="list-row">
                            <div class="lr-icon"><i class="bi bi-snow"></i></div>
                            <div class="lr-main">
                                <div class="lr-title">Room {{ $room->room_number }}</div>
                                <div class="lr-sub">Capacity {{ $room->capacity }}</div>
                            </div>
                            <span class="status-badge status-default status-{{ $room->status }}">{{ $room->status }}</span>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="bi bi-door-closed"></i>No rooms yet.
                            <a href="{{ route('storage.create') }}">Add one</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Staff activity --}}
        <div class="col-lg-7">
            <div class="panel">
                <div class="panel-header">
                    <h2 class="panel-title"><i class="bi bi-people"></i> Staff activity</h2>
                </div>
                <div class="panel-body p-0">
                    @if($staffMembers->isEmpty())
                        <div class="empty-state"><i class="bi bi-person-x"></i>No mortuary staff accounts yet.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Staff member</th>
                                        <th class="text-center">Bodies registered</th>
                                        <th class="text-center">Payments recorded</th>
                                        <th class="pe-4">Joined</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($staffMembers as $member)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-semibold">{{ $member->name }}</div>
                                                <div class="small text-muted">{{ $member->email }}</div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge rounded-pill bg-pink-soft">{{ $member->deceaseds_count }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge rounded-pill bg-pink-soft">{{ $member->payments_count }}</span>
                                            </td>
                                            <td class="pe-4 small text-muted">{{ $member->created_at?->format('d M Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
