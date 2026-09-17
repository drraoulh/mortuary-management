@extends('layouts.app')

@section('content')

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-7">

            <div class="card shadow">

                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        Payment Processing
                    </h4>
                </div>

                <div class="card-body text-center">

                    {{-- SUCCESS MESSAGE --}}
                    @if(session('success'))

                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>

                    @endif


                    {{-- ERROR MESSAGE --}}
                    @if(session('error'))

                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>

                    @endif


                    <h5 class="mb-4">
                        Payment Details
                    </h5>


                    {{-- RECEIPT NUMBER --}}
                    <p>
                        <strong>Receipt Number:</strong><br>

                        {{ $payment->receipt_number }}
                    </p>


                    {{-- AMOUNT --}}
                    <p>
                        <strong>Amount:</strong><br>

                        {{ number_format((float) $payment->amount, 0) }}
                        FCFA
                    </p>


                    {{-- NETWORK --}}
                    <p>
                        <strong>Network:</strong><br>

                        @if($payment->mobile_operator === 'MTN')

                            MTN Mobile Money

                        @elseif($payment->mobile_operator === 'ORANGE')

                            Orange Money

                        @else

                            Mobile Money

                        @endif

                    </p>


                    {{-- PHONE --}}
                    <p>
                        <strong>Phone:</strong><br>

                        {{ $payment->phone_number ?? 'N/A' }}

                    </p>

                    @if(session('ussd_code'))
                        <p>
                            <strong>USSD code:</strong><br>
                            <span class="fs-4 fw-bold">{{ session('ussd_code') }}</span>
                        </p>
                    @endif

                    @if($payment->campay_reference)
                        <p>
                            <strong>CamPay reference:</strong><br>
                            <code>{{ $payment->campay_reference }}</code>
                        </p>
                    @endif


                    {{-- STATUS --}}
                    <p>
                        <strong>Status:</strong><br>

                        @if($payment->status === 'pending')

                            <span class="badge bg-warning text-dark">
                                Pending
                            </span>

                        @elseif($payment->status === 'successful')

                            <span class="badge bg-success">
                                Successful
                            </span>

                        @elseif($payment->status === 'failed')

                            <span class="badge bg-danger">
                                Failed
                            </span>

                        @else

                            <span class="badge bg-secondary">
                                {{ ucfirst($payment->status) }}
                            </span>

                        @endif

                    </p>


                    <hr>


                    {{-- ========================================= --}}
                    {{-- PENDING --}}
                    {{-- ========================================= --}}

                    @if($payment->status === 'pending')

                        <div class="alert alert-warning">

                            <h5>
                                Payment Pending
                            </h5>

                            <p class="mb-0">
                                Confirm the payment on your phone
                                @if(session('ussd_code'))
                                    (dial <strong>{{ session('ussd_code') }}</strong> if prompted)
                                @endif
                                . This page refreshes the CamPay status automatically.
                            </p>

                        </div>


                    {{-- ========================================= --}}
                    {{-- SUCCESSFUL --}}
                    {{-- ========================================= --}}

                    @elseif($payment->status === 'successful')

                        <div class="alert alert-success">

                            <h4>
                                Payment Successful!
                            </h4>

                            <p class="mb-0">
                                Your payment has been confirmed successfully.
                            </p>

                        </div>


                        <div class="d-flex justify-content-center gap-2 mt-4">

                            {{-- VIEW RECEIPT --}}

                            <a
                                href="{{ route('payments.show', $payment->id) }}"
                                class="btn btn-primary btn-lg"
                            >
                                View Receipt
                            </a>


                            {{-- DOWNLOAD PDF --}}

                            <a
                                href="{{ route('payments.receipt', $payment->id) }}"
                                class="btn btn-danger btn-lg"
                            >
                                Download PDF
                            </a>

                        </div>


                    {{-- ========================================= --}}
                    {{-- FAILED --}}
                    {{-- ========================================= --}}

                    @elseif($payment->status === 'failed')

                        <div class="alert alert-danger">

                            <h4>
                                Payment Failed
                            </h4>

                            <p>
                                The payment could not be completed.
                            </p>

                        </div>


                        <a
                            href="{{ route('payments.create') }}"
                            class="btn btn-primary"
                        >
                            Try Again
                        </a>

                    @endif


                    {{-- BACK TO PAYMENTS --}}

                    <div class="mt-4">

                        <a
                            href="{{ route('payments.index') }}"
                            class="btn btn-outline-secondary"
                        >
                            My Payments
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const checkUrl = "{{ route('payments.check-status', $payment->id) }}";
    const paymentsUrl = "{{ route('payments.index') }}";

    @if($payment->status === 'pending')

        let attempts = 0;
        const maxAttempts = 60;

        const checkPayment = async () => {

            attempts++;

            try {

                const response = await fetch(checkUrl, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                console.log('CamPay status:', data.status);

               if (data.status === 'SUCCESSFUL') {

    window.location.href = data.receipt_url;

    return;
}

                if (data.status === 'FAILED') {

                    window.location.reload();

                    return;
                }

                if (attempts < maxAttempts) {

                    setTimeout(checkPayment, 5000);

                } else {

                    console.log(
                        'Payment status check timed out.'
                    );

                }

            } catch (error) {

                console.error(
                    'Payment status check error:',
                    error
                );

                if (attempts < maxAttempts) {

                    setTimeout(checkPayment, 5000);

                }

            }
        };

        checkPayment();

    @endif

});
</script>

@endsection