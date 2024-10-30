<?php

namespace App\Helpers;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\Product;
use App\Models\User;

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
            'companies' => 'perusahaan Karyawan',
            'products' => 'produk',
            'organizations' => 'perusahaan Kontak',
            'user_invitations' => 'karyawan',
            'leads' => 'leads',
            'contact' => 'kontak',
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
            'expired_at' => 'kadaluarsa',
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
                'is_used' => 'digunakan',
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
                'tag' => 'tag',
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
                $companiesName = Company::getCompaniesNameById($log->changes['id']['new']);
                return 'Perusahaan ' . $companiesName . ' dibuat oleh ' . $userName;

            case 'user_invitations':
                $companiesName = $log->user->company->name ??  '';
                return $userName . ' mengundang ' . $changes['email']['new'] . ' untuk bergabung di Perusahaan ' . $companiesName;

            case 'users':
                $userAdminName = User::getUserNameById($changes['id']['new']);
                return 'Register akun ' . $userAdminName;

            case 'leads':
                $customerName = Customer::getCustomerNameById($changes['id']['new']);
                return $userName . ' menambahkan data Leads ' .  $customerName;
                break;

            case 'contact':
                $customerName = Customer::getCustomerNameById($changes['id']['new']);
                return $userName . ' menambahkan data Kontak ' . $customerName;
                break;

            case 'deals':
                $valueEstimated = number_format($changes['value_estimated']['new'], 0, ',', '.')?? '';
                $dealsName = Deal::getDealsNameById($changes['id']['new']);
                return $userName . ' menambahkan data Deals ' . $dealsName . ' sebesar ' . $valueEstimated;

            case 'organizations':
                return $userName . ' menambahkan data Perusahaan ' . $changes['name']['new'];

            case 'products':
                return $userName . ' menambahkan data Produk ' . $changes['name']['new'];
        }

        return '';
    }

    private static function mapUpdateDescription($log, string $modelName, array $changes, string $userName): string
    {
        switch ($modelName) {
            case 'companies':
                $companiesName = Company::getCompaniesNameById($changes['id']['new']);
                return $userName . ' memperbarui data Perusahaan ' . $companiesName;

            case 'users':
                $isSelfUpdate = $log->user->id === ($changes['id']['new'] ?? null);
                $employeeName = User::getUserNameById($changes['id']['new']);
                
                if ($isSelfUpdate) {
                    return self::mapSelfUpdateDescription($changes, $userName);
                } else {
                    return $userName . ' memperbarui data diri Karyawan ' . $employeeName;
                }

            case 'leads':
                $customerName = Customer::getCustomerNameById($changes['id']['new']);
                return $userName . ' memperbarui data Leads ' . $customerName;
                break;
                
            case 'contact':
                $customerName = Customer::getCustomerNameById($changes['id']['new']);
                return $userName . ' memperbarui data Kontak ' . $customerName;
                break;

            case 'deals':  
                $dealsName = Deal::getDealsNameById($changes['id']['new']);
                return self::mapDealsUpdateDescription($changes, $userName, $dealsName);

            case 'organizations':
                $companiesName = Company::getCompaniesNameById($changes['id']['new']);
                return $userName . ' memperbarui data Perusahaan ' . $changes['name']['new'];
         
            case 'products': 
                $productName = Product::getProductNameById($changes['id']['new']);
                $quantityOld = $changes['quantity']['old']?? '';
                $quantityNew = $changes['quantity']['new']?? '';
                if (isset($quantityNew)){
                    return $userName . ' memperbarui data jumlah Produk ' . $productName . ' dari ' . $quantityOld . ' menjadi ' . $quantityNew;
                }

                return $userName . ' memperbarui data Produk ' . $productName;
        }

    //     return '';
    // }

    private static function mapDeleteDescription($log, string $modelName, array $changes, string $userName): string
    {
        switch ($modelName) {
            case 'companies':
                $companiesName = Company::getCompaniesNameById($changes['id']['new']);
                return 'Perusahaan ' . $companiesName . ' dihapus oleh ' . $userName;

            case 'users':
                return $userName . ' menghapus akunnya';
            
            // case 'customers':
            //     $customerCategory = $changes['customerCategory']['new'] ?? '';
            //     $customerName = Customer::getCustomerNameById($changes['id']['new']);

            //     if ($customerCategory === 'leads') {
            //         return $userName . ' menghapus data Leads ' . $customerName;
            //     } elseif ($customerCategory === 'contact') {
            //         return $userName . ' menghapus data Kontak ' . $customerName;
            //     }
            //     break;

            case 'leads':
                $customerName = Customer::getCustomerNameById($changes['id']['new']);
                return $userName . ' menghapus data Leads ' . $customerName;
                break;
            
            case 'contact':
                $customerName = Customer::getCustomerNameById($changes['id']['new']);
                return $userName . ' menghapus data Kontak ' . $customerName;
                break;

            case 'deals':
                $dealsName = Deal::getDealsNameById($changes['id']['new']);
                return $userName . ' menghapus data Deals ' . $dealsName;

            case 'organizations':
                $companiesName = Company::getCompaniesNameById($changes['id']['new']);
                return $userName . ' menghapus data Perusahaan ' . $companiesName;

            case 'products':
                $productName = Product::getProductNameById($changes['id']['new']);
                return $userName . ' menghapus data Produk ' . $productName;
        }

    //     return '';
    // }

    private static function mapSelfUpdateDescription(array $changes, string $userName): string
    {
        if (isset($changes['password'])) {
            return $userName . ' memperbarui passwordnya';

        } elseif (isset($changes['image_url'])) {
            return $userName . ' memperbarui foto profilnya';
        
        } else {
            return $userName . ' memperbarui data dirinya';
        }
    }

    private static function mapDealsUpdateDescription(array $changes, string $userName, string $dealsName): string
    {
        $stageOld = $changes['stage']['old'] ?? '';
        $stageNew = $changes['stage']['new'] ?? '';

        if (isset($stageNew)) {
            if ($stageNew !== 'won' && $stageNew !== 'lost') {
                return $userName . ' memindahkan tahap Deals ' . $dealsName .
                    ' dari ' . $stageOld . ' menjadi ' . $stageNew;

            } elseif ($stageNew == 'won') {
                $valueActual = number_format($changes['value_actual']['new'], 0, ',', '.')?? '';
                return 'Deals ' . $dealsName . ' sebesar Rp' . $valueActual . 
                    ' berhasil tercapai oleh ' . $userName;

            } elseif ($stageNew == 'lost') {
                $valueEstimated = number_format($changes['value_estimated']['new'], 0, ',', '.')?? '';
                return 'Deals ' . $dealsName . ' dengan perkiraan sebesar Rp' . $valueEstimated . ' gagal didapatkan oleh ' . $userName;
            }
        }

        return $userName . ' memperbarui data Deals ' . $dealsName;
    }
}
