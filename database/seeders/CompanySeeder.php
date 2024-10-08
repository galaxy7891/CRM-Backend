<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::create([
            'id' => '123e4567-e89b-12d3-a456-426614174000',
            'name' => 'PT Jaya Abadi',
            'industry' => 'Tekstil',
            'logo' => null,
            'email' => null,
            'phone' => null,
            'website' => null,
        ]);
    }
}
