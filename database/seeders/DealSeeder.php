<?php

namespace Database\Seeders;

use App\Models\Deal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DealSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Deal::create([
            'id' => '123e4567-e89b-12d3-a456-426614174000',
            'customer_id' => '123e4567-e89b-12d3-a456-426614174001',
            'name' => 'Penjualan Alat Tulis Kantor',
            'deals_customer' => '123e4567-e89b-12d3-a456-426614174001',
            'description' => 'Lorem ipsum dolor sit amet.',
            'tag' => 'Alat Tulis',
            'status' => 'warm',
            'stage' => 'negotiate',
            'open_date' => '2022-01-01',
            'close_date' => '2022-01-31',
            'expected_close_date' => '2022-01-31',
            'payment_expected' => 1000000,
            'payment_category' => 'once',
            'payment_duration' => 1,
            'owner' => 'user_satu@gmail.com',
        ]);
    }
}
