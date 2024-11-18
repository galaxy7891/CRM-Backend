<?php

namespace Database\Seeders;

use App\Models\DealsProduct;
use Illuminate\Database\Seeder;

class DealsProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DealsProduct::create([
            'id' => '123e4567-e89b-12dp7-v212-646184150',
            'deals_id' => '123e4567-e89b-12d7-a452-42661413400',
            'product_id' => '123e4567-e89b-12d3-a456-4266141818',
            'quantity' => 5,
            'unit' => 'pcs'
        ]);

        DealsProduct::create([
            'id' => '123e4567-e89b-12dp7-v212-646281150',
            'deals_id' => '123e4567-e89b-12d7-a452-42661413300',
            'product_id' => '123e4567-e89b-12d3-a456-426614171001',
            'quantity' => 5,
            'unit' => 'pcs'
        ]);
        
        DealsProduct::create([
            'id' => '123e4567-e89b-12dp7-v212-646452150',
            'deals_id' => '123e4567-e89b-12d7-a452-42661413200',
            'product_id' => '123e4567-e89b-12d3-a456-426614172002',
            'quantity' => 5,
            'unit' => 'pcs'
        ]);
        
        DealsProduct::create([
            'id' => '123e4567-e89b-12dp7-v212-643457110',
            'deals_id' => '123e4567-e89b-12d7-a452-42661413100',
            'product_id' => '123e4567-e89b-12d3-a456-426614173003',
            'quantity' => 2,
            'unit' => 'pcs'
        ]);
    }
}
