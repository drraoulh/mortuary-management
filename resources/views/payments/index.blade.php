@extends('layouts.app')

@section('content')

<div class="container mt-4">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>My Payments</h2>

        <a href="{{ route('payments.create') }}" class="btn btn-success">
            Make Payment
        </a>

    </div>


    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif


    {{-- Error Message --}}
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif


    {{-- Payments Card --}}
    <div class="card shadow">

        <div class="card-header">
            <h5 class="mb-0">Payment History</h5>
        </div>

        <div class="card-body">

            @if($payments->isEmpty())

                <div class="alert alert-info">
                    You have not made any payments yet.
                </div>

            @else

                <div class="table-responsive">

                    <table class="table table-bordered table-hover align-middle">

                        <thead class="table-dark">

                            <tr>

                                <th>Receipt Number</th>

                                <th>Deceased</th>

                                <th>Date</th>

                                <th>Amount</th>

                                <th>Method</th>

                                <th>Network</th>

                                <th>Status</th>

                                <th>Action</th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($payments as $payment)

                                <tr>

                                    {{-- Receipt Number --}}
                                    <td>
                                        {{ $payment->receipt_number ?? 'N/A' }}
                                    </td>


                                    {{-- Deceased --}}
                                    <td>
                                        {{ optional($payment->deceased)->full_name ?? 'N/A' }}
                                    </td>


                                    {{-- Payment Date --}}
                                    <td>
                                        @if($payment->payment_date)
                                            {{ $payment->payment_date->format('d/m/Y') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>


                                    {{-- Amount --}}
                                    <td>
                                        {{ number_format((float) $payment->amount, 0) }}
                                        FCFA
                                    </td>


                                    {{-- Payment Method --}}
                                    <td>
                                        @if($payment->payment_method === 'mobile_money')
                                            Mobile Money
                                        @else
                                            {{ ucfirst(str_replace('_', ' ', $payment->payment_method ?? 'N/A')) }}
                                        @endif
                                    </td>


                                    {{-- Network --}}
                                    <td>
                                        @if($payment->mobile_operator)
                                            {{ $payment->mobile_operator }}
                                        @else
                                            N/A
                                        @endif
                                    </td>


                                    {{-- Status --}}
                                    <td>

                                        @if($payment->status === 'successful')

                                            <span class="badge bg-success">
                                                Successful
                                            </span>

                                        @elseif($payment->status === 'pending')

                                            <span class="badge bg-warning text-dark">
                                                Pending
                                            </span>

                                        @elseif($payment->status === 'failed')

                                            <span class="badge bg-danger">
                                                Failed
                                            </span>

                                        @else

                                            <span class="badge bg-secondary">
                                                {{ ucfirst($payment->status ?? 'Unknown') }}
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Action --}}
                                    <td>

                                        @if($payment->status === 'successful')

                                            <div class="d-flex gap-2">

                                                <a
                                                    href="{{ route('payments.show', $payment->id) }}"
                                                    class="btn btn-sm btn-primary"
                                                >
                                                    View Receipt
                                                </a>

                                                <a
                                                    href="{{ route('payments.receipt', $payment->id) }}"
                                                    class="btn btn-sm btn-danger"
                                                >
                                                    Download PDF
                                                </a>

                                            </div>

                                        @elseif($payment->status === 'pending')

                                            <a
                                                href="{{ route('payments.processing', $payment->id) }}"
                                                class="btn btn-sm btn-warning"
                                            >
                                                Continue Payment
                                            </a>

                                        @elseif($payment->status === 'failed')

                                            <span class="text-danger">
                                                Payment Failed
                                            </span>

                                        @else

                                            <span class="text-muted">
                                                No Receipt
                                            </span>

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

@endsection