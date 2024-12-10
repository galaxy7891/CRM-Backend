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
            'id' => '123e457-e89b-1dp7-v12-64628115016',
            'deals_id' => '123e4567-e89b-12d7-a452-42661413300',
            'product_id' => '123e4567-e89b-12d3-a456-426614171001',
            'quantity' => 5,
            'unit' => 'pcs'
        ]);

        DealsProduct::create([
            'id' => '123e4567-e89b-12dp7-v212-646452150',
            'deals_id' => '123e4567-e89b-12d7-a452-42661413355',
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
        DealsProduct::create([
            'id' => '123e4567-e89b-12dp7-v212-643457111',
            'deals_id' => '123e4567-e89b-12d7-a452-42661413299',
            'product_id' => '123e4567-e89b-12d3-a456-426614181011',
            'quantity' => 2,
            'unit' => 'pcs'
        ]);

        DealsProduct::create([
            'id' => '123e4567-e89b-12dp7-v212-643457222',
            'deals_id' => '123e4567-e89b-12d7-a452-42661413188',
            'product_id' => '123e4567-e89b-12d3-a456-426614182012',
            'quantity' => 2,
            'unit' => 'pcs'
        ]);
        DealsProduct::create([
            'id' => '123e4567-e89b-12dp7-v212-643457233',
            'deals_id' => '123e4567-e89b-12d7-a452-42661413177',
            'product_id' => '123e4567-e89b-12d3-a456-426614183013',
            'quantity' => 2,
            'unit' => 'pcs'
        ]);
    }
}
