@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="page-hero">
    <h1>Dashboard</h1>
    <p>Welcome back, {{ auth()->user()->name }}. Manage cases, payments and AI tools.</p>
</div>

<div class="stat-grid mb-4">
    <div class="stat-card">
        <div class="label">Deceased</div>
        <div class="value">{{ $totalDeceased }}</div>
    </div>
    <div class="stat-card">
        <div class="label">Available rooms</div>
        <div class="value">{{ $availableRooms }}</div>
    </div>
    <div class="stat-card">
        <div class="label">Pending payments</div>
        <div class="value">{{ $pendingPayments }}</div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="surface-card p-4 h-100">
            <h5 class="fw-bold">Cases</h5>
            <p class="text-muted">Register deceased and open AI faire-part tools.</p>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('deceased.create') }}" class="btn btn-accent btn-sm">Register</a>
                <a href="{{ route('deceased.index') }}" class="btn btn-soft btn-sm">View all</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="surface-card p-4 h-100">
            <h5 class="fw-bold">Payments</h5>
            <p class="text-muted">CamPay mobile money collection and receipts.</p>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('payments.create') }}" class="btn btn-accent btn-sm">Pay</a>
                <a href="{{ route('payments.index') }}" class="btn btn-soft btn-sm">History</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="surface-card p-4 h-100">
            <h5 class="fw-bold">AI</h5>
            <p class="text-muted">FR/EN faire-part, condolences, SMS and summaries.</p>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('ai.index') }}" class="btn btn-accent btn-sm">Open AI</a>
                <a href="{{ route('geolocation.index') }}" class="btn btn-soft btn-sm">Geo</a>
            </div>
        </div>
    </div>
</div>
@endsection
