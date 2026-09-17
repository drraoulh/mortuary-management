@extends('layouts.app')

@section('content')
<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <h2 class="mb-3" style="color:#0b3d91;">Create Mobile Money Payment</h2>

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

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
                    Maximum amount is <strong>{{ number_format($maxAmount, 0) }} XAF</strong>.
                    Use a real MTN/Orange test number and confirm the USSD prompt on the phone.
                </div>
            @endif

            @if($deceaseds->isEmpty())
                <div class="alert alert-warning">
                    Register a deceased record before creating a payment.
                    <a href="{{ route('deceased.create') }}">Add deceased</a>
                </div>
            @else
                <form action="{{ route('payments.store') }}" method="POST" class="p-4 border rounded bg-light">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Deceased</label>
                        <select name="deceased_id" class="form-select" required>
                            @foreach($deceaseds as $deceased)
                                <option value="{{ $deceased->id }}" @selected(old('deceased_id') == $deceased->id)>
                                    {{ $deceased->full_name }}
                                    @if($deceased->identifier) ({{ $deceased->identifier }}) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Amount (XAF)</label>
                        <input
                            type="number"
                            name="amount"
                            class="form-control"
                            required
                            min="1"
                            @if($maxAmount) max="{{ $maxAmount }}" @endif
                            step="1"
                            value="{{ old('amount', $isDemo ? 5 : '') }}"
                            placeholder="{{ $isDemo ? 'Max '.$maxAmount.' in demo' : 'Amount' }}"
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Date</label>
                        <input
                            type="date"
                            name="payment_date"
                            class="form-control"
                            required
                            value="{{ old('payment_date', now()->toDateString()) }}"
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Balance remaining (optional)</label>
                        <input
                            type="number"
                            name="balance"
                            class="form-control"
                            min="0"
                            step="1"
                            value="{{ old('balance', 0) }}"
                        >
                    </div>

                    <input type="hidden" name="payment_method" value="mobile_money">

                    <div class="mb-3">
                        <label class="form-label">Mobile Money Network</label>
                        <select name="mobile_operator" id="mobile_operator" class="form-select" required>
                            <option value="">Select network</option>
                            <option value="MTN" @selected(old('mobile_operator') === 'MTN')>MTN Mobile Money</option>
                            <option value="ORANGE" @selected(old('mobile_operator') === 'ORANGE')>Orange Money</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input
                            type="text"
                            name="phone_number"
                            id="phone_number"
                            class="form-control"
                            required
                            value="{{ old('phone_number') }}"
                            placeholder="677123456 or 237677123456"
                        >
                        <small class="text-muted">
                            Cameroon MTN/Orange number that will authorize the payment.
                        </small>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-success btn-lg px-4">
                            Pay with CamPay
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
