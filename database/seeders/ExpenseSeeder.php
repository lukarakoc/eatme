<?php

namespace Database\Seeders;

use App\Models\ExpenseType;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ExpenseType::insert([
            [
                'id' => 1,
                'name' => 'Dnevnica'
            ],
            [
                'id' => 2,
                'name' => 'Materijalni troškovi'
            ], [
                'id' => 3,
                'name' => 'Putni troškovi'
            ],
            [
                'id' => 4,
                'name' => 'Gorivo'
            ]
        ]);
    }
}
