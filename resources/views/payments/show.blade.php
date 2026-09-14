@extends('layouts.app')

@section('content')

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card shadow">

                <div class="card-header bg-success text-white">

                    <h4 class="mb-0">
                        Payment Receipt
                    </h4>

                </div>

                <div class="card-body">

                    <div class="text-center mb-4">

                        <h3>
                            MORTUARY SYSTEM
                        </h3>

                        <h5>
                            OFFICIAL PAYMENT RECEIPT
                        </h5>

                    </div>


                    <table class="table table-bordered">

                        <tr>
                            <th>Receipt Number</th>
                            <td>
                                {{ $payment->receipt_number }}
                            </td>
                        </tr>

                        <tr>
                            <th>Deceased</th>
                            <td>
                                {{ optional($payment->deceased)->full_name ?? 'N/A' }}
                            </td>
                        </tr>

                        <tr>
                            <th>Payment Date</th>
                            <td>
                                {{ $payment->payment_date?->format('d/m/Y') }}
                            </td>
                        </tr>

                        <tr>
                            <th>Amount</th>
                            <td>
                                {{ number_format((float) $payment->amount, 0) }}
                                FCFA
                            </td>
                        </tr>

                        <tr>
                            <th>Payment Method</th>
                            <td>
                                Mobile Money
                            </td>
                        </tr>

                        <tr>
                            <th>Network</th>
                            <td>
                                {{ $payment->mobile_operator ?? 'N/A' }}
                            </td>
                        </tr>

                        <tr>
                            <th>Phone Number</th>
                            <td>
                                {{ $payment->phone_number ?? 'N/A' }}
                            </td>
                        </tr>

                        <tr>
                            <th>Status</th>
                            <td>

                                <span class="badge bg-success">
                                    Successful
                                </span>

                            </td>
                        </tr>

                        @if($payment->campay_reference)

                        <tr>
                            <th>CamPay Reference</th>
                            <td>
                                {{ $payment->campay_reference }}
                            </td>
                        </tr>

                        @endif

                        @if($payment->campay_operator_reference)

                        <tr>
                            <th>Transaction Reference</th>
                            <td>
                                {{ $payment->campay_operator_reference }}
                            </td>
                        </tr>

                        @endif

                    </table>


                    <div class="text-center mt-4">

                        <a
                            href="{{ route('payments.receipt', $payment->id) }}"
                            class="btn btn-danger"
                        >
                            Download PDF
                        </a>


                        <a
                            href="{{ route('payments.index') }}"
                            class="btn btn-secondary"
                        >
                            Back to My Payments
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection