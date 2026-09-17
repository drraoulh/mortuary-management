<?php

namespace Database\Seeders;

use App\Models\Mortuary;
use Illuminate\Database\Seeder;

class MortuarySeeder extends Seeder
{
    public function run(): void
    {
        $mortuaries = [
            [
                'name' => 'CEFTA Morgue',
                'city' => 'Yaoundé',
                'quarter' => 'Ekounou',
                'address' => 'Ekounou, Yaoundé',
                'latitude' => 3.8459000,
                'longitude' => 11.5418000,
                'description' => 'Mortuary facility associated with CEFTA in Ekounou.',
                'phone' => '+237670000001',
            ],
            [
                'name' => 'Yaoundé General Hospital Morgue',
                'city' => 'Yaoundé',
                'quarter' => 'Ngousso',
                'address' => 'Hôpital Général de Yaoundé, Ngousso',
                'latitude' => 3.8756000,
                'longitude' => 11.5402000,
                'description' => 'Mortuary facility at Yaoundé General Hospital.',
                'phone' => '+237670000002',
            ],
            [
                'name' => 'Mvan Mortuary',
                'city' => 'Yaoundé',
                'quarter' => 'Mvan',
                'address' => 'Mvan, Yaoundé',
                'latitude' => 3.8234000,
                'longitude' => 11.5208000,
                'description' => 'Community mortuary facility serving the Mvan area.',
                'phone' => '+237670000003',
            ],
            [
                'name' => 'Essos Mortuary',
                'city' => 'Yaoundé',
                'quarter' => 'Essos',
                'address' => 'Essos, Yaoundé',
                'latitude' => 3.8768000,
                'longitude' => 11.5486000,
                'description' => 'Community mortuary facility serving the Essos area.',
                'phone' => '+237670000004',
            ],
            [
                'name' => 'Odza Community Mortuary',
                'city' => 'Yaoundé',
                'quarter' => 'Odza',
                'address' => 'Odza, Yaoundé IV',
                'latitude' => 3.7986000,
                'longitude' => 11.5291000,
                'description' => 'Mortuary access point near Odza.',
                'phone' => '+237670000005',
            ],
            [
                'name' => 'Douala General Hospital Morgue',
                'city' => 'Douala',
                'quarter' => 'Bonanjo',
                'address' => 'Hôpital Général de Douala, Bonanjo',
                'latitude' => 4.0412000,
                'longitude' => 9.7048000,
                'description' => 'Mortuary facility at Douala General Hospital.',
                'phone' => '+237670000006',
            ],
            [
                'name' => 'Deido Mortuary',
                'city' => 'Douala',
                'quarter' => 'Deido',
                'address' => 'Deido, Douala',
                'latitude' => 4.0658000,
                'longitude' => 9.7205000,
                'description' => 'Community mortuary facility serving the Deido area.',
                'phone' => '+237670000007',
            ],
            [
                'name' => 'Bonassama Mortuary',
                'city' => 'Douala',
                'quarter' => 'Bonabéri',
                'address' => 'Hôpital de Bonassama, Bonabéri',
                'latitude' => 4.0815000,
                'longitude' => 9.6662000,
                'description' => 'Community mortuary facility serving Bonabéri.',
                'phone' => '+237670000008',
            ],
        ];

        foreach ($mortuaries as $mortuary) {
            Mortuary::updateOrCreate(
                [
                    'name' => $mortuary['name'],
                    'city' => $mortuary['city'],
                ],
                array_merge($mortuary, ['is_active' => true])
            );
        }
    }
}
