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
            'id' => '123e4567-e89b-12d7-a452-42661413400',
            'category' => 'customers',
            'customer_id' => '123e4567-e89b-12d3-a456-426614174001',
            'name' => 'Penjualan Alat Tulis Kantor',
            'description' => 'Lorem ipsum dolor sit amet.',
            'tag' => 'Alat Tulis',
            'status' => 'warm',
            'stage' => 'negotiate',
            'open_date' => '2022-01-01',
            'expected_close_date' => '2022-01-31',
            'value_estimated' => 3000000,
            'payment_category' => 'once',
            'owner' => 'employee_cdi@gmail.com',
        ]);

        Deal::create([
            'id' => '123e4567-e89b-12d7-a452-42661413300',
            'category' => 'customers',
            'customer_id' => '123e4567-e89b-12d3-a456-426614174001',
            'name' => 'Penjualan Alat Tulis Kantor',
            'description' => 'Lorem ipsum dolor sit amet.',
            'tag' => 'Alat Tulis',
            'status' => 'warm',
            'stage' => 'lose',
            'open_date' => '2022-01-01',
            'expected_close_date' => '2022-01-31',
            'value_estimated' => 2000000,
            'payment_category' => 'once',
            'owner' => 'employee_cdi@gmail.com',
        ]);
        
        Deal::create([
            'id' => '123e4567-e89b-12d7-a452-42661413200',               
            'category' => 'customers',       
            'customer_id' => '123e4567-e89b-12d3-a456-426614174001',
            'name' => 'Penjualan Alat Tulis Kantor',
            'description' => 'Lorem ipsum dolor sit amet.',
            'tag' => 'Alat Tulis',
            'status' => 'warm',
            'stage' => 'won',
            'open_date' => '2022-01-01',
            'close_date' => '2022-01-31',
            'expected_close_date' => '2022-01-31',
            'value_estimated' => 1500000,
            'value_actual' => 1500000,
            'payment_category' => 'once',
            'owner' => 'employee_cdi@gmail.com',
        ]);
        
        Deal::create([
            'id' => '123e4567-e89b-12d7-a452-42661413100',
            'category' => 'customers_companies',
            'customers_company_id' => '123e4567-e89b-12d3-a456-426614174005',
            'name' => 'Penjualan Alat Tulis Kantor',
            'description' => 'Lorem ipsum dolor sit amet.',
            'tag' => 'Alat Tulis',
            'status' => 'warm',
            'stage' => 'negotiate',
            'open_date' => '2022-01-01',
            'expected_close_date' => '2022-01-31',
            'value_estimated' => 1000000,
            'payment_category' => 'once',
            'owner' => 'employee_cdi@gmail.com',
        ]);
    }
}
