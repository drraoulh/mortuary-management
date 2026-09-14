<?php

namespace Database\Seeders;

use App\Models\Mortuary;
use Illuminate\Database\Seeder;

class MortuarySeeder extends Seeder
{
    public function run(): void
    {
        Mortuary::create([
            'name' => 'CEFTA Morgue',
            'city' => 'Yaoundé',
            'quarter' => 'Ekounou',
            'address' => 'Ekounou, Yaoundé',
            'latitude' => 3.7895000,
            'longitude' => 11.5410000,
            'description' => 'Mortuary facility associated with CEFTA in Ekounou.',
            'phone' => null,
            'is_active' => true,
        ]);

        Mortuary::create([
            'name' => 'Yaoundé General Hospital Morgue',
            'city' => 'Yaoundé',
            'quarter' => 'Centre',
            'address' => 'Yaoundé General Hospital, Yaoundé',
            'latitude' => 3.8737000,
            'longitude' => 11.5217000,
            'description' => 'Mortuary facility at Yaoundé General Hospital.',
            'phone' => null,
            'is_active' => true,
        ]);

        Mortuary::create([
            'name' => 'Mvan Mortuary',
            'city' => 'Yaoundé',
            'quarter' => 'Mvan',
            'address' => 'Mvan, Yaoundé',
            'latitude' => 3.8230000,
            'longitude' => 11.5200000,
            'description' => 'Demonstration mortuary facility serving the Mvan area.',
            'phone' => null,
            'is_active' => true,
        ]);

        Mortuary::create([
            'name' => 'Essos Mortuary',
            'city' => 'Yaoundé',
            'quarter' => 'Essos',
            'address' => 'Essos, Yaoundé',
            'latitude' => 3.8760000,
            'longitude' => 11.5480000,
            'description' => 'Demonstration mortuary facility serving the Essos area.',
            'phone' => null,
            'is_active' => true,
        ]);

        Mortuary::create([
            'name' => 'Douala General Hospital Morgue',
            'city' => 'Douala',
            'quarter' => 'Bonanjo',
            'address' => 'Douala General Hospital, Bonanjo',
            'latitude' => 4.0470000,
            'longitude' => 9.6890000,
            'description' => 'Mortuary facility at Douala General Hospital.',
            'phone' => null,
            'is_active' => true,
        ]);

        Mortuary::create([
            'name' => 'Deido Mortuary',
            'city' => 'Douala',
            'quarter' => 'Deido',
            'address' => 'Deido, Douala',
            'latitude' => 4.0650000,
            'longitude' => 9.7000000,
            'description' => 'Demonstration mortuary facility serving the Deido area.',
            'phone' => null,
            'is_active' => true,
        ]);

        Mortuary::create([
            'name' => 'Bonassama Mortuary',
            'city' => 'Douala',
            'quarter' => 'Bonabéri',
            'address' => 'Bonabéri, Douala',
            'latitude' => 4.0730000,
            'longitude' => 9.6460000,
            'description' => 'Demonstration mortuary facility serving Bonabéri.',
            'phone' => null,
            'is_active' => true,
        ]);
    }
}