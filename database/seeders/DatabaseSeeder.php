<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(CompanySeeder::class);
        $this->call(UserSeeder::class);
        $this->call(OrganizationSeeder::class);
        $this->call(CustomerSeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(DealSeeder::class);
        // \App\Models\Company::factory(1)->create();
        // \App\Models\User::factory(1)->create();
        // \App\Models\Organization::factory(3)->create();
        // \App\Models\Customer::factory(10)->create();
    }
}
