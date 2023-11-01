<?php

namespace Database\Seeders;

use App\Models\Grocery;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GrocerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Grocery::insert([[
            'name' => 'Namirnica',
            'unit_price' => 10,
            'is_product' => false,
            'created_by' => 1,
            'created_at' => now()
        ],
            [
                'name' => 'Test proizvod',
                'unit_price' => 20,
                'is_product' => true,
                'created_by' => 1,
                'created_at' => now()
            ]
        ]);
    }
}
