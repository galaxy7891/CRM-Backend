<?php

namespace Database\Seeders;

use App\Models\UsersCompany;
use Illuminate\Database\Seeder;

class UsersCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UsersCompany::create([
            'id' => '123e4567-e89b-12d3-a456-426614174000',
            'name' => 'PT Jaya Abadi',
            'industry' => 'Tekstil',
            'email' => null,
            'phone' => null,
            'website' => null,
        ]);
    }
}
