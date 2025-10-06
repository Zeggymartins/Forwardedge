<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@forwardedge.com'], // Change to your preferred email
            [
                'name' => 'SystemAdmin',
                'password' => Hash::make('goodfood'), // Change this to a secure password
                'role' => 'admin', // Make sure you add a `role` column in `users` table
            ]
        );
    }
}