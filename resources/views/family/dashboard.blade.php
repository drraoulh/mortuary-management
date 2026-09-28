@extends('layouts.app')

@section('title', 'My Space')

@section('content')

<div class="container pb-4">

    <div class="page-header d-flex flex-wrap justify-content-between align-items-end gap-3">
        <div>
            <span class="eyebrow"><i class="bi bi-heart me-1"></i> Family / Client</span>
            <h1>Welcome, {{ auth()->user()->name }}</h1>
            <p>Follow your loved one's file, make payments and prepare the announcement.</p>
        </div>
        <a href="{{ route('deceased.verify-form') }}" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-shield-check me-1"></i> Verify a deceased
        </a>
    </div>

    @include('partials.flash')

    {{-- Summary cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-4">
            <div class="stat-card stat-pink">
                <div class="stat-icon"><i class="bi bi-person-heart"></i></div>
                <div class="stat-label">My loved ones on file</div>
                <div class="stat-value">{{ $myDeceased->count() }}</div>
                <div class="stat-sub">Unlocked with a verification key</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="stat-card stat-fuchsia">
                <div class="stat-icon"><i class="bi bi-cash-coin"></i></div>
                <div class="stat-label">Total paid</div>
                <div class="stat-value">{{ number_format($paidTotal, 0, ',', ' ') }}</div>
                <div class="stat-sub">FCFA over {{ $paymentsCount }} payment(s)</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="stat-card stat-blush">
                <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
                <div class="stat-label">Pending payments</div>
                <div class="stat-value">{{ $pendingPayments }}</div>
                <div class="stat-sub">Waiting for confirmation</div>
            </div>
        </div>
    </div>

    {{-- Quick actions --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <a href="{{ route('deceased.verify-form') }}" class="quick-action">
                <span class="qa-icon"><i class="bi bi-shield-check"></i></span>
                <span>
                    <span class="qa-title d-block">Verify a deceased</span>
                    <span class="qa-sub">Use the key given by the mortuary</span>
                </span>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3">
            <a href="{{ route('payments.create') }}" class="quick-action">
                <span class="qa-icon"><i class="bi bi-phone"></i></span>
                <span>
                    <span class="qa-title d-block">Make a payment</span>
                    <span class="qa-sub">MTN / Orange mobile money</span>
                </span>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3">
            <a href="{{ route('faire-part.create') }}" class="quick-action">
                <span class="qa-icon"><i class="bi bi-envelope-paper-heart"></i></span>
                <span>
                    <span class="qa-title d-block">Faire-part</span>
                    <span class="qa-sub">Generate an announcement</span>
                </span>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3">
            <a href="{{ route('geolocation.index') }}" class="quick-action">
                <span class="qa-icon"><i class="bi bi-geo-alt"></i></span>
                <span>
                    <span class="qa-title d-block">Find a mortuary</span>
                    <span class="qa-sub">Near you in Yaoundé or Douala</span>
                </span>
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Loved ones --}}
        <div class="col-lg-7">
            <div class="panel">
                <div class="panel-header">
                    <h2 class="panel-title"><i class="bi bi-person-heart"></i> My loved ones</h2>
                </div>
                <div class="panel-body">
                    @forelse($myDeceased as $deceased)
                        <div class="list-row">
                            <div class="lr-icon"><i class="bi bi-flower1"></i></div>
                            <div class="lr-main">
                                <div class="lr-title">{{ $deceased->full_name }}</div>
                                <div class="lr-sub">
                                    {{ $deceased->identifier ?? 'No identifier' }}
                                    @if($deceased->schedule)
                                        &middot; pickup {{ \Illuminate\Support\Carbon::parse($deceased->schedule->pickup_date)->format('d M Y') }}
                                        ({{ $deceased->schedule->status ?? 'pending' }})
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('payments.create', ['deceased_id' => $deceased->id]) }}"
                               class="btn btn-sm btn-primary rounded-pill px-3">Pay</a>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="bi bi-shield-lock"></i>
                            Enter the verification key given by the mortuary to follow your loved one's file.
                            <div class="mt-2">
                                <a href="{{ route('deceased.verify-form') }}" class="btn btn-sm btn-primary rounded-pill px-3">Verify now</a>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Recent payments --}}
        <div class="col-lg-5">
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
                                <div class="lr-title">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</div>
                                <div class="lr-sub">{{ $payment->deceased->full_name ?? '—' }}</div>
                            </div>
                            <span class="status-badge status-default status-{{ $payment->status }}">{{ $payment->status }}</span>
                        </a>
                    @empty
                        <div class="empty-state"><i class="bi bi-wallet2"></i>No payments yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
