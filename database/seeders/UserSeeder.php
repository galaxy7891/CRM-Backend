<?php

namespace Database\Seeders;

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
        User::create([
            'id' => '123e4567-e89b-12d3-a456-426614174003',
            'user_company_id' => '123e4567-e89b-12d3-a456-426614174000',
            'google_id' => null,
            'email' => 'user_satu@gmail.com',
            'first_name' => 'User',
            'last_name' => 'Satu',
            'password' => Hash::make('password123'),
            'phone' => '628731221412',
            'job_position' => null,
            'role' => 'employee',
            'gender' => 'male',
            'image_url' => null,
            'image_public_id' => null,
        ]);

        User::create([
            'id' => '123e4567-e89b-12d3-a456-426614174010',
            'user_company_id' => '123e4567-e89b-12d3-a456-426614174000',
            'google_id' => null,
            'email' => 'user_dua@gmail.com',
            'first_name' => 'User',
            'last_name' => 'Dua',
            'password' => Hash::make('password123'),
            'phone' => '628731223412',
            'job_position' => null,
            'role' => 'employee',
            'gender' => 'male',
            'image_url' => null,
            'image_public_id' => null,
        ]);

        User::create([
            'id' => '123e4567-e89b-12d3-a456-426614174100',
            'user_company_id' => '123e4567-e89b-12d3-a456-426614174000',
            'google_id' => null,
            'email' => 'raafi.sdmsib7@gmail.com',
            'first_name' => 'Raafi',
            'last_name' => 'Adzani',
            'password' => Hash::make('password123'),
            'phone' => '628731223414',
            'job_position' => null,
            'role' => 'super_admin',
            'gender' => 'male',
            'image_url' => null,
            'image_public_id' => null,
        ]);
        User::create([
            'id' => '123e4567-e89b-12d3-a456-426614174111',
            'user_company_id' => '123e4567-e89b-12d3-a456-426614174000',
            'google_id' => null,
            'email' => 'sufyan.uimsib7@gmail.com',
            'first_name' => 'Sufyan',
            'last_name' => 'Hanif',
            'password' => Hash::make('123'),
            'phone' => null,
            'job_position' => null,
            'role' => 'admin',
            'gender' => 'male',
            'image_url' => null,
            'image_public_id' => null,
        ]);
        User::create([
            'id' => '123e4567-e89b-12d3-a456-426614174122',
            'user_company_id' => '123e4567-e89b-12d3-a456-426614174000',
            'google_id' => null,
            'email' => 'arifa.uimsib7@gmail.com',
            'first_name' => 'Arifa',
            'last_name' => 'Mutia',
            'password' => Hash::make('123'),
            'phone' => null,
            'job_position' => null,
            'role' => 'admin',
            'gender' => 'male',
            'image_url' => null,
            'image_public_id' => null,
        ]);
        User::create([
            'id' => '123e4567-e89b-12d3-a456-426614174123',
            'user_company_id' => '123e4567-e89b-12d3-a456-426614174000',
            'google_id' => null,
            'email' => 'yudistira.uimsib7@gmail.com',
            'first_name' => 'Yudistira',
            'last_name' => 'Putra',
            'password' => Hash::make('123'),
            'phone' => null,
            'job_position' => null,
            'role' => 'admin',
            'gender' => 'male',
            'image_url' => null,
            'image_public_id' => null,
        ]);
        User::create([
            'id' => '123e4567-e89b-12d3-a456-426614174124',
            'user_company_id' => '123e4567-e89b-12d3-a456-426614174000',
            'google_id' => null,
            'email' => 'hernan.sdmsib7@gmail.com',
            'first_name' => 'Hernan',
            'last_name' => 'Sandi',
            'password' => Hash::make('123'),
            'phone' => null,
            'job_position' => null,
            'role' => 'admin',
            'gender' => 'male',
            'image_url' => null,
            'image_public_id' => null,
        ]);
        User::create([
            'id' => '123e4567-e89b-12d3-a456-426614174125',
            'user_company_id' => '123e4567-e89b-12d3-a456-426614174000',
            'google_id' => null,
            'email' => 'farina.sdmsib7@gmail.com',
            'first_name' => 'Farina',
            'last_name' => 'Naswa',
            'password' => Hash::make('123'),
            'phone' => null,
            'job_position' => null,
            'role' => 'admin',
            'gender' => 'male',
            'image_url' => null,
            'image_public_id' => null,
        ]);
    }
}
