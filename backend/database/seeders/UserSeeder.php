<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([

            'id' => '123e4567-e89b-12d3-a456-426614174003',
            'company_id' => '123e4567-e89b-12d3-a456-426614174000',
            'google_id' => null,
            'email' => 'user_satu@gmail.com',
            'first_name' => 'User',
            'last_name' => 'Satu',
            'password' => Hash::make('password123'),
            'phone' => null,
            'job_position' => null,
            'role' => 'employee',
            'gender' => 'male',
            'photo' => null,
        ]);
    }
}
