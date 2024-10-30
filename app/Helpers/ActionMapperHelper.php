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
            'users' => 'karyawan',
            'companies' => 'perusahaan karyawan',
            'products' => 'produk',
            'organizations' => 'perusahaan pelanggan',
            'user_invitations' => 'karyawan',
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

    public static function mapDescription($log, array $changes, string $modelName): string
    {
        $userName = ucfirst($log->user->first_name) . ' ' . ucfirst($log->user->last_name);
        $description = '';

        switch ($log->action) {
            case 'CREATE':
                $description = self::mapCreateDescription($log, $modelName, $changes, $userName);
                break;
            // case 'UPDATE':
            //     $description = self::mapUpdateDescription($log, $modelName, $changes, $userName);
            //     break;
            // case 'DELETE':
            //     $description = self::mapDeleteDescription($log, $modelName, $changes, $userName);
            //     break;
        }

        return $description;
    }

    private static function mapCreateDescription($log, string $modelName, array $changes, string $userName): string
    {
        switch ($modelName) {
            case 'companies':
                return 'Perusahaan ' . $changes['name']['new'] . ' dibuat oleh ' . $userName;

            case 'user_invitations':
                return $userName . ' mengundang ' . $changes['email']['new'] . ' untuk bergabung di Perusahaan ' . $changes['name']['new'];

            case 'users':
                return 'Register akun ' . $userName;

            case 'customers':
                $customerCategory = $changes['customerCategory']['new'] ?? '';
                $customerName = trim(($changes['first_name']['new'] ?? '') . ' ' . ($changes['last_name']['new'] ?? ''));

                if ($customerCategory === 'leads') {
                    return $userName . ' menambahkan data Leads ' .  $customerName;
                } elseif ($customerCategory === 'contact') {
                    return $userName . ' menambahkan data Kontak ' . $customerName;
                }
                break;
            case 'deals':
                $valueEstimated = $changes['value_estimated']['new'] ?? '';
                return $userName . ' menambahkan data Deals ' . $changes['name']['new'] . ' sebesar ' . $valueEstimated;

            case 'organizations':
                return $userName . ' menambahkan data Perusahaan ' . $changes['name']['new'];

            case 'products':
                return $userName . ' menambahkan data Produk ' . $changes['name'];
        }
////////sampe sini/////////
        return '';
    }

    // private static function mapUpdateDescription($log, string $modelName, array $changes, string $userName): string
    // {
    //     switch ($modelName) {
    //         case 'companies':
    //             return "$userName {memperbarui} data {Perusahaan} $changes[namaCompanies]";
    //         case 'users':
    //             $isSelfUpdate = $log->user->id === ($changes['id']['new'] ?? null);
                
    //             if ($isSelfUpdate) {
    //                 return self::mapSelfUpdateDescription($changes, $userName);
    //             } else {
    //                 $namaKaryawanAdmin = ucfirst($log->user->first_name) . ' ' . ucfirst($log->user->last_name); 
    //                 return "$namaKaryawanAdmin {memperbarui} data diri {Karyawan} $changes[namaKaryawan]";
    //             }
    //         case 'customers':
    //             $customerCategory = $changes['customerCategory']['new'] ?? '';
    //             $firstNameLeads = $changes['firstNameLeads'] ?? '';
    //             $lastNameLeads = $changes['lastNameLeads'] ?? '';

    //             if ($customerCategory === 'leads') {
    //                 return "$userName {memperbarui} data {Leads} $firstNameLeads $lastNameLeads";
    //             } elseif ($customerCategory === 'contact') {
    //                 return "$userName {memperbarui} data {Kontak} $firstNameLeads $lastNameLeads";
    //             }
    //             break;
    //         case 'deals':
    //             // Logika untuk deals
    //             return self::mapDealsUpdateDescription($changes, $userName);
    //         case 'organizations':
    //             return "$userName {memperbarui} data {Perusahaan} $changes[namaOrganization]";
    //         case 'products':
    //             return self::mapProductsUpdateDescription($changes, $userName);
    //     }

    //     return '';
    // }

    // private static function mapDeleteDescription($log, string $modelName, array $changes, string $userName): string
    // {
    //     switch ($modelName) {
    //         case 'companies':
    //             return "{Perusahaan} {$changes['namaCompanies']} {dihapus} oleh $userName";
    //         case 'users':
    //             // Logika untuk deskripsi penghapusan user
    //             return "$userName {menghapus} akunnya";
    //         case 'customers':
    //             $customerCategory = $changes['customerCategory']['new'] ?? '';
    //             $firstNameLeads = $changes['firstNameLeads'] ?? '';
    //             $lastNameLeads = $changes['lastNameLeads'] ?? '';

    //             if ($customerCategory === 'leads') {
    //                 return "$userName {menghapus} data {Leads} $firstNameLeads $lastNameLeads";
    //             } elseif ($customerCategory === 'contact') {
    //                 return "$userName {menghapus} data {Kontak} {$changes['firstNameKontak']} {$changes['lastNameKontak']}";
    //             }
    //             break;
    //         case 'deals':
    //             return "$userName {menghapus} data {Deals}";
    //         case 'organizations':
    //             return "$userName {menghapus} data {Perusahaan} {$changes['namaOrganizations']}";
    //         case 'products':
    //             return "$userName {menghapus} data {Produk} {$changes['namaProduct']}";
    //     }

    //     return '';
    // }

    private static function mapSelfUpdateDescription(array $changes, string $userName): string
    {
        // Implementasikan logika untuk deskripsi update data sendiri
        if (isset($changes['password'])) {
            return "$userName {memperbarui} password";
        } elseif (isset($changes['profile_picture'])) {
            return "$userName {memperbarui} foto profil";
        } else {
            return "$userName {memperbarui} data diri";
        }
    }

    private static function mapDealsUpdateDescription(array $changes, string $userName): string
    {
        // Implementasikan logika untuk deskripsi update deals
        return "$userName {memperbarui} data {Deals} {$changes['namaDeals']}";
    }

    private static function mapProductsUpdateDescription(array $changes, string $userName): string
    {
        // Implementasikan logika untuk deskripsi update produk
        return "$userName {memperbarui} data {Produk} {$changes['namaProduk']}";
    }
}
