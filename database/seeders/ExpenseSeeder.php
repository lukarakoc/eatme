<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\ExpenseType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ExpenseType::insert([[
            'id' => 1,
            'name' => 'Dnevnica'
        ],
        [
            'id' => 2,
            'name' => 'Gorivo'
        ]]);

        Expense::insert([[
            'notes' => 'Luka Rakocevic',
            'amount' => 20,
            'expense_type_id' => 1,
            'created_by' => 1,
            'created_at' => now()
        ],
        [
            'notes' => 'Put za Berane',
            'amount' => 25,
            'expense_type_id' => 2,
            'created_by' => 1,
            'created_at' => now()
        ]]);
    }
}
