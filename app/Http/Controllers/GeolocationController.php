<?php

namespace App\Http\Controllers;

use App\Models\Mortuary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeolocationController extends Controller
{
    public function index()
    {
        $mortuaries = Mortuary::query()
            ->where('is_active', true)
            ->orderBy('city')
            ->orderBy('name')
            ->get()
            ->map(fn (Mortuary $mortuary) => $this->formatMortuary($mortuary))
            ->values();

        return view('geolocation.index', [
            'allMortuaries' => $mortuaries,
            'nearbyMortuaries' => collect(),
            'userLocation' => null,
            'displayName' => null,
            'searchMode' => null,
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'location' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'radius_km' => ['nullable', 'numeric', 'min:1', 'max:200'],
        ]);

        $radiusKm = (float) ($request->input('radius_km') ?: 40);
        $userLatitude = null;
        $userLongitude = null;
        $displayName = null;
        $searchMode = null;

        if ($request->filled('latitude') && $request->filled('longitude')) {
            $userLatitude = (float) $request->input('latitude');
            $userLongitude = (float) $request->input('longitude');
            $searchMode = 'gps';

            $reverse = $this->reverseGeocode($userLatitude, $userLongitude);
            $displayName = $reverse['display_name']
                ?? sprintf('GPS %.5f, %.5f', $userLatitude, $userLongitude);
        } elseif ($request->filled('location')) {
            $searchMode = 'address';
            $geocoded = $this->geocodeAddress((string) $request->input('location'));

            if (!$geocoded) {
                return back()
                    ->withInput()
                    ->with('error', 'We could not find that location. Try a city and quarter in Cameroon, e.g. “Odza, Yaoundé”.');
            }

            $userLatitude = $geocoded['latitude'];
            $userLongitude = $geocoded['longitude'];
            $displayName = $geocoded['display_name'];
        } else {
            return back()->with('error', 'Enter an address or use your current GPS location.');
        }

        $nearbyMortuaries = Mortuary::query()
            ->where('is_active', true)
            ->get()
            ->map(function (Mortuary $mortuary) use ($userLatitude, $userLongitude) {
                $distance = $this->calculateDistance(
                    $userLatitude,
                    $userLongitude,
                    (float) $mortuary->latitude,
                    (float) $mortuary->longitude
                );

                return $this->formatMortuary($mortuary, $distance);
            })
            ->filter(fn (array $item) => $item['distance'] <= $radiusKm)
            ->sortBy('distance')
            ->values();

        $allMortuaries = Mortuary::query()
            ->where('is_active', true)
            ->orderBy('city')
            ->orderBy('name')
            ->get()
            ->map(fn (Mortuary $mortuary) => $this->formatMortuary($mortuary))
            ->values();

        return view('geolocation.index', [
            'allMortuaries' => $allMortuaries,
            'nearbyMortuaries' => $nearbyMortuaries,
            'userLocation' => [
                'latitude' => $userLatitude,
                'longitude' => $userLongitude,
                'label' => $displayName,
            ],
            'displayName' => $displayName,
            'searchMode' => $searchMode,
            'radiusKm' => $radiusKm,
            'location' => $request->input('location'),
        ]);
    }

    /**
     * @return array{latitude:float,longitude:float,display_name:string}|null
     */
    private function geocodeAddress(string $query): ?array
    {
        try {
            $response = Http::timeout(20)
                ->withHeaders([
                    'User-Agent' => 'MortuaryManagement/1.0 (contact: admin@mortuary.local)',
                    'Accept-Language' => 'fr,en',
                ])
                ->get('https://nominatim.openstreetmap.org/search', [
                    'q' => $query . ', Cameroon',
                    'format' => 'json',
                    'addressdetails' => 1,
                    'limit' => 1,
                    'countrycodes' => 'cm',
                ]);

            if (!$response->successful() || empty($response->json())) {
                return null;
            }

            $place = $response->json()[0];

            return [
                'latitude' => (float) $place['lat'],
                'longitude' => (float) $place['lon'],
                'display_name' => (string) ($place['display_name'] ?? $query),
            ];
        } catch (\Throwable $e) {
            Log::warning('Nominatim geocode failed', ['error' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * @return array{display_name?:string}
     */
    private function reverseGeocode(float $latitude, float $longitude): array
    {
        try {
            $response = Http::timeout(20)
                ->withHeaders([
                    'User-Agent' => 'MortuaryManagement/1.0 (contact: admin@mortuary.local)',
                    'Accept-Language' => 'fr,en',
                ])
                ->get('https://nominatim.openstreetmap.org/reverse', [
                    'lat' => $latitude,
                    'lon' => $longitude,
                    'format' => 'json',
                    'addressdetails' => 1,
                ]);

            if (!$response->successful()) {
                return [];
            }

            return [
                'display_name' => (string) ($response->json('display_name') ?? ''),
            ];
        } catch (\Throwable $e) {
            Log::warning('Nominatim reverse geocode failed', ['error' => $e->getMessage()]);

            return [];
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function formatMortuary(Mortuary $mortuary, ?float $distance = null): array
    {
        return [
            'id' => $mortuary->id,
            'name' => $mortuary->name,
            'city' => $mortuary->city,
            'quarter' => $mortuary->quarter,
            'address' => $mortuary->address,
            'description' => $mortuary->description,
            'phone' => $mortuary->phone,
            'latitude' => (float) $mortuary->latitude,
            'longitude' => (float) $mortuary->longitude,
            'distance' => $distance !== null ? round($distance, 1) : null,
            'maps_url' => sprintf(
                'https://www.openstreetmap.org/directions?from=&to=%.6f%%2C%.6f',
                (float) $mortuary->latitude,
                (float) $mortuary->longitude
            ),
            'google_maps_url' => sprintf(
                'https://www.google.com/maps/dir/?api=1&destination=%.6f,%.6f',
                (float) $mortuary->latitude,
                (float) $mortuary->longitude
            ),
        ];
    }

    private function calculateDistance(
        float $latitude1,
        float $longitude1,
        float $latitude2,
        float $longitude2
    ): float {
        $earthRadius = 6371;

        $latitudeDifference = deg2rad($latitude2 - $latitude1);
        $longitudeDifference = deg2rad($longitude2 - $longitude1);

        $a = sin($latitudeDifference / 2) ** 2
            + cos(deg2rad($latitude1))
            * cos(deg2rad($latitude2))
            * sin($longitudeDifference / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
