<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            [
                'name' => 'Dhaka University',
                'latitude' => 23.7279,
                'longitude' => 90.3981,
                'address' => 'Dhaka University, Dhaka 1000, Bangladesh',
                'type' => 'saved',
            ],
            [
                'name' => 'National Parliament House',
                'latitude' => 23.7627,
                'longitude' => 90.3772,
                'address' => 'Sher-e-Bangla Nagar, Dhaka 1207, Bangladesh',
                'type' => 'saved',
            ],
            [
                'name' => 'Lalbagh Fort',
                'latitude' => 23.7186,
                'longitude' => 90.3854,
                'address' => 'Lalbagh Road, Dhaka 1211, Bangladesh',
                'type' => 'saved',
            ],
            [
                'name' => 'Ahsan Manzil',
                'latitude' => 23.7085,
                'longitude' => 90.4067,
                'address' => 'Kumartoli, Dhaka 1100, Bangladesh',
                'type' => 'saved',
            ],
            [
                'name' => 'Sadarghat Launch Terminal',
                'latitude' => 23.7045,
                'longitude' => 90.4113,
                'address' => 'Sadarghat Road, Dhaka 1100, Bangladesh',
                'type' => 'saved',
            ],
            [
                'name' => 'Dhanmondi Lake',
                'latitude' => 23.7461,
                'longitude' => 90.3742,
                'address' => 'Dhanmondi, Dhaka 1205, Bangladesh',
                'type' => 'saved',
            ],
            [
                'name' => 'Gulshan Lake Park',
                'latitude' => 23.7925,
                'longitude' => 90.4078,
                'address' => 'Gulshan 1, Dhaka 1212, Bangladesh',
                'type' => 'saved',
            ],
            [
                'name' => 'Ramna Park',
                'latitude' => 23.7387,
                'longitude' => 90.3958,
                'address' => 'Ramna, Dhaka 1000, Bangladesh',
                'type' => 'saved',
            ],
            [
                'name' => 'Hatirjheel',
                'latitude' => 23.7516,
                'longitude' => 90.4050,
                'address' => 'Hatirjheel, Dhaka, Bangladesh',
                'type' => 'saved',
            ],
            [
                'name' => 'Bashundhara City Shopping Complex',
                'latitude' => 23.7501,
                'longitude' => 90.3872,
                'address' => 'Panthapath, Dhaka 1205, Bangladesh',
                'type' => 'saved',
            ],
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }
    }
}
