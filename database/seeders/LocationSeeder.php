<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            'India' => [
                'type' => 'country',
                'iso_code' => 'IN',
                'states' => ['Maharashtra', 'Delhi', 'Karnataka', 'Tamil Nadu', 'Gujarat']
            ],
            'United States' => [
                'type' => 'country',
                'iso_code' => 'US',
                'states' => ['California', 'New York', 'Texas', 'Florida', 'Illinois']
            ],
            'United Kingdom' => [
                'type' => 'country',
                'iso_code' => 'GB',
                'states' => ['England', 'Scotland', 'Wales', 'Northern Ireland']
            ],
        ];

        foreach ($countries as $countryName => $countryData) {
            $country = Location::updateOrCreate(
                ['slug' => Str::slug($countryName)],
                [
                    'name' => $countryName,
                    'type' => $countryData['type'],
                    'iso_code' => $countryData['iso_code'],
                ]
            );

            foreach ($countryData['states'] as $stateName) {
                Location::updateOrCreate(
                    ['slug' => Str::slug($stateName)],
                    [
                        'name' => $stateName,
                        'type' => 'state',
                        'parent_id' => $country->id,
                    ]
                );
            }
        }
    }
}
