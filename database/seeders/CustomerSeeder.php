<?php

namespace Database\Seeders;

use App\Models\Customer;
use PharIo\Manifest\Email;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::create([
            'id' => '123e4567-e89b-12d3-a456-426614174001',
            'organization_id' => '123e4567-e89b-12d3-a456-426614174004',
            'first_name' => 'Tejo',
            'last_name' => 'Wijaya',
            'customerCategory' => 'leads',
            'job' => 'PNS',
            'description' => 'Lorem ipsum dolor sit amet.',
            'status' => 'hot',
            'email' => 'tejowijaya1@gmail.com',
            'phone' => '08578900123',
            'owner' => 'user_admin@gmail.com',
            'address' => 'Jl Kemayoran Baru',
            'country' => 'Indonesia',
            'city' => 'Jakarta',
            'subdistrict' => 'Kemayoran',
            'village' => 'Kemayoran Baru',
            'zip_code' => '12345'
        ]);

        Customer::create([
            'id' => '123e4567-e89b-12d3-a456-426614174002',
            'organization_id' => '123e4567-e89b-12d3-a456-426614174004',
            'first_name' => 'Siti',
            'last_name' => 'Aisyah',
            'customerCategory' => 'contact',
            'job' => 'Guru',
            'description' => 'Pekerja keras dan teliti.',
            'status' => 'warm',
            'email' => 'siti.aisyah@gmail.com',
            'phone' => '08578900234',
            'owner' => 'user_satu@gmail.com',
            'address' => 'Jl Kebon Jeruk',
            'country' => 'Indonesia',
            'city' => 'Jakarta',
            'subdistrict' => 'Palmerah',
            'village' => 'Palmerah Selatan',
            'zip_code' => '12346'
        ]);

        Customer::create([
            'id' => '123e4567-e89b-12d3-a456-426614174003',
            'organization_id' => '123e4567-e89b-12d3-a456-426614174004',
            'first_name' => 'Andi',
            'last_name' => 'Saputra',
            'customerCategory' => 'leads',
            'job' => 'Dokter',
            'description' => 'Ahli bedah jantung.',
            'status' => 'cold',
            'email' => 'andi.saputra@gmail.com',
            'phone' => '08578900345',
            'owner' => 'user_admin@gmail.com',
            'address' => 'Jl Bintaro',
            'country' => 'Indonesia',
            'city' => 'Jakarta',
            'subdistrict' => 'Kebayoran',
            'village' => 'Bintaro Utara',
            'zip_code' => '12347'
        ]);

        Customer::create([
            'id' => '123e4567-e89b-12d3-a456-426614174004',
            'organization_id' => '123e4567-e89b-12d3-a456-426614174004',
            'first_name' => 'Rani',
            'last_name' => 'Putri',
            'customerCategory' => 'contact',
            'job' => 'Pengacara',
            'description' => 'Spesialis hukum keluarga.',
            'status' => 'warm',
            'email' => 'rani.putri@gmail.com',
            'phone' => '08578900456',
            'owner' => 'user_dua@gmail.com',
            'address' => 'Jl Sudirman',
            'country' => 'Indonesia',
            'city' => 'Jakarta',
            'subdistrict' => 'Menteng',
            'village' => 'Menteng Atas',
            'zip_code' => '12348'
        ]);

        Customer::create([
            'id' => '123e4567-e89b-12d3-a456-426614174005',
            'organization_id' => '123e4567-e89b-12d3-a456-426614174004',
            'first_name' => 'Dian',
            'last_name' => 'Sari',
            'customerCategory' => 'leads',
            'job' => 'Arsitek',
            'description' => 'Desainer interior profesional.',
            'status' => 'hot',
            'email' => 'dian.sari@gmail.com',
            'phone' => '08578900567',
            'owner' => 'user_satu@gmail.com',
            'address' => 'Jl Thamrin',
            'country' => 'Indonesia',
            'city' => 'Jakarta',
            'subdistrict' => 'Tanah Abang',
            'village' => 'Thamrin Utama',
            'zip_code' => '12349'
        ]);

        Customer::create([
            'id' => '123e4567-e89b-12d3-a456-426614174006',
            'organization_id' => '123e4567-e89b-12d3-a456-426614174004',
            'first_name' => 'Budi',
            'last_name' => 'Setiawan',
            'customerCategory' => 'contact',
            'job' => 'Aktor',
            'description' => 'Pemain film action.',
            'status' => 'warm',
            'email' => 'budi.setiawan@gmail.com',
            'phone' => '08578900678',
            'owner' => 'user_satu@gmail.com',
            'address' => 'Jl Rasuna Said',
            'country' => 'Indonesia',
            'city' => 'Jakarta',
            'subdistrict' => 'Kuningan',
            'village' => 'Kuningan Barat',
            'zip_code' => '12350'
        ]);

        Customer::create([
            'id' => '123e4567-e89b-12d3-a456-426614174007',
            'organization_id' => '123e4567-e89b-12d3-a456-426614174004',
            'first_name' => 'Agus',
            'last_name' => 'Slamet',
            'customerCategory' => 'leads',
            'job' => 'Polisi',
            'description' => 'Petugas lalu lintas.',
            'status' => 'cold',
            'email' => 'agus.slamet@gmail.com',
            'phone' => '08578900789',
            'owner' => 'user_admin@gmail.com',
            'address' => 'Jl Gatot Subroto',
            'country' => 'Indonesia',
            'city' => 'Jakarta',
            'subdistrict' => 'Setiabudi',
            'village' => 'Gatot Subroto Utara',
            'zip_code' => '12351'
        ]);

        Customer::create([
            'id' => '123e4567-e89b-12d3-a456-426614174008',
            'organization_id' => '123e4567-e89b-12d3-a456-426614174004',
            'first_name' => 'Eka',
            'last_name' => 'Yulianto',
            'customerCategory' => 'contact',
            'job' => 'Pilot',
            'description' => 'Kapten maskapai penerbangan.',
            'status' => 'hot',
            'email' => 'eka.yulianto@gmail.com',
            'phone' => '08578900890',
            'owner' => 'user_dua@gmail.com',
            'address' => 'Jl Mampang Prapatan',
            'country' => 'Indonesia',
            'city' => 'Jakarta',
            'subdistrict' => 'Mampang',
            'village' => 'Prapatan Lama',
            'zip_code' => '12352'
        ]);

        Customer::create([
            'id' => '123e4567-e89b-12d3-a456-426614174009',
            'organization_id' => '123e4567-e89b-12d3-a456-426614174004',
            'first_name' => 'Fauzi',
            'last_name' => 'Rahman',
            'customerCategory' => 'leads',
            'job' => 'Pengusaha',
            'description' => 'Owner perusahaan makanan cepat saji.',
            'status' => 'warm',
            'email' => 'fauzi.rahman@gmail.com',
            'phone' => '08578900901',
            'owner' => 'user_dua@gmail.com',
            'address' => 'Jl Fatmawati',
            'country' => 'Indonesia',
            'city' => 'Jakarta',
            'subdistrict' => 'Cilandak',
            'village' => 'Fatmawati Indah',
            'zip_code' => '12353'
        ]);

        Customer::create([
            'id' => '123e4567-e89b-12d3-a456-426614174010',
            'organization_id' => '123e4567-e89b-12d3-a456-426614174004',
            'first_name' => 'Gita',
            'last_name' => 'Pratiwi',
            'customerCategory' => 'contact',
            'job' => 'Designer',
            'description' => 'Ahli desain grafis.',
            'status' => 'cold',
            'email' => 'gita.pratiwi@gmail.com',
            'phone' => '08578900123',
            'owner' => 'user_satu@gmail.com',
            'address' => 'Jl Senayan',
            'country' => 'Indonesia',
            'city' => 'Jakarta',
            'subdistrict' => 'Kebayoran Lama',
            'village' => 'Senayan Barat',
            'zip_code' => '12354'
        ]);
        Customer::create([
            'id' => '123e4567-e89b-12d3-a456-426614174011',
            'organization_id' => '123e4567-e89b-12d3-a456-426614174004',
            'first_name' => 'Hari',
            'last_name' => 'Santoso',
            'customerCategory' => 'leads',
            'job' => 'Manajer',
            'description' => 'Manajer di perusahaan retail.',
            'status' => 'hot',
            'email' => 'hari.santoso@gmail.com',
            'phone' => '08578900912',
            'owner' => 'user_dua@gmail.com',
            'address' => 'Jl Manggarai',
            'country' => 'Indonesia',
            'city' => 'Jakarta',
            'subdistrict' => 'Tebet',
            'village' => 'Manggarai',
            'zip_code' => '12355'
        ]);

        Customer::create([
            'id' => '123e4567-e89b-12d3-a456-426614174012',
            'organization_id' => '123e4567-e89b-12d3-a456-426614174004',
            'first_name' => 'Dewi',
            'last_name' => 'Lestari',
            'customerCategory' => 'contact',
            'job' => 'Penulis',
            'description' => 'Penulis novel terkenal.',
            'status' => 'warm',
            'email' => 'dewi.lestari@gmail.com',
            'phone' => '08578900923',
            'owner' => 'user_admin@gmail.com',
            'address' => 'Jl Ciputat Raya',
            'country' => 'Indonesia',
            'city' => 'Jakarta',
            'subdistrict' => 'Ciputat',
            'village' => 'Ciputat Timur',
            'zip_code' => '12356'
        ]);

        Customer::create([
            'id' => '123e4567-e89b-12d3-a456-426614174013',
            'organization_id' => '123e4567-e89b-12d3-a456-426614174004',
            'first_name' => 'Irwan',
            'last_name' => 'Prakoso',
            'customerCategory' => 'leads',
            'job' => 'Insinyur',
            'description' => 'Ahli di bidang teknik sipil.',
            'status' => 'cold',
            'email' => 'irwan.prakoso@gmail.com',
            'phone' => '08578900934',
            'owner' => 'user_admin@gmail.com',
            'address' => 'Jl Fatmawati Raya',
            'country' => 'Indonesia',
            'city' => 'Jakarta',
            'subdistrict' => 'Cilandak',
            'village' => 'Fatmawati Lama',
            'zip_code' => '12357'
        ]);

        Customer::create([
            'id' => '123e4567-e89b-12d3-a456-426614174014',
            'organization_id' => '123e4567-e89b-12d3-a456-426614174004',
            'first_name' => 'Feri',
            'last_name' => 'Suharto',
            'customerCategory' => 'contact',
            'job' => 'Pedagang',
            'description' => 'Pemilik toko elektronik.',
            'status' => 'hot',
            'email' => 'feri.suharto@gmail.com',
            'phone' => '08578900945',
            'owner' => 'user_satu@gmail.com',
            'address' => 'Jl Meruya Utara',
            'country' => 'Indonesia',
            'city' => 'Jakarta',
            'subdistrict' => 'Kembangan',
            'village' => 'Meruya',
            'zip_code' => '12358'
        ]);

        Customer::create([
            'id' => '123e4567-e89b-12d3-a456-426614174015',
            'organization_id' => '123e4567-e89b-12d3-a456-426614174004',
            'first_name' => 'Rina',
            'last_name' => 'Sulastri',
            'customerCategory' => 'leads',
            'job' => 'Bidan',
            'description' => 'Bidan berpengalaman di klinik.',
            'status' => 'warm',
            'email' => 'rina.sulastri@gmail.com',
            'phone' => '08578900956',
            'owner' => 'user_dua@gmail.com',
            'address' => 'Jl Pondok Indah',
            'country' => 'Indonesia',
            'city' => 'Jakarta',
            'subdistrict' => 'Kebayoran Lama',
            'village' => 'Pondok Indah',
            'zip_code' => '12359'
        ]);

        Customer::create([
            'id' => '123e4567-e89b-12d3-a456-426614171011',
            'organization_id' => '123e4567-e89b-12d3-a456-426614174004',
            'first_name' => 'Rina',
            'last_name' => 'Sulastri',
            'customerCategory' => 'leads',
            'job' => 'Bidan',
            'description' => 'Bidan berpengalaman di klinik.',
            'status' => 'warm',
            'email' => 'rina.sulastri@gmail.com',
            'phone' => '08578900956',
            'owner' => 'user_satu@gmail.com',
            'address' => 'Jl Pondok Indah',
            'country' => 'Indonesia',
            'city' => 'Jakarta',
            'subdistrict' => 'Kebayoran Lama',
            'village' => 'Pondok Indah',
            'zip_code' => '12359'
        ]);
    }
}
