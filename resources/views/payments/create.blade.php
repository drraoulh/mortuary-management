@extends('layouts.app')

@section('title', 'Make a Payment')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="page-hero">
            <span class="eyebrow">Payments</span>
            <h1>Mobile Money payment</h1>
            <p>Pay mortuary fees with MTN Mobile Money or Orange Money through CamPay.</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($isDemo)
            <div class="alert alert-info">
                CamPay <strong>demo</strong> mode is active.
                You can enter any invoice amount; the backend will collect exactly
                <strong>{{ number_format($maxAmount, 0) }} XAF</strong> via CamPay for testing.
                Confirm the USSD prompt on the phone.
            </div>
        @endif

        @if($deceaseds->isEmpty())
            <div class="surface-card empty-state">
                <i class="bi bi-shield-lock"></i>
                @if(auth()->user()->isClient())
                    <p class="mb-3">Verify your deceased relative with the key given by the mortuary before paying.</p>
                    <a href="{{ route('deceased.verify.form') }}" class="btn btn-accent">
                        <i class="bi bi-shield-check me-1"></i> Verify a deceased
                    </a>
                @else
                    <p class="mb-3">Register a deceased record before creating a payment.</p>
                    <a href="{{ route('deceased.create') }}" class="btn btn-accent">
                        <i class="bi bi-person-plus me-1"></i> Add deceased
                    </a>
                @endif
            </div>
        @else
            <form action="{{ route('payments.store') }}" method="POST" class="surface-card p-4">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">Deceased</label>
                    <select name="deceased_id" class="form-select" required>
                        @foreach($deceaseds as $deceased)
                            <option value="{{ $deceased->id }}" @selected(old('deceased_id', $selectedDeceasedId) == $deceased->id)>
                                {{ $deceased->full_name }}
                                @if($deceased->identifier) ({{ $deceased->identifier }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Amount (XAF)</label>
                        <input type="number" name="amount" class="form-control" required min="1" step="1"
                               value="{{ old('amount') }}" placeholder="Invoice amount (XAF)">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Payment date</label>
                        <input type="date" name="payment_date" class="form-control" required
                               value="{{ old('payment_date', now()->toDateString()) }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Balance remaining (optional)</label>
                    <input type="number" name="balance" class="form-control" min="0" step="1" value="{{ old('balance', 0) }}">
                </div>

                <input type="hidden" name="payment_method" value="mobile_money">

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Mobile Money network</label>
                        <select name="mobile_operator" id="mobile_operator" class="form-select" required>
                            <option value="">Select network</option>
                            <option value="MTN" @selected(old('mobile_operator') === 'MTN')>MTN Mobile Money</option>
                            <option value="ORANGE" @selected(old('mobile_operator') === 'ORANGE')>Orange Money</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Phone number</label>
                        <input type="text" name="phone_number" id="phone_number" class="form-control" required
                               value="{{ old('phone_number') }}" placeholder="677123456 or 237677123456">
                        <small class="text-muted">Cameroon MTN/Orange number that will authorize the payment.</small>
                    </div>
                </div>

                <button type="submit" class="btn btn-accent btn-lg w-100">
                    <i class="bi bi-phone me-1"></i> Pay with CamPay
                </button>
            </form>
        @endif
    </div>
</div>
@endsection
