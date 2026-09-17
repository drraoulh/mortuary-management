<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Deceased;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use App\Services\CamPayService;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::where('user_id', auth()->id())
            ->with('deceased')
            ->latest()
            ->get();

        return view('payments.index', compact('payments'));
    }

    public function create(CamPayService $campay)
    {
        $deceaseds = Deceased::orderBy('full_name')->get();

        return view('payments.create', [
            'deceaseds' => $deceaseds,
            'isDemo' => $campay->isDemo(),
            'maxAmount' => $campay->maxAmount(),
        ]);
    }

    public function store(Request $request, CamPayService $campay)
    {
        $request->validate([
            'deceased_id' => 'required|exists:deceaseds,id',
            'amount' => ['required', 'numeric', 'min:1'],
            'balance' => 'nullable|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:mobile_money',
            'mobile_operator' => 'required|in:MTN,ORANGE',
            'phone_number' => [
                'required',
                'regex:/^(?:237)?6[5-9][0-9]{7}$/',
            ],
        ]);

        $phone = preg_replace('/\D/', '', (string) $request->phone_number);

        if (strlen($phone) === 9) {
            $phone = '237' . $phone;
        }

        $receiptNumber =
            'REC-' .
            now()->format('Ymd') .
            '-' .
            strtoupper(Str::random(8));

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

        try {
            $campayResponse = $campay->collect(
                (float) $request->amount,
                $phone,
                'Mortuary payment - ' . $receiptNumber,
                $receiptNumber
            );

            $payment->update([
                'campay_reference' => $campayResponse['reference'] ?? null,
                'campay_status' => strtoupper((string) ($campayResponse['status'] ?? 'PENDING')),
            ]);

            return redirect()
                ->route('payments.processing', $payment->id)
                ->with('success', 'Payment request sent. Confirm on your phone.')
                ->with('ussd_code', $campayResponse['ussd_code'] ?? null)
                ->with('campay_operator', $campayResponse['operator'] ?? $request->mobile_operator);
        } catch (\Throwable $e) {
            Log::error('CamPay payment error', [
                'payment_id' => $payment->id ?? null,
                'error' => $e->getMessage(),
            ]);

            try {
                $payment->forceFill([
                    'status' => 'failed',
                    'campay_status' => 'FAILED',
                ])->save();
            } catch (\Throwable $updateError) {
                // Older MySQL ENUM schemas may reject "failed".
                // Keep the original CamPay error visible to the user.
                Log::warning('Unable to mark payment as failed', [
                    'payment_id' => $payment->id ?? null,
                    'error' => $updateError->getMessage(),
                ]);
            }

            return redirect()
                ->route('payments.create')
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function processing(Payment $payment)
    {
        abort_unless($payment->user_id === auth()->id(), 403);

        return view('payments.processing', compact('payment'));
    }

    public function show(Payment $payment)
    {
        if (
            auth()->user()->role !== 'admin' &&
            $payment->user_id !== auth()->id()
        ) {
            abort(403);
        }

        return view('payments.show', compact('payment'));
    }

    public function downloadReceipt(Payment $payment)
    {
        if (
            auth()->user()->role !== 'admin' &&
            $payment->user_id !== auth()->id()
        ) {
            abort(403);
        }

        $pdf = Pdf::loadView('payments.receipt', compact('payment'));

        return $pdf->download($payment->receipt_number . '.pdf');
    }

    public function confirm($id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $payment = Payment::findOrFail($id);
        $payment->status = 'confirmed';
        $payment->confirmed = true;
        $payment->confirmed_at = now();
        $payment->save();

        return back()->with('success', 'Payment confirmed successfully.');
    }

    public function checkStatus(Payment $payment, CamPayService $campay)
    {
        abort_unless($payment->user_id === auth()->id(), 403);

        if (!$payment->campay_reference) {
            return response()->json([
                'status' => 'FAILED',
                'message' => 'No CamPay reference found.',
            ], 400);
        }

        try {
            $result = $campay->status($payment->campay_reference);
            $campayStatus = strtoupper(trim((string) ($result['status'] ?? 'PENDING')));

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
                    'receipt_url' => route('payments.show', $payment->id),
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
                'ussd_code' => $result['ussd_code'] ?? null,
            ]);
        } catch (\Throwable $e) {
            Log::error('CamPay status check error', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'ERROR',
                'message' => 'Unable to check payment status right now.',
            ], 500);
        }
    }

    /**
     * CamPay server-to-server webhook callback.
     */
    public function webhook(Request $request)
    {
        $configuredKey = (string) config('services.campay.webhook_key');
        $providedKey = (string) (
            $request->header('X-Campay-Webhook-Key')
            ?? $request->input('webhook_key')
            ?? $request->query('key')
            ?? ''
        );

        if ($configuredKey !== '' && !hash_equals($configuredKey, $providedKey)) {
            Log::warning('CamPay webhook rejected: invalid key');
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $reference = $request->input('reference')
            ?? $request->input('external_reference');

        if (!$reference) {
            return response()->json(['message' => 'Missing reference'], 422);
        }

        $payment = Payment::where('campay_reference', $reference)
            ->orWhere('receipt_number', $reference)
            ->first();

        if (!$payment) {
            Log::warning('CamPay webhook: payment not found', [
                'reference' => $reference,
            ]);

            return response()->json(['message' => 'Payment not found'], 404);
        }

        $status = strtoupper((string) (
            $request->input('status')
            ?? $request->input('transaction_status')
            ?? 'PENDING'
        ));

        if ($status === 'SUCCESSFUL') {
            $payment->update([
                'status' => 'successful',
                'confirmed' => true,
                'confirmed_at' => now(),
                'campay_status' => 'SUCCESSFUL',
                'campay_operator_reference' =>
                    $request->input('operator_reference')
                    ?? $payment->campay_operator_reference,
            ]);
        } elseif ($status === 'FAILED') {
            $payment->update([
                'status' => 'failed',
                'confirmed' => false,
                'campay_status' => 'FAILED',
            ]);
        } else {
            $payment->update([
                'campay_status' => $status,
            ]);
        }

        return response()->json(['message' => 'ok']);
    }

    public function simulateSuccess(Payment $payment)
    {
        abort_unless($payment->user_id === auth()->id(), 403);

        if (!config('services.campay.simulation') && !config('app.debug')) {
            abort(403, 'Simulation is disabled.');
        }

        if ($payment->status !== 'pending') {
            return back()->with('error', 'This payment is no longer pending.');
        }

        $payment->update([
            'status' => 'successful',
            'confirmed' => true,
            'confirmed_at' => now(),
            'campay_status' => 'SUCCESSFUL',
            'campay_reference' => $payment->campay_reference
                ?: ('SIM-' . strtoupper(Str::random(8))),
        ]);

        return redirect()
            ->route('payments.index')
            ->with('success', 'Payment successful! Your receipt is now available.');
    }
}
