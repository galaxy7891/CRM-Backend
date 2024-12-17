<?php

namespace Database\Seeders;

use App\Models\AccountsType;
use Illuminate\Database\Seeder;

class AccountsTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AccountsType::create([
            'id' => '123e4567-e89b-6641-a456-54312753',
            'user_company_id' => '123e4567-e89b-12d3-a456-426614174000',
            'account_type' => 'trial',
            'start_date' => now(),
            'end_date' => now()->addDays(7),
        ]);
         
        AccountsType::create([
            'id' => '123e4567-e12f-1234d-a456-54312754',
            'user_company_id' => '123e4567-e89b-13a3-a456-426614175030',
            'account_type' => 'regular',
            'start_date' => now(),
            'end_date' => now()->addDays(30),
        ]);
        
        AccountsType::create([
            'id' => '123e4567-we2b-6641-a4as4-54312755',
            'user_company_id' => '123e4567-e89b-62e3-a456-426614151005',
            'account_type' => 'professional',
            'start_date' => now(),
            'end_date' => now()->addDays(30),
        ]);
        
        AccountsType::create([
            'id' => '123e4567-e12b-as41-87g6-54312756',
            'user_company_id' => '128e4267-1d4b-12d3-a456-426614112440',
            'account_type' => 'business',
            'start_date' => now(),
            'end_date' => now()->addDays(30),
        ]);
        
        AccountsType::create([
            'id' => '12qw4567-re123-6431-a512-54312757',
            'user_company_id' => '128e4267-1d4b-dsd3-a456-412314119870',
            'account_type' => 'unactive',
            'start_date' => now()->subDays(7),
            'end_date' => now(),
        ]);
    }
}
