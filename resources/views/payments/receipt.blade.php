<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <title>
        Payment Receipt
    </title>

    <style>

        body {
            font-family: DejaVu Sans, sans-serif;
            padding: 30px;
        }

        .title {
            text-align: center;
            margin-bottom: 30px;
        }

        .success {
            color: green;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            width: 35%;
            background: #f5f5f5;
        }

    </style>

</head>

<body>

<div class="title">

    <h1>
        MORTUARY MANAGEMENT SYSTEM
    </h1>

    <h2>
        PAYMENT RECEIPT
    </h2>

    <p class="success">
        PAYMENT SUCCESSFUL
    </p>

</div>


<table>

    <tr>
        <th>Receipt Number</th>
        <td>
            {{ $payment->receipt_number }}
        </td>
    </tr>

    <tr>
        <th>Deceased</th>
        <td>
            {{ $payment->deceased->full_name ?? 'N/A' }}
        </td>
    </tr>

    <tr>
        <th>Amount</th>
        <td>
            {{ number_format($payment->amount, 0) }} FCFA
        </td>
    </tr>

    <tr>
        <th>Payment Date</th>
        <td>
            {{ $payment->payment_date?->format('d/m/Y') }}
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
        <th>CamPay Reference</th>
        <td>
            {{ $payment->campay_reference ?? 'N/A' }}
        </td>
    </tr>

    <tr>
        <th>Operator Reference</th>
        <td>
            {{ $payment->campay_operator_reference ?? 'N/A' }}
        </td>
    </tr>

    <tr>
        <th>Status</th>
        <td class="success">
            SUCCESSFUL
        </td>
    </tr>

</table>

</body>

</html>