<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class CamPayService
{
    protected string $baseUrl;

   public function __construct()
{
    $this->baseUrl = rtrim(
        config('services.campay.base_url'),
        '/'
    );
}
    protected function getToken(): string
    {
        $response = Http::acceptJson()
            ->post($this->baseUrl . '/token/', [
                'username' => config('services.campay.username'),
                'password' => config('services.campay.password'),
            ]);

        if ($response->failed()) {
            throw new Exception(
                'Unable to authenticate with CamPay: ' .
                $response->body()
            );
        }

        return $response->json('token');
    }

    /**
     * Initiate a payment.
     *
     * When CAMPAY_SIMULATION=true, no real money is requested.
     * The payment is created as PENDING and can be simulated
     * as successful from the application.
     */
    public function collect(
    float $amount,
    string $phoneNumber,
    string $description,
    string $externalReference
): array {

    $token = $this->getToken();

    dd([
        'message' => 'CAMPAY TOKEN RECEIVED',
        'token_exists' => !empty($token),
        'token_length' => strlen($token),
    ]);

    $response = Http::withToken($token)
        ->acceptJson()
        ->post($this->baseUrl . '/api/collect/', [
            'amount' => $amount,
            'currency' => 'XAF',
            'from' => $phoneNumber,
            'description' => $description,
            'external_reference' => $externalReference,
        ]);

    dd([
        'status_code' => $response->status(),
        'successful' => $response->successful(),
        'body' => $response->body(),
        'json' => $response->json(),
    ]);

    if ($response->failed()) {
        throw new \Exception(
            'CamPay payment request failed: ' .
            $response->body()
        );
    }

    return $response->json();
}

    /**
     * Check transaction status.
     */
    public function status(string $reference): array
    {
        // ---------------------------------------------
        // SIMULATION MODE
        // ---------------------------------------------

        if (config('services.campay.simulation')) {

            return [
                'reference' => $reference,

                'status' => 'PENDING',

                'simulation' => true,
            ];
        }

        // ---------------------------------------------
        // REAL CAMPAY MODE
        // ---------------------------------------------

        $token = $this->getToken();

        $response = Http::withToken($token)
            ->acceptJson()
            ->get(
                $this->baseUrl .
                '/api/transaction/' .
                $reference .
                '/'
            );

        if ($response->failed()) {

            throw new Exception(
                'Unable to check CamPay transaction: ' .
                $response->body()
            );
        }

        return $response->json();
    }
}