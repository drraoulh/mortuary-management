<?php

namespace App\Http\Controllers;

use App\Models\Mortuary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GeolocationController extends Controller
{
    public function index()
    {
        return view('geolocation.index');
    }

    public function search(Request $request)
    {
        $request->validate([
            'location' => ['required', 'string', 'max:255'],
        ]);

        $location = $request->input('location');

        /*
        |--------------------------------------------------------------------------
        | Geocode the user's typed location
        |--------------------------------------------------------------------------
        | Example:
        | Yaoundé, Odza
        |
        | The user only enters a normal location name.
        | Latitude/longitude are handled internally.
        */

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'MortuaryManagement/1.0',
            ])->get('https://nominatim.openstreetmap.org/search', [
                'q' => $location . ', Cameroon',
                'format' => 'json',
                'limit' => 1,
            ]);

            if (!$response->successful() || empty($response->json())) {
                return back()
                    ->withInput()
                    ->with('error', 'We could not find that location. Please enter a valid city and quarter.');
            }

            $place = $response->json()[0];

            $userLatitude = (float) $place['lat'];
            $userLongitude = (float) $place['lon'];
            $displayName = $place['display_name'];

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Location search is temporarily unavailable. Please try again.');
        }

        /*
        |--------------------------------------------------------------------------
        | Determine the city
        |--------------------------------------------------------------------------
        */

        $city = null;

        if (stripos($location, 'yaound') !== false) {
            $city = 'Yaoundé';
        } elseif (stripos($location, 'douala') !== false) {
            $city = 'Douala';
        }

        if (!$city) {
            return back()
                ->withInput()
                ->with('error', 'Please search within Yaoundé or Douala.');
        }

        /*
        |--------------------------------------------------------------------------
        | Get active mortuaries for the selected city
        |--------------------------------------------------------------------------
        */

        $mortuaries = Mortuary::where('city', $city)
            ->where('is_active', true)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Calculate distance from the user's location
        |--------------------------------------------------------------------------
        */

        $nearbyMortuaries = $mortuaries->map(function ($mortuary) use ($userLatitude, $userLongitude) {

            $distance = $this->calculateDistance(
                $userLatitude,
                $userLongitude,
                $mortuary->latitude,
                $mortuary->longitude
            );

            return [
                'id' => $mortuary->id,
                'name' => $mortuary->name,
                'city' => $mortuary->city,
                'quarter' => $mortuary->quarter,
                'address' => $mortuary->address,
                'description' => $mortuary->description,
                'phone' => $mortuary->phone,
                'distance' => round($distance, 1),

                // Used internally by the map.
                // They are NOT displayed as raw coordinates.
                'latitude' => $mortuary->latitude,
                'longitude' => $mortuary->longitude,
            ];
        })
        ->sortBy('distance')
        ->values();

        return view('geolocation.index', [
            'location' => $location,
            'displayName' => $displayName,
            'nearbyMortuaries' => $nearbyMortuaries,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Calculate distance between two points
    |--------------------------------------------------------------------------
    */

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