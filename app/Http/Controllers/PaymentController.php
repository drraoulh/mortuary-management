<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use App\Services\CamPayService;

class PaymentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Display payments
    |--------------------------------------------------------------------------
    */
    public function index()
{
    $payments = Payment::where(
        'user_id',
        auth()->id()
    )
    ->with('deceased')
    ->latest()
    ->get();

    return view(
        'payments.index',
        compact('payments')
    );
}


    /*
    |--------------------------------------------------------------------------
    | Show payment form
    |--------------------------------------------------------------------------
    */
    public function create(Request $request)
    {
        $deceaseds = $request->user()->visibleDeceased()->orderBy('full_name')->get();

        $selectedDeceasedId = $request->query('deceased_id');

        return view('payments.create', compact('deceaseds', 'selectedDeceasedId'));
    }


    /*
    |--------------------------------------------------------------------------
    | Store payment
    |--------------------------------------------------------------------------
    */
   public function store(Request $request, CamPayService $campay)
{
    
    $request->validate([
        'deceased_id' => 'required|exists:deceaseds,id',

        'amount' => 'required|numeric|min:1',

        'balance' => 'nullable|numeric|min:0',

        'payment_date' => 'required|date',

        'payment_method' => 'required|in:mobile_money',

        'mobile_operator' => 'required|in:MTN,ORANGE',

        'phone_number' => [
            'required',
            'regex:/^(?:237)?6[5-9][0-9]{7}$/',
        ],
    ]);

    abort_unless(
        $request->user()->visibleDeceased()->whereKey($request->deceased_id)->exists(),
        403
    );
    


    /*
    |--------------------------------------------------------------------------
    | Normalize phone number
    |--------------------------------------------------------------------------
    */

    $phone = preg_replace(
        '/\D/',
        '',
        $request->phone_number
    );


    if (strlen($phone) === 9) {

        $phone = '237' . $phone;

    }


    /*
    |--------------------------------------------------------------------------
    | Generate our own receipt number
    |--------------------------------------------------------------------------
    */

    $receiptNumber =
        'REC-' .
        now()->format('Ymd') .
        '-' .
        strtoupper(Str::random(8));


    /*
    |--------------------------------------------------------------------------
    | Create local payment first
    |--------------------------------------------------------------------------
    */

    $payment = Payment::create([

        'user_id' => auth()->id(),

        'deceased_id' => $request->deceased_id,

        'amount' => $request->amount,

        'balance' => $request->balance ?? 0,

        'payment_date' => $request->payment_date,

        'receipt_number' => $receiptNumber,

        'payment_method' => 'mobile_money',

        'mobile_operator' => $request->mobile_operator,

        'phone_number' => $phone,

        'status' => 'pending',

        'confirmed' => false,

    ]);


    /*
    |--------------------------------------------------------------------------
    | Send payment request to CamPay
    |--------------------------------------------------------------------------
    */

    try {

        $campayResponse = $campay->collect(

            (float) $request->amount,

            $phone,

            'Mortuary payment - ' . $receiptNumber,

            $receiptNumber

        );
        


        /*
        |--------------------------------------------------------------------------
        | Save CamPay reference
        |--------------------------------------------------------------------------
        */

        $payment->update([

            'campay_reference' =>
                $campayResponse['reference'] ?? null,

        ]);


        /*
        |--------------------------------------------------------------------------
        | Redirect user to payment waiting page
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'payments.processing',
                $payment->id
            )
            ->with(
                'success',
                'Payment request sent. Please confirm the transaction on your phone.'
            );


    } catch (\Throwable $e) {

    \Log::error('CamPay payment error', [
        'payment_id' => $payment->id ?? null,
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
    ]);

    $payment->update([
        'status' => 'failed',
    ]);

    return redirect()
        ->route('payments.index')
        ->with(
            'error',
            'The mobile money request could not be sent. Please try again.'
        );
}
}
public function processing(Payment $payment)
{
    $this->authorizeOwner($payment);

    return view(
        'payments.processing',
        compact('payment')
    );
}
    public function show(Payment $payment)
    {
        /*
        | Security:
        | A normal user cannot open another
        | user's receipt by changing the URL.
        */
        $this->authorizeViewer($payment);

        return view('payments.show', compact('payment'));
    }
    public function downloadReceipt(Payment $payment)
{
    $this->authorizeViewer($payment);

    $pdf = Pdf::loadView(
        'payments.receipt',
        compact('payment')
    );

    return $pdf->download(
        $payment->receipt_number . '.pdf'
    );
}


    /*
    |--------------------------------------------------------------------------
    | Admin confirms payment
    |--------------------------------------------------------------------------
    */
    public function confirm($id)
    {
        /*
        | Only administrators and staff managers can confirm
        */
        if (! auth()->user()->canSupervise()) {
            abort(403);
        }

        $payment = Payment::findOrFail($id);

        $payment->status = 'confirmed';

        $payment->confirmed = true;

        $payment->confirmed_at = now();

        $payment->save();

        return back()->with(
            'success',
            'Payment confirmed successfully.'
        );
    }
   public function checkStatus(
    Payment $payment,
    CamPayService $campay
) {
    $this->authorizeOwner($payment);

    if (!$payment->campay_reference) {
        return response()->json([
            'status' => 'FAILED',
            'message' => 'No CamPay reference found.',
        ], 400);
    }

    try {

        $result = $campay->status(
            $payment->campay_reference
        );

        $campayStatus = strtoupper(
            trim($result['status'] ?? 'PENDING')
        );

        if ($campayStatus === 'SUCCESSFUL') {

            $payment->update([
                'status' => 'successful',
                'confirmed' => true,
                'confirmed_at' => now(),
                'campay_status' => $campayStatus,
                'campay_operator_reference' =>
                    $result['operator_reference'] ?? null,
            ]);

            return response()->json([
                'status' => 'SUCCESSFUL',
                'message' => 'Payment successful.',
                'receipt_url' => route(
                    'payments.receipt',
                    $payment->id
                ),
            ]);
        }

        if ($campayStatus === 'FAILED') {

            $payment->update([
                'status' => 'failed',
                'confirmed' => false,
                'campay_status' => $campayStatus,
            ]);

            return response()->json([
                'status' => 'FAILED',
                'message' => 'Payment failed.',
            ]);
        }

        $payment->update([
            'campay_status' => $campayStatus,
        ]);

        return response()->json([
            'status' => 'PENDING',
            'campay_status' => $campayStatus,
        ]);

    } catch (\Throwable $e) {

    \Log::error('CamPay payment error', [
        'payment_id' => $payment->id,
        'error' => $e->getMessage(),
    ]);

    return response()->json([
        'status' => 'ERROR',
        'message' => 'Unable to check the payment status right now.',
    ], 500);
}
}
public function simulateSuccess(Payment $payment)
{
    $this->authorizeOwner($payment);

    if ($payment->status !== 'pending') {
        return back()->with(
            'error',
            'This payment is no longer pending.'
        );
    }

    $payment->update([
        'status' => 'successful',
        'confirmed' => true,
        'confirmed_at' => now(),
        'campay_status' => 'SUCCESSFUL',
    ]);

    return redirect()
        ->route('payments.index')
        ->with(
            'success',
            'Payment successful! Your receipt is now available.'
        );
}

    private function authorizeOwner(Payment $payment): void
    {
        abort_unless(
            (int) $payment->user_id === (int) auth()->id(),
            403
        );
    }

    private function authorizeViewer(Payment $payment): void
    {
        abort_unless(
            auth()->user()->canSupervise()
                || (int) $payment->user_id === (int) auth()->id(),
            403
        );
    }
}