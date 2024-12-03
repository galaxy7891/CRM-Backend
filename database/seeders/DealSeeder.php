<?php

namespace Database\Seeders;

use App\Models\Deal;
use App\Models\DealsProduct;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DealSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Deal::create([
            'id' => '123e4567-e89b-12d7-a452-42661413299',
            'category' => 'customers',
            'customer_id' => '123e4567-e89b-12d3-a456-426614174001',
            'name' => 'Penjualan Alat Tulis Kantor',
            'description' => 'Lorem ipsum dolor sit amet.',
            'tag' => 'Alat Tulis',
            'status' => 'warm',
            'stage' => 'qualificated',
            'open_date' => '2022-01-01',
            'expected_close_date' => '2022-01-31',
            'value_estimated' => 2000000,
            'payment_category' => 'once',
            'owner' => 'user_satu@gmail.com',
        ]);

        Deal::create([
            'id' => '123e4567-e89b-12d7-a452-42661413300',
            'category' => 'customers',
            'customer_id' => '123e4567-e89b-12d3-a456-426614174001',
            'name' => 'Penjualan Alat Tulis Kantor',
            'description' => 'Lorem ipsum dolor sit amet.',
            'tag' => 'Alat Tulis',
            'status' => 'warm',
            'stage' => 'negotiate',
            'open_date' => '2022-01-01',
            'expected_close_date' => '2022-01-31',
            'value_estimated' => 2000000,
            'payment_category' => 'once',
            'owner' => 'user_satu@gmail.com',
        ]);

        Deal::create([
            'id' => '123e4567-e89b-12d7-a452-42661413100',
            'category' => 'customers',
            'customer_id' => '123e4567-e89b-12d3-a456-426614174001',
            'name' => 'Penjualan Alat Tulis Sekolah',
            'description' => 'Lorem ipsum dolor sit amet.',
            'tag' => 'Alat Tulis',
            'status' => 'warm',
            'stage' => 'negotiate',
            'open_date' => '2022-01-01',
            'expected_close_date' => '2022-01-31',
            'value_estimated' => 2000000,
            'payment_category' => 'once',
            'owner' => 'user_satu@gmail.com',
        ]);

        Deal::create([
            'id' => '123e4567-e89b-12d7-a452-42661413188',
            'category' => 'customers',
            'customer_id' => '123e4567-e89b-12d3-a456-426614174001',
            'name' => 'Penjualan Alat Tukang',
            'description' => 'Lorem ipsum dolor sit amet.',
            'tag' => 'Alat Tulis',
            'status' => 'warm',
            'stage' => 'negotiate',
            'open_date' => '2022-01-01',
            'expected_close_date' => '2022-01-31',
            'value_estimated' => 2000000,
            'payment_category' => 'once',
            'owner' => 'user_satu@gmail.com',
        ]);
        Deal::create([
            'id' => '123e4567-e89b-12d7-a452-42661413177',
            'category' => 'customers',
            'customer_id' => '123e4567-e89b-12d3-a456-426614174001',
            'name' => 'Penjualan Alat Mandor',
            'description' => 'Lorem ipsum dolor sit amet.',
            'tag' => 'Alat Tulis',
            'status' => 'warm',
            'stage' => 'negotiate',
            'open_date' => '2022-01-01',
            'expected_close_date' => '2022-01-31',
            'value_estimated' => 2000000,
            'payment_category' => 'once',
            'owner' => 'user_satu@gmail.com',
        ]);

        Deal::create([
            'id' => '123e4567-e89b-12d7-a452-42661413355',
            'category' => 'customers',
            'customer_id' => '123e4567-e89b-12d3-a456-426614174001',
            'name' => 'Penjualan Alat Tulis Tercapai',
            'description' => 'Lorem ipsum dolor sit amet.',
            'tag' => 'Alat Tulis',
            'status' => 'warm',
            'stage' => 'won',
            'open_date' => '2022-01-01',
            'expected_close_date' => '2022-01-31',
            'value_estimated' => 2000000,
            'payment_category' => 'once',
            'owner' => 'user_satu@gmail.com',
        ]);

        Deal::create([
            'id' => '123e4567-e89b-12d7-a452-42661413366',
            'category' => 'customers',
            'customer_id' => '123e4567-e89b-12d3-a456-426614174001',
            'name' => 'Penjualan Alat Tulis Gagal',
            'description' => 'Lorem ipsum dolor sit amet.',
            'tag' => 'Alat Tulis',
            'status' => 'warm',
            'stage' => 'lose',
            'open_date' => '2022-01-01',
            'expected_close_date' => '2022-01-31',
            'value_estimated' => 2000000,
            'payment_category' => 'once',
            'owner' => 'user_satu@gmail.com',
        ]);
        }
}
