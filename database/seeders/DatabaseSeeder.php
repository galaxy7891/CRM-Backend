<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UsersCompanySeeder::class);
        $this->call(UserSeeder::class);
        $this->call(CustomersCompanySeeder::class);
        $this->call(CustomerSeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(DealSeeder::class);
        $this->call(DealsProductSeeder::class);
        $this->call(AccountsTypeSeeder::class);
        $this->call(ArticleSeeder::class);
    }
}
