<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class CamPayService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(
            (string) config('services.campay.base_url', 'https://demo.campay.net'),
            '/'
        );
    }

    public function isDemo(): bool
    {
        return (bool) config('services.campay.use_demo', true);
    }

    public function maxAmount(): ?float
    {
        return $this->isDemo() ? 25.0 : null;
    }

    /**
     * Resolve an access token: permanent APP token first, then username/password.
     */
    protected function getToken(): string
    {
        $permanent = trim((string) config('services.campay.token'));

        if ($permanent !== '') {
            return $permanent;
        }

        $cacheKey = 'campay_access_token_' . md5($this->baseUrl . config('services.campay.username'));

        return Cache::remember($cacheKey, now()->addMinutes(50), function () {
            $username = config('services.campay.username');
            $password = config('services.campay.password');

            if (!$username || !$password) {
                throw new Exception(
                    'CamPay credentials are missing. Set CAMPAY_TOKEN or CAMPAY_USERNAME/CAMPAY_PASSWORD.'
                );
            }

            $response = Http::acceptJson()
                ->asJson()
                ->timeout(30)
                ->post($this->baseUrl . '/api/token/', [
                    'username' => $username,
                    'password' => $password,
                ]);

            if ($response->failed()) {
                throw new Exception(
                    'Unable to authenticate with CamPay: ' . $response->body()
                );
            }

            $token = (string) $response->json('token');

            if ($token === '') {
                throw new Exception('CamPay returned an empty access token.');
            }

            return $token;
        });
    }

    protected function client()
    {
        // CamPay requires "Authorization: Token <jwt>", not Bearer.
        return Http::acceptJson()
            ->asJson()
            ->timeout(60)
            ->withHeaders([
                'Authorization' => 'Token ' . $this->getToken(),
            ]);
    }

    /**
     * Initiate a mobile-money collection request.
     *
     * @return array{reference:string,status?:string,ussd_code?:string,operator?:string}
     */
    public function collect(
        float $amount,
        string $phoneNumber,
        string $description,
        string $externalReference
    ): array {
        if (config('services.campay.simulation')) {
            return [
                'reference' => 'SIM-' . strtoupper(bin2hex(random_bytes(4))),
                'status' => 'PENDING',
                'simulation' => true,
                'ussd_code' => '*126#',
                'operator' => 'MTN',
                'amount' => $amount,
                'from' => $phoneNumber,
                'description' => $description,
                'external_reference' => $externalReference,
            ];
        }

        $max = $this->maxAmount();
        if ($max !== null && $amount > $max) {
            throw new Exception(
                "CamPay demo maximum amount is {$max} XAF. Use a smaller amount while CAMPAY_USE_DEMO=true."
            );
        }

        $amountValue = (string) (int) round($amount);

        if ((int) $amountValue < 1) {
            throw new Exception('CamPay amount must be at least 1 XAF.');
        }

        $response = $this->client()->post($this->baseUrl . '/api/collect/', [
            'amount' => $amountValue,
            'currency' => 'XAF',
            'from' => $phoneNumber,
            'description' => $description,
            'external_reference' => $externalReference,
        ]);

        if ($response->failed()) {
            Log::error('CamPay collect failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            $message = $response->json('message')
                ?? $response->json('detail')
                ?? $response->body();

            throw new Exception('CamPay payment request failed: ' . $message);
        }

        $data = $response->json() ?? [];

        if (empty($data['reference'])) {
            throw new Exception('CamPay did not return a transaction reference.');
        }

        return $data;
    }

    /**
     * Check transaction status by CamPay reference.
     */
    public function status(string $reference): array
    {
        if (config('services.campay.simulation')) {
            return [
                'reference' => $reference,
                'status' => 'PENDING',
                'simulation' => true,
            ];
        }

        $response = $this->client()->get(
            $this->baseUrl . '/api/transaction/' . $reference . '/'
        );

        if ($response->failed()) {
            Log::error('CamPay status check failed', [
                'reference' => $reference,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new Exception(
                'Unable to check CamPay transaction: ' . $response->body()
            );
        }

        return $response->json() ?? [];
    }
}
