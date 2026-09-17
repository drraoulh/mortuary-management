<?php

namespace Tests\Feature;

use App\Models\Mortuary;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeolocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_geolocation_page_loads_with_map_data(): void
    {
        $user = User::factory()->create();
        Mortuary::create([
            'name' => 'Test Morgue',
            'city' => 'Yaoundé',
            'quarter' => 'Odza',
            'address' => 'Odza',
            'latitude' => 3.7986,
            'longitude' => 11.5291,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get(route('geolocation.index'))
            ->assertOk()
            ->assertSee('Find nearby mortuaries')
            ->assertSee('Use my real location');
    }

    public function test_address_search_uses_nominatim_and_returns_nearby_results(): void
    {
        Http::fake([
            'nominatim.openstreetmap.org/search*' => Http::response([
                [
                    'lat' => '3.7985968',
                    'lon' => '11.5291189',
                    'display_name' => 'Odza, Yaoundé, Cameroun',
                ],
            ], 200),
        ]);

        $user = User::factory()->create();

        Mortuary::create([
            'name' => 'Odza Community Mortuary',
            'city' => 'Yaoundé',
            'quarter' => 'Odza',
            'address' => 'Odza, Yaoundé IV',
            'latitude' => 3.7986,
            'longitude' => 11.5291,
            'is_active' => true,
        ]);

        Mortuary::create([
            'name' => 'Far Away Morgue',
            'city' => 'Douala',
            'quarter' => 'Deido',
            'address' => 'Deido',
            'latitude' => 4.0658,
            'longitude' => 9.7205,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(route('geolocation.search'), [
            'location' => 'Odza, Yaoundé',
            'radius_km' => 20,
        ]);

        $response->assertOk()
            ->assertSee('Odza, Yaoundé, Cameroun')
            ->assertSee('Odza Community Mortuary')
            ->assertDontSee('Far Away Morgue');
    }

    public function test_gps_search_accepts_coordinates(): void
    {
        Http::fake([
            'nominatim.openstreetmap.org/reverse*' => Http::response([
                'display_name' => 'Ngoa-Ékélé, Yaoundé, Cameroun',
            ], 200),
        ]);

        $user = User::factory()->create();
        Mortuary::create([
            'name' => 'Central Morgue',
            'city' => 'Yaoundé',
            'quarter' => 'Centre',
            'address' => 'Centre',
            'latitude' => 3.8480,
            'longitude' => 11.5021,
            'is_active' => true,
        ]);

        $this->actingAs($user)->post(route('geolocation.search'), [
            'latitude' => 3.8480,
            'longitude' => 11.5021,
            'radius_km' => 30,
        ])->assertOk()
            ->assertSee('Ngoa-Ékélé, Yaoundé, Cameroun')
            ->assertSee('Central Morgue');
    }
}
