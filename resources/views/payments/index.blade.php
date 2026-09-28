@extends('layouts.app')

@section('title', 'My Payments')

@section('content')
<div class="page-hero d-flex flex-wrap justify-content-between align-items-end gap-3">
    <div>
        <span class="eyebrow">Payments</span>
        <h1>My payments</h1>
        <p>Mobile Money payments you made and their receipts.</p>
    </div>
    <a href="{{ route('payments.create') }}" class="btn btn-accent">
        <i class="bi bi-plus-circle me-1"></i> Make payment
    </a>
</div>

<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title"><i class="bi bi-receipt"></i> Payment history</h2>
        <span class="text-muted small">{{ $payments->count() }} payment(s)</span>
    </div>

    @if($payments->isEmpty())
        <div class="empty-state">
            <i class="bi bi-wallet2"></i>
            You have not made any payments yet.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Receipt</th>
                        <th>Deceased</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Network</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                        <tr>
                            <td class="fw-semibold">{{ $payment->receipt_number ?? 'N/A' }}</td>
                            <td>{{ optional($payment->deceased)->full_name ?? 'N/A' }}</td>
                            <td>{{ $payment->payment_date ? $payment->payment_date->format('d/m/Y') : 'N/A' }}</td>
                            <td>{{ number_format((float) $payment->amount, 0, ',', ' ') }} FCFA</td>
                            <td>{{ $payment->mobile_operator ?? 'N/A' }}</td>
                            <td>
                                <span class="status-badge status-{{ $payment->status }}">
                                    {{ $payment->status ?? 'unknown' }}
                                </span>
                            </td>
                            <td class="text-end">
                                @if(in_array($payment->status, ['successful', 'confirmed'], true))
                                    <div class="d-inline-flex gap-2">
                                        <a href="{{ route('payments.show', $payment->id) }}" class="btn btn-sm btn-soft">
                                            <i class="bi bi-eye"></i> Receipt
                                        </a>
                                        <a href="{{ route('payments.receipt', $payment->id) }}" class="btn btn-sm btn-accent">
                                            <i class="bi bi-file-earmark-pdf"></i> PDF
                                        </a>
                                    </div>
                                @elseif($payment->status === 'pending')
                                    <a href="{{ route('payments.processing', $payment->id) }}" class="btn btn-sm btn-warning">
                                        Continue payment
                                    </a>
                                @elseif($payment->status === 'failed')
                                    <span class="text-danger small">Payment failed</span>
                                @else
                                    <span class="text-muted small">No receipt</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
