<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::insert([
            [
                'name' => 'Luka Rakocevic',
                'email' => 'rakocevic.luka@gmail.com',
                'password' => Hash::make('Rakoc211009'),
                'role_id' => Role::ADMIN
            ],
            [
                'name' => 'Eat.me - Matija',
                'email' => 'eatme@dmline.me',
                'password' => Hash::make("eatmesoftware"),
                'role_id' => Role::ADMIN
            ]
        ]);
    }
}
