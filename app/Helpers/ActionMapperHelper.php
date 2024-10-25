<?php

namespace App\Helpers;

class ActionMapperHelper
{
    public static function mapActionTitle(string $action): string
    {
        $actionMapping = [
            'CREATE' => 'Tambah',
            'UPDATE' => 'Perbarui',
            'DELETE' => 'Hapus',
        ];
    
        return $actionMapping[$action] ?? $action;
    }
    
    public static function mapActionDesc(string $action): string
    {
        $actionMapping = [
            'CREATE' => 'menambahkan',
            'UPDATE' => 'memperbarui',
            'DELETE' => 'menghapus',
        ];

        return $actionMapping[$action] ?? $action;
    }

    public static function mapModels(string $modelName): string
    {
        $modelsMapping = [
            'users' => 'pengguna',
            'companies' => 'perusahaan pengguna',
            'products' => 'produk',
            'organizations' => 'perusahaan pelanggan',
            'user_invitations' => 'pengguna',
            'customers' => 'pelanggan',
            'otps' => 'OTP',
        ];

        return $modelsMapping[$modelName] ?? $modelName;
    }

    public static function mapProperties(string $propertiesName, string $modelName): string
    {
        $commonMapping = [
            'email' => 'email',
            'first_name' => 'nama depan',
            'last_name' => 'nama belakang',
            'phone' => 'nomor telepon',
            'website' => 'website',
            'job_position' => 'jabatan',
            'role' => 'akses',
            'industry' => 'jenis industri',
            'status' => 'status',
            'gender' => 'jenis kelamin',
            'province' => 'provinsi',
            'city' => 'kota',
            'subdistrict' => 'kecamatan',
            'village' => 'kelurahan',
            'zip_code' => 'kode pos',
            'address' => 'alamat',
            'owner' => 'penanggung jawab',
            'code' => 'kode',
            'expired_at' => 'kadaluarsa',  /////
        ];
        
        $modelSpecificMapping = [
            'companies' => [
                'name' => 'nama perusahaan',
                'image_url' => 'foto profil perusahaan',
            ],
            'user_invitations' => [
                'email' => 'email',
                'token' => 'token',
            ],
            'users' => [
                'password' => 'kata sandi',
                'job_position' => 'jabatan',
                'role' => 'akses',
                'gender' => 'jenis kelamin',
                'image_url' => 'foto profil',
            ],
            'password_reset_tokens' => [
                'token' => 'token',
            ],
            'otps' => [
                'is_used' => 'digunakan',     /////
            ],
            'customers' => [
                'customerCategory' => 'kategori pelanggan',
                'job' => 'pekerjaan',
                'description' => 'deskripsi',
                'birthdate' => 'tanggal lahir',
            ],
            'deals' => [
                'name' => 'nama deals',
                'deals_customer' => 'nama pelanggan',
                'description' => 'deskripsi',
                'tag' => 'tag',  /////
                'stage' => 'tahapan',
                'open_date' => 'tanggal pembukaan',
                'close_date' => 'tanggal penutupan',
                'expected_close_date' => 'tanggal perkiraan penutupan', 
                'value_estimated' => 'perkiraan nilai',
                'value_actual' => 'nilai sebenarnya',
                'payment_category' => 'kategori pembayaran',
                'payment_duration' => 'durasi pembayaran',
            ],
            'organizations' => [
                'name' => 'nama perusahaan',
            ],
            'products' => [
                'name' => 'nama produk',
                'category' => 'kategori produk',
                'quantity' => 'jumlah produk',
                'unit' => 'satuan produk',
                'price' => 'harga produk',
                'description' => 'deskripsi',
                'image_url' => 'foto produk',
            ],
        ];
    
        if (isset($modelSpecificMapping[$modelName][$propertiesName])) {
            return $modelSpecificMapping[$modelName][$propertiesName];
        }
    
        return $commonMapping[$propertiesName] ?? $propertiesName;
    }
}
