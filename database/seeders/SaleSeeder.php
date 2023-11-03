<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Client::insert([
            [
                'id' => 1,
                'name' => 'Jugopetrol AD'
            ],
            [
                'id' => 2,
                'name' => 'Lukoil Montenegro'
            ],
            [
                'id' => 3,
                'name' => 'Duty free'
            ],
            [
                'id' => 4,
                'name' => 'Petrol'
            ],
            [
                'id' => 5,
                'name' => 'Hifa Oil'
            ],
            [
                'id' => 6,
                'name' => 'Voli'
            ]
        ]);

    }
}
