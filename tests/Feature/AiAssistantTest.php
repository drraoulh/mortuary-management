<?php

namespace Tests\Feature;

use App\Models\Deceased;
use App\Models\FuneralNotice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiAssistantTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_page_is_available(): void
    {
        $user = User::factory()->create(['role' => 'staff']);

        $this->actingAs($user)
            ->get(route('ai.index'))
            ->assertOk();
    }

    public function test_faire_part_uses_huggingface_when_available(): void
    {
        config([
            'services.huggingface.token' => 'hf_test_token',
            'services.huggingface.model' => 'Qwen/Qwen2.5-7B-Instruct:fastest',
            'services.huggingface.base_url' => 'https://router.huggingface.co/v1',
        ]);

        Http::fake([
            'https://router.huggingface.co/v1/chat/completions' => Http::response([
                'choices' => [[
                    'message' => [
                        'content' => 'Faire-part de test généré par Hugging Face.',
                    ],
                ]],
            ], 200),
        ]);

        $user = User::factory()->create(['role' => 'staff']);
        $deceased = Deceased::create([
            'user_id' => $user->id,
            'full_name' => 'Jean Dupont',
            'gender' => 'male',
            'date_of_death' => now()->toDateString(),
            'admission_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($user)->post(route('ai.generate'), [
            'deceased_id' => $deceased->id,
            'feature' => 'faire_part',
            'language' => 'fr',
            'save_notice' => 1,
        ]);

        $response->assertOk()
            ->assertSee('Faire-part de test généré par Hugging Face.')
            ->assertSee('Hugging Face');

        $this->assertSame(1, FuneralNotice::count());
    }

    public function test_ai_falls_back_locally_when_huggingface_fails(): void
    {
        config([
            'services.huggingface.token' => 'hf_test_token',
            'services.huggingface.base_url' => 'https://router.huggingface.co/v1',
        ]);

        Http::fake([
            'https://router.huggingface.co/v1/chat/completions' => Http::response([
                'error' => 'This authentication method does not have sufficient permissions',
            ], 403),
        ]);

        $user = User::factory()->create(['role' => 'staff']);
        $deceased = Deceased::create([
            'user_id' => $user->id,
            'full_name' => 'Marie Ngo',
            'gender' => 'female',
            'date_of_death' => now()->toDateString(),
            'admission_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($user)->post(route('ai.generate'), [
            'deceased_id' => $deceased->id,
            'feature' => 'condolences',
            'language' => 'fr',
        ]);

        $response->assertOk()
            ->assertSee('Marie Ngo')
            ->assertSee('Générateur local');
    }
}
