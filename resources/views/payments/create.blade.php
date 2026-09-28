@extends('layouts.app')

@section('title', 'Make a Payment')

@section('content')

<div class="container pb-4" style="max-width: 760px;">

    <div class="page-header">
        <span class="eyebrow"><i class="bi bi-phone me-1"></i> Mobile Money</span>
        <h1>Make a payment</h1>
        <p>The payment request is sent to the phone number below; confirm it on the phone to complete it.</p>
    </div>

    @include('partials.flash')

    @if($errors->any())
        <div class="alert alert-danger rounded-4">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="panel">
        <div class="panel-body p-4">
            @if($deceaseds->isEmpty())
                <div class="empty-state">
                    <i class="bi bi-shield-lock"></i>
                    @if(auth()->user()->isClient())
                        Verify your loved one with the key given by the mortuary before making a payment.
                        <div class="mt-3">
                            <a href="{{ route('deceased.verify-form') }}" class="btn btn-primary rounded-pill px-4">Verify a deceased</a>
                        </div>
                    @else
                        No deceased registered yet.
                    @endif
                </div>
            @else
                <form action="{{ route('payments.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="payment_method" value="mobile_money">

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="deceased_id" class="form-label fw-semibold">Deceased</label>
                            <select name="deceased_id" id="deceased_id" class="form-select" required>
                                <option value="">Select the deceased</option>
                                @foreach($deceaseds as $deceased)
                                    <option value="{{ $deceased->id }}"
                                        @selected(old('deceased_id', $selectedDeceasedId) == $deceased->id)>
                                        {{ $deceased->full_name }}
                                        @if($deceased->identifier) ({{ $deceased->identifier }}) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="amount" class="form-label fw-semibold">Amount (FCFA)</label>
                            <input type="number" name="amount" id="amount" min="1" class="form-control"
                                   value="{{ old('amount') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label for="balance" class="form-label fw-semibold">Remaining balance (FCFA)</label>
                            <input type="number" name="balance" id="balance" min="0" class="form-control"
                                   value="{{ old('balance', 0) }}">
                        </div>

                        <div class="col-md-6">
                            <label for="payment_date" class="form-label fw-semibold">Payment date</label>
                            <input type="date" name="payment_date" id="payment_date" class="form-control"
                                   value="{{ old('payment_date', now()->toDateString()) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label for="mobile_operator" class="form-label fw-semibold">Network</label>
                            <select name="mobile_operator" id="mobile_operator" class="form-select" required>
                                <option value="">Select network</option>
                                <option value="MTN" @selected(old('mobile_operator') === 'MTN')>MTN Mobile Money</option>
                                <option value="ORANGE" @selected(old('mobile_operator') === 'ORANGE')>Orange Money</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="phone_number" class="form-label fw-semibold">Mobile money phone number</label>
                            <input type="text" name="phone_number" id="phone_number" class="form-control"
                                   value="{{ old('phone_number') }}" placeholder="Example: 677123456" required>
                            <div class="form-text">The MTN or Orange number that will authorise the payment.</div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-send me-1"></i> Send payment request
                        </button>
                        <a href="{{ route('payments.index') }}" class="btn btn-pink-soft rounded-pill px-4">Cancel</a>
                    </div>
                </form>
            @endif
        </div>
    </div>

</div>
@endsection
