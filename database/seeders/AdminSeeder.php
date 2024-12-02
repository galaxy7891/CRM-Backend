<?php

namespace Database\Seeders;

use App\Models\Admin;
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
        Admin::create([
            'id' => '123a4567-a89c-12e1-k256-226824173900',
            'email' => 'admin@gmail.com',
            'first_name' => 'Admin',
            'last_name' => 'Loyalcust',
            'password' => Hash::make('password123'),
            'phone' => '628731223412',
            'image_url' => null,
            'image_public_id' => null,
        ]);
    }
}
