<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Sale;
use App\Models\SaleGrocery;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Client::create([
            'id' => 1,
            'name' => 'Jugopetrol'
        ]);

        Sale::create([
            'total' => 10,
            'created_at' => now(),
            'created_by' => 1,
            'client_id' => 1
        ]);

        SaleGrocery::create([
            'sale_id' => 1,
            'grocery_id' => 1,
            'quantity' => 10,
            'total' => 100,
            'unit_price' => 10
        ]);
    }
}
