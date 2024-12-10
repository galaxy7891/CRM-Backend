<?php

namespace Database\Seeders;

use App\Models\CustomersCompany;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomersCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CustomersCompany::factory()->count(100)->create();
        CustomersCompany::create([
            'id' => '123e4567-e89b-12d3-a456-426614174004',
            'name' => 'PT Sumber Kencana',
            'industry' => 'Perdagangan',
            'email' => 'info@sumberkencana.com',
            'status' => 'hot',
            'phone' => '021-12345678',
            'owner' => 'admin_cdi@gmail.com',
            'description' => 'Lorem ipsum, dolor sit amet consectetur adipisicing elit. Eveniet, dolorum',
            'website' => 'https://sumberkencana.com',
            'address' => 'Jl. Jend. Sudirman No. 10',
            'province' => 'Jakarta',
            'city' => 'Jakarta',
            'subdistrict' => 'Setiabudi',
            'village' => 'Kota Bambu',
            'zip_code' => '12910'
        ]);

        CustomersCompany::create([
            'id' => '123e4567-e89b-12d3-a456-426614174005',
            'name' => 'CV Maju Jaya',
            'industry' => 'Konstruksi',
            'email' => 'contact@majujaya.com',
            'status' => 'hot',
            'phone' => '021-23456789',
            'owner' => 'admin_cdi@gmail.com',
            'description' => 'Lorem ipsum, dolor sit amet consectetur adipisicing elit. Eveniet, dolorum',
            'website' => 'https://majujaya.com',
            'address' => 'Jl. Anggrek No. 15',
            'province' => 'Jakarta',
            'city' => 'Bandung',
            'subdistrict' => 'Bandung Wetan',
            'village' => 'Cibadak',
            'zip_code' => '40121'
        ]);

        CustomersCompany::create([
            'id' => '123e4567-e89b-12d3-a456-426614174006',
            'name' => 'PT Bumi Resources',
            'industry' => 'Pertambangan',
            'email' => 'info@bumiresources.com',
            'status' => 'hot',
            'phone' => '021-34567890',
            'owner' => 'admin_cdi@gmail.com',
            'description' => 'Lorem ipsum, dolor sit amet consectetur adipisicing elit. Eveniet, dolorum',
            'website' => 'https://bumiresources.com',
            'address' => 'Jl. Raya Kebayoran No. 5',
            'province' => 'Jakarta',
            'city' => 'Surabaya',
            'subdistrict' => 'Gubeng',
            'village' => 'Kedungdoro',
            'zip_code' => '60282'
        ]);
    }
}
