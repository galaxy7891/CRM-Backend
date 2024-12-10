<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //ADMIN LC  
        User::create([
            'id' => '123e4567-vwxy-8721-a981-421249743124',
            'user_company_id' => null,
            'google_id' => null,
            'email' => 'admin@gmail.com',
            'first_name' => 'Super',
            'last_name' => 'Admin LoyalCust',
            'password' => Hash::make('password123'),
            'phone' => '6287453114151',
            'job_position' => null,
            'role' => 'super_admin_lc', 
            'gender' => null, 
            'image_url' => null, 
            'image_public_id' => null, 
        ]); 
        
        //PT Jaya Abadi
        User::create([
            'id' => '123e4567-abcd-1123-a456-426342443123',
            'user_company_id' => '123e4567-e89b-12d3-a456-426614174000',
            'google_id' => null,
            'email' => 'employee_jaya@gmail.com',
            'first_name' => 'Karyawan',
            'last_name' => 'Jaya',
            'password' => Hash::make('password123'),
            'phone' => '628734311415',
            'job_position' => null,
            'role' => 'employee',
            'gender' => 'male',
            'image_url' => null,
            'image_public_id' => null,
        ]);
        
        User::create([
            'id' => '123e4567-abce-1527-a456-426341741424',
            'user_company_id' => '123e4567-e89b-12d3-a456-426614174000',
            'google_id' => null,
            'email' => 'admin_jaya@gmail.com',
            'first_name' => 'Admin',
            'last_name' => 'CDI',
            'password' => Hash::make('password123'),
            'phone' => '628712341798',
            'job_position' => null,
            'role' => 'employee',
            'gender' => 'male',
            'image_url' => null,
            'image_public_id' => null,
        ]);

        User::create([
            'id' => '123e4567-abcr-1985-a456-426312174125',
            'user_company_id' => '123e4567-e89b-12d3-a456-426614174000',
            'google_id' => null,
            'email' => 'superadmin_jaya@gmail.com',
            'first_name' => 'Super',
            'last_name' => 'Admin Jaya',
            'password' => Hash::make('password123'),
            'phone' => '628716519713',
            'job_position' => null,
            'role' => 'super_admin',
            'gender' => 'male',
            'image_url' => null,
            'image_public_id' => null,
        ]);

        //Nusantara Delima
        User::create([
            'id' => '123e4567-abwq-6531-a456-426342414125',
            'user_company_id' => '123e4567-e89b-62e3-a456-426614151005',
            'google_id' => null,
            'email' => 'employee_nusantara@gmail.com',
            'first_name' => 'Karyawan',
            'last_name' => 'Nusantara',
            'password' => Hash::make('password123'),
            'phone' => '628734311416',
            'job_position' => null,
            'role' => 'employee',
            'gender' => 'male',
            'image_url' => null,
            'image_public_id' => null,
        ]);

        User::create([
            'id' => '123e4567-iurq-4562-a456-426241741126',
            'user_company_id' => '123e4567-e89b-62e3-a456-426614151005',
            'google_id' => null,
            'email' => 'admin_nusantara@gmail.com',
            'first_name' => 'Admin',
            'last_name' => 'Nusantara',
            'password' => Hash::make('password123'),
            'phone' => '628712341751',
            'job_position' => null,
            'role' => 'employee',
            'gender' => 'male',
            'image_url' => null,
            'image_public_id' => null,
        ]);

        User::create([
            'id' => '123e4567-iurq-4562-a456-426342417426',
            'user_company_id' => '123e4567-e89b-62e3-a456-426614151005',
            'google_id' => null,
            'email' => 'superadmin_nusantara@gmail.com',
            'first_name' => 'Super',
            'last_name' => 'Admin ',
            'password' => Hash::make('password123'),
            'phone' => '628716519716',
            'job_position' => null,
            'role' => 'super_admin',
            'gender' => 'male',
            'image_url' => null,
            'image_public_id' => null,
        ]);

        //Campus Digital
        User::create([
            'id' => '123e4567-e89b-12d3-a456-426614174003',
            'user_company_id' => '128e4267-1d4b-12d3-a456-426614112440',
            'google_id' => null,
            'email' => 'employee_cdi@gmail.com',
            'first_name' => 'Karyawan',
            'last_name' => 'CDI',
            'password' => Hash::make('password123'),
            'phone' => '628734311413',
            'job_position' => null,
            'role' => 'employee',
            'gender' => 'male',
            'image_url' => null,
            'image_public_id' => null,
        ]);

        User::create([
            'id' => '123e4567-e89b-12d3-a456-426614174010',
            'user_company_id' => '128e4267-1d4b-12d3-a456-426614112440',
            'google_id' => null,
            'email' => 'admin_cdi@gmail.com',
            'first_name' => 'Admin',
            'last_name' => 'CDI',
            'password' => Hash::make('password123'),
            'phone' => '628712341724',
            'job_position' => null,
            'role' => 'admin',
            'gender' => 'male',
            'image_url' => null,
            'image_public_id' => null,
        ]);

        User::create([
            'id' => '123e4567-e89b-12d3-a456-426614174100',
            'user_company_id' => '128e4267-1d4b-12d3-a456-426614112440',
            'google_id' => null,
            'email' => 'superadmin_cdi@gmail.com',
            'first_name' => 'Super',
            'last_name' => 'Admin CDI',
            'password' => Hash::make('password123'),
            'phone' => '628716519736',
            'job_position' => null,
            'role' => 'super_admin',
            'gender' => 'male',
            'image_url' => null,
            'image_public_id' => null,
        ]);
        
        //Harapan Jaya
        User::create([
            'id' => '123e4567-i12b-abdw-a456-426614177412',
            'user_company_id' => '128e4267-1d4b-dsd3-a456-412314119870',
            'google_id' => null,
            'email' => 'employee_harapan@gmail.com',
            'first_name' => 'Karyawan',
            'last_name' => 'Harapan',
            'password' => Hash::make('password123'),
            'phone' => '628734311414',
            'job_position' => null,
            'role' => 'employee',
            'gender' => 'male',
            'image_url' => null,
            'image_public_id' => null,
        ]);

        User::create([
            'id' => '123e4567-i12b-tqdw-a456-426614177413',
            'user_company_id' => '128e4267-1d4b-dsd3-a456-412314119870',
            'google_id' => null,
            'email' => 'admin_harapan@gmail.com',
            'first_name' => 'Admin',
            'last_name' => 'Harapan',
            'password' => Hash::make('password123'),
            'phone' => '628712341756',
            'job_position' => null,
            'role' => 'employee',
            'gender' => 'male',
            'image_url' => null,
            'image_public_id' => null,
        ]);

        User::create([
            'id' => '123e4567-yt15-q8dw-a456-426614177412',
            'user_company_id' => '128e4267-1d4b-dsd3-a456-412314119870',
            'google_id' => null,
            'email' => 'superadmin_harapan@gmail.com',
            'first_name' => 'Super',
            'last_name' => 'Admin Harapan',
            'password' => Hash::make('password123'),
            'phone' => '628716519739',
            'job_position' => null,
            'role' => 'super_admin',
            'gender' => 'male',
            'image_url' => null,
            'image_public_id' => null,
        ]);
        User::create([
            'id' => '123e4567-e89b-12d3-a456-426614174111',
            'user_company_id' => '123e4567-e89b-12d3-a456-426614174000',
            'google_id' => null,
            'email' => 'sufyan.uimsib7@gmail.com',
            'first_name' => 'Sufyan',
            'last_name' => 'Hanif',
            'password' => Hash::make('123'),
            'phone' => '1315262346',
            'job_position' => null,
            'role' => 'admin',
            'gender' => 'male',
            'image_url' => null,
            'image_public_id' => null,
        ]);
        User::create([
            'id' => '123e4567-e89b-12d3-a456-426614174122',
            'user_company_id' => '123e4567-e89b-12d3-a456-426614174000',
            'google_id' => null,
            'email' => 'arifa.uimsib7@gmail.com',
            'first_name' => 'Arifa',
            'last_name' => 'Mutia',
            'password' => Hash::make('123'),
            'phone' => '2323164',
            'job_position' => null,
            'role' => 'admin',
            'gender' => 'male',
            'image_url' => null,
            'image_public_id' => null,
        ]);
        User::create([
            'id' => '123e4567-e89b-12d3-a456-426614174123',
            'user_company_id' => '123e4567-e89b-12d3-a456-426614174000',
            'google_id' => null,
            'email' => 'yudistira.uimsib7@gmail.com',
            'first_name' => 'Yudistira',
            'last_name' => 'Putra',
            'password' => Hash::make('123'),
            'phone' => '545841245',
            'job_position' => null,
            'role' => 'admin',
            'gender' => 'male',
            'image_url' => null,
            'image_public_id' => null,
        ]);
        User::create([
            'id' => '123e4567-e89b-12d3-a456-426614174124',
            'user_company_id' => '123e4567-e89b-12d3-a456-426614174000',
            'google_id' => null,
            'email' => 'hernan.sdmsib7@gmail.com',
            'first_name' => 'Hernan',
            'last_name' => 'Sandi',
            'password' => Hash::make('123'),
            'phone' => '454545115',
            'job_position' => null,
            'role' => 'admin',
            'gender' => 'male',
            'image_url' => null,
            'image_public_id' => null,
        ]);
        User::create([
            'id' => '123e4567-e89b-12d3-a456-426614174125',
            'user_company_id' => '123e4567-e89b-12d3-a456-426614174000',
            'google_id' => null,
            'email' => 'farina.sdmsib7@gmail.com',
            'first_name' => 'Farina',
            'last_name' => 'Naswa',
            'password' => Hash::make('123'),
            'phone' => '545451',
            'job_position' => null,
            'role' => 'admin',
            'gender' => 'male',
            'image_url' => null,
            'image_public_id' => null,
        ]);
    }
}
