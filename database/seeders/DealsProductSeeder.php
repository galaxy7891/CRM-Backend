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
            'id' => '123e4127-e89b-124-v22-6443512a',
            'deals_id' => '123e4567-e89b-12d7-a452-42661413400',
            'product_id' => '123e4567-e89b-12d3-a456-426614173004',
            'quantity' => 5,
            'unit' => 'pcs'
        ]);

        DealsProduct::create([
            'id' => '123e457-e89b-1dp7-v12-64628115016',
            'deals_id' => '123e4567-e89b-12d7-a452-42661413300',
            'product_id' => '123e4567-e89b-12d3-a456-426614172004',
            'quantity' => 5,
            'unit' => 'pcs'
        ]);
        
        DealsProduct::create([
            'id' => '123e4567-e89b-12dp7-v212-646452150',
            'deals_id' => '123e4567-e89b-12d7-a452-42661413200',
            'product_id' => '123e4567-e89b-12d3-a456-426614171004',
            'quantity' => 5,
            'unit' => 'pcs'
        ]);
        
        DealsProduct::create([
            'id' => '123e4567-e89b-12dp7-v212-643457110',
            'deals_id' => '123e4567-e89b-12d7-a452-42661413100',
            'product_id' => '123e4567-e89b-12d3-a456-426614171004',
            'quantity' => 2,
            'unit' => 'pcs'
        ]);
    }
}
