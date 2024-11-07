<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Support\Arr;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 50; $i++) {
            Organization::create([
                'id' => '123e4567-e89b-12d3-a456-4266141740' . $i,
                'name' => 'Organization ' . $i,
                'industry' => 'Perdagangan',
                'email' => 'organization' . $i . '@sumberkencana.com',
                'status' => Arr::random(['warm', 'cold', 'hot']),
                'phone' => '08123456' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'owner' => 'user_satu@gmail.com',
                'website' => 'https://organization' . $i . '.com',
                'address' => 'Jl. Jend. Sudirman No. 10',
                'province' => 'Jakarta',
                'city' => 'Jakarta',
                'subdistrict' => 'Setiabudi',
                'village' => 'Kota Bambu',
                'zip_code' => '12910'
            ]);
        }
        Organization::create([
            'id' => '123e4567-e89b-12d3-a456-426614174004',
            'name' => 'PT Sumber Kencana',
            'industry' => 'Perdagangan',
            'email' => 'info@sumberkencana.com',
            'status' => 'hot',
            'phone' => '021-12345678',
            'owner' => 'user_satu@gmail.com',
            'website' => 'https://sumberkencana.com',
            'address' => 'Jl. Jend. Sudirman No. 10',
            'province' => 'Jakarta',
            'city' => 'Jakarta',
            'subdistrict' => 'Setiabudi',
            'village' => 'Kota Bambu',
            'zip_code' => '12910'
        ]);

        Organization::create([
            'id' => '123e4567-e89b-12d3-a456-426614174005',
            'name' => 'CV Maju Jaya',
            'industry' => 'Konstruksi',
            'email' => 'contact@majujaya.com',
            'status' => 'hot',
            'phone' => '021-23456789',
            'owner' => 'user_satu@gmail.com',
            'website' => 'https://majujaya.com',
            'address' => 'Jl. Anggrek No. 15',
            'province' => 'Jakarta',
            'city' => 'Bandung',
            'subdistrict' => 'Bandung Wetan',
            'village' => 'Cibadak',
            'zip_code' => '40121'
        ]);

        Organization::create([
            'id' => '123e4567-e89b-12d3-a456-426614174006',
            'name' => 'PT Bumi Resources',
            'industry' => 'Pertambangan',
            'email' => 'info@bumiresources.com',
            'status' => 'hot',
            'phone' => '021-34567890',
            'owner' => 'user_satu@gmail.com',
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
