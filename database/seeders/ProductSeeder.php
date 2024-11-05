<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'id' => '123e4567-e89b-12d3-a456-426614171004',
            'name' => 'Pensil',
            'company_id'=> '123e4567-e89b-12d3-a456-426614174000',
            'category' => 'stuff',
            'code' => 'PEN-001',
            'quantity' => 10,
            'unit' => 'pcs',
            'price' => 10000,
            'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit.',
        ]);
        Product::create([
            'id' => '123e4567-e89b-12d3-a456-426614172004',
            'name' => 'Pulpen',
            'company_id'=> '123e4567-e89b-12d3-a456-426614174000',
            'category' => 'stuff',
            'code' => 'BOL-001',
            'quantity' => 10,
            'unit' => 'pcs',
            'price' => 20000,
            'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit.',

        ]);
        Product::create([
            'id' => '123e4567-e89b-12d3-a456-426614173004',
            'name' => 'Bantal',
            'company_id'=> '123e4567-e89b-12d3-a456-426614174000',
            'category' => 'stuff',
            'code' => 'BAN-001',
            'quantity' => 10,
            'unit' => 'pcs',
            'price' => 100000,
            'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit.',
        ]);
    }
}
