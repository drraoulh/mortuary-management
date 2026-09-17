<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class HuggingFaceService
{
    public function isConfigured(): bool
    {
        return filled(config('services.huggingface.token'));
    }

    /**
     * Chat completion via Hugging Face Inference Providers (OpenAI-compatible).
     *
     * @param  array<int, array{role:string,content:mixed}>  $messages
     */
    public function chat(array $messages, int $maxTokens = 900, float $temperature = 0.35): string
    {
        if (!$this->isConfigured()) {
            throw new RuntimeException('HF_TOKEN is not configured.');
        }

        $model = (string) config(
            'services.huggingface.model',
            'Qwen/Qwen2.5-7B-Instruct:fastest'
        );

        $baseUrl = rtrim(
            (string) config(
                'services.huggingface.base_url',
                'https://router.huggingface.co/v1'
            ),
            '/'
        );

        $response = Http::timeout(120)
            ->acceptJson()
            ->withToken((string) config('services.huggingface.token'))
            ->post($baseUrl . '/chat/completions', [
                'model' => $model,
                'messages' => $messages,
                'max_tokens' => $maxTokens,
                'temperature' => $temperature,
                'stream' => false,
            ]);

        if ($response->failed()) {
            Log::warning('Hugging Face chat failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            $detail = $response->json('error.message')
                ?? $response->json('error')
                ?? $response->body();

            if (is_array($detail)) {
                $detail = json_encode($detail);
            }

            throw new RuntimeException(
                'Hugging Face request failed: ' . $detail
            );
        }

        $content = data_get($response->json(), 'choices.0.message.content');

        if (!is_string($content) || trim($content) === '') {
            throw new RuntimeException('Hugging Face returned an empty response.');
        }

        return trim($content);
    }

    /**
     * Build multimodal user content with an optional image.
     *
     * @return array<int, array<string, mixed>>|string
     */
    public function buildUserContent(string $text, ?UploadedFile $image = null): array|string
    {
        if (!$image) {
            return $text;
        }

        $mime = $image->getMimeType() ?: 'image/jpeg';
        $data = base64_encode((string) file_get_contents($image->getRealPath()));

        return [
            [
                'type' => 'text',
                'text' => $text . "\n\nA photograph of the deceased is attached. Use it only as visual context for the announcement layout tone. Do not invent biographical facts from the image.",
            ],
            [
                'type' => 'image_url',
                'image_url' => [
                    'url' => 'data:' . $mime . ';base64,' . $data,
                ],
            ],
        ];
    }
}
