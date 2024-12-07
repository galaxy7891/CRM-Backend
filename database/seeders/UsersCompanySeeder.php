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
            'email' => 'ptjayaabadi@gmail.com',
            'phone' => null,
            'website' => null,
        ]);

        UsersCompany::create([
            'id' => '123e4567-e89b-13a3-a456-426614175030',
            'name' => 'Printing Comp',
            'industry' => 'Jasa',
            'email' => 'printing@gmail.com',
            'phone' => null,
            'website' => null,
        ]);
        
        UsersCompany::create([
            'id' => '123e4567-e89b-62e3-a456-426614151005',
            'name' => 'Nusantara Delima',
            'industry' => 'Manufaktur',
            'email' => 'nusantaradelima@gmail.com',
            'phone' => null,
            'website' => null,
        ]);
        
        UsersCompany::create([
            'id' => '128e4267-1d4b-12d3-a456-426614112440',
            'name' => 'Campus Digital',
            'industry' => 'Teknologi',
            'email' => 'campusdigital@gmail.com',
            'phone' => null,
            'website' => null,
        ]);
        
        UsersCompany::create([
            'id' => '128e4267-1d4b-dsd3-a456-412314119870',
            'name' => 'PT Harapan Jaya',
            'industry' => 'Jasa',
            'email' => 'ptharapanjaya@gmail.com',
            'phone' => null,
            'website' => null,
        ]);
    }
}
