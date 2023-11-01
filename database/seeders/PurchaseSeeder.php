<?php

namespace Database\Seeders;

use App\Models\Purchase;
use App\Models\PurchaseGrocery;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PurchaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Purchase::create([
            'total' => 10,
            'created_at' => now(),
            'created_by' => 1
        ]);

        PurchaseGrocery::create([
            'purchase_id' => 1,
            'grocery_id' => 1,
            'quantity' => 10,
            'total' => 100,
            'unit_price' => 10
        ]);
    }
}
