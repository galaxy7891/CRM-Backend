<?php

namespace App\Helpers;

use App\Models\UsersCompany;
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
            'users_companies' => 'perusahaan User',
            'products' => 'produk',
            'customers_companies' => 'perusahaan Pelanggan',
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
            'users_companies' => [
                'name' => 'nama perusahaan user',
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
            'customers_companies' => [
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

    public static function mapCategoryProduct($categoryProduct)
    {
        switch ($categoryProduct) {
            case 'stuff':
                return 'Barang';
            case 'service':
                return 'Jasa';
            default:
                return $categoryProduct;
        }
    }

    public static function mapCategoryProductToDatabase($categoryProduct)
    {
        switch ($categoryProduct) {
            case 'Barang':
                return 'stuff';
            case 'Jasa':
                return 'service';
            default:
                return $categoryProduct;
        }
    }

    public static function mapCategoryDeals($categoryDeals)
    {
        switch ($categoryDeals) {
            case 'customers':
                return 'Pelanggan';
            case 'customers_companies':
                return 'Perusahaan';
            default:
                return $categoryDeals;
        }
    }

    public static function mapCategoryDealsToDatabase($categoryDeals)
    {
        switch ($categoryDeals) {
            case 'Pelanggan':
                return 'customers';
            case 'Perusahaan':
                return 'customers_companies';
            default:
                return $categoryDeals;
        }
    }

    public static function mapStatus($status)
    {
        switch ($status) {
            case 'cold':
                return 'Rendah';
            case 'warm':
                return 'Sedang';
            case 'hot':
                return 'Tinggi';
            default:
                return $status;
        }
    }

    public static function mapStatusToDatabase($status)
    {
        switch ($status) {
            case 'Rendah':
                return 'cold';
            case 'Sedang':
                return 'warm';
            case 'Tinggi':
                return 'hot';
            default:
                return $status;
        }
    }

    public static function mapCustomerCategory($customerCategory)
    {
        switch ($customerCategory) {
            case 'leads':
                return 'Leads';
            case 'contact':
                return 'Kontak';
            default:
                return $customerCategory;
        }
    }   

    public static function mapCustomerCategoryToDatabase($customerCategory)
    {
        switch ($customerCategory) {
            case 'Leads':
                return 'leads';
            case 'Kontak':
                return 'contact';
            default:
                return $customerCategory;
        }
    }
    
    public static function mapRole($role)
    {
        switch ($role) {
            case 'super_admin':
                return 'Super Admin';
            case 'admin':
                return 'Admin';
            case 'employee':
                return 'Karyawan';
            default:
                return $role;
        }
    }
    
    public static function mapRoleToDatabase($role)
    {
        switch ($role) {
            case 'Super Admin':
                return 'super_admin';
            case 'Admin':
                return 'admin';
            case 'Karyawan':
                return 'employee';
            default:
                return $role;
        }
    }
    
    public static function mapGender($gender)
    {
        switch ($gender) {
            case 'male':
                return 'Laki-laki';
            case 'female':
                return 'Perempuan';
            case 'other':
                return 'Lainnya';
            default:
                return $gender;
        }
    }
    
    public static function mapGenderToDatabase($status)
    {
        switch ($status) {
            case 'Laki-laki':
                return 'male';
            case 'Perempuan':
                return 'female';
            case 'Lainnya':
                return 'other';
            default:
                return $status;
        }
    }
    
    public static function mapStageDeal($stage)
    {
        switch ($stage) {
            case 'qualificated':
                return 'Kualifikasi';
            case 'proposal':
                return 'Proposal';
            case 'negotiate':
                return 'Negosiasi';
            case 'won':
                return 'Tercapai';
            case 'lose':
                return 'Gagal';
            default:
                return $stage;
        }
    }

    public static function mapStageDealToDatabase($stage)
    {
        switch ($stage) {
            case 'Kualifikasi':
                return 'qualificated';
            case 'Proposal':
                return 'proposal';
            case 'Negosiasi':
                return 'negotiate';
            case 'Tercapai':
                return 'won';
            case 'Gagal':
                return 'lose';
            default:
                return $stage;
        }
    }

    public static function mapPaymentCategory($paymentCategory)
    {
        switch ($paymentCategory) {
            case 'once':
                return 'Sekali';
            case 'daily':
                return 'Hari';
            case 'monthly':
                return 'Bulan';
            case 'yearly':
                return 'Tahun';
            default:
                return $paymentCategory;
        }
    }

    public static function mapPaymentCategoryToDatabase($paymentCategory)
    {
        switch ($paymentCategory) {
            case 'Sekali':
                return 'once';
            case 'Hari':
                return 'daily';
            case 'Bulan':
                return 'monthly';
            case 'Tahun':
                return 'yearly';
            default:
                return $paymentCategory;
        }
    }

    public static function mapStatusArticle($status)
    {
        switch ($status) {
            case 'draft':
                return 'Draf';
            case 'post':
                return 'Terbit';
            default:
                return $status;
        }
    }

    public static function mapStatusArticleToDatabase($status)
    {
        switch ($status) {
            case 'Draf':
                return 'draft';
            case 'Terbit':
                return 'post';
            default:
                return $status;
        }
    }

    public static function mapAccountsTypes($accountsType)
    {
        switch ($accountsType) {
            case 'trial':
                return 'Percobaan';
            case 'regular':
                return 'Reguler';
            case 'professional':
                return 'Profesional';
            case 'business':
                return 'Bisnis';
            case 'unactive':
                return 'Tidak Aktif';
            default:
                return $accountsType;
        }
    }
    
    public static function mapAccountsTypesToDatabase($accountsType)
    {
        switch ($accountsType) {
            case 'Percobaan':
                return 'trial';
            case 'Reguler':
                return 'regular';
            case 'Profesional':
                return 'professional';
            case 'Bisnis':
                return 'business';
            case 'Tidak Aktif':
                return 'unactive';
            default:
                return $accountsType;
        }
    }

    public static function mapDescription($log, array $changes, string $modelName): string
    {
        $userName = ucfirst($log->user->first_name) . ' ' . ucfirst($log->user->last_name);
        $description = '';

        switch ($log->action) {
            case 'CREATE':
                $description = self::mapCreateDescription($log, $modelName, $changes, $userName);
                break;
            case 'UPDATE':
                $description = self::mapUpdateDescription($log, $modelName, $changes, $userName);
                break;
            case 'DELETE':
                $description = self::mapDeleteDescription($log, $modelName, $changes, $userName);
                break;
        }

        return $description;
    }

    private static function mapCreateDescription($log, string $modelName, array $changes, string $userName): string
    {
        switch ($modelName) {
            case 'users_companies':
                $userCompaniesName = UsersCompany::getCompaniesNameById($log->changes['id']['new']);
                return 'Perusahaan ' . $userCompaniesName . ' dibuat oleh ' . $userName;

            case 'user_invitations':
                $userCompaniesName = $log->user->company->name ??  '';
                return $userName . ' mengundang ' . $changes['email']['new'] . ' untuk bergabung di Perusahaan ' . $userCompaniesName;

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
                return $userName . ' menambahkan data Deals ' . $dealsName . ' sebesar Rp' . $valueEstimated;
                
            case 'customers_companies':
                return $userName . ' menambahkan data Perusahaan ' . $changes['name']['new'];

            case 'products':
                return $userName . ' menambahkan data Produk ' . $changes['name']['new'];
        }

        return '';
    }

    private static function mapUpdateDescription($log, string $modelName, array $changes, string $userName): string
    {
        switch ($modelName) {
            case 'users_companies':
                $userCompaniesName = UsersCompany::getCompaniesNameById($changes['id']['new']);
                return $userName . ' memperbarui data Perusahaan ' . $userCompaniesName;

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

            case 'customers_companies':
                $userCompaniesName = UsersCompany::getCompaniesNameById($changes['id']['new']);
                return $userName . ' memperbarui data Perusahaan ' . $userCompaniesName;
                
            case 'products': 
                $productName = Product::getProductNameById($changes['id']['new']);
                $quantityOld = $changes['quantity']['old']?? null;
                $quantityNew = $changes['quantity']['new']?? null;
                if (isset($quantityNew)){
                    return $userName . ' memperbarui data jumlah Produk ' . $productName . ' dari ' . $quantityOld . ' menjadi ' . $quantityNew;
                }

                return $userName . ' memperbarui data Produk ' . $productName;
        }

        return '';
    }

    private static function mapDeleteDescription($log, string $modelName, array $changes, string $userName): string
    {
        switch ($modelName) {
            case 'users_companies':
                $userCompaniesName = UsersCompany::getCompaniesNameById($changes['id']['new']);
                return 'Perusahaan ' . $userCompaniesName . ' dihapus oleh ' . $userName;

            case 'users':
                return $userName . ' menghapus akunnya';

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

            case 'customers_companies':
                $userCompaniesName = UsersCompany::getCompaniesNameById($changes['id']['new']);
                return $userName . ' menghapus data Perusahaan ' . $userCompaniesName;

            case 'products':
                $productName = Product::getProductNameById($changes['id']['new']);
                return $userName . ' menghapus data Produk ' . $productName;
        }

        return '';
    }

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
        if (isset($changes['stage'])){
            $stageOld = $changes['stage']['old'] ? self::mapStageDeal($changes['stage']['old']) : null;
            $stageNew = $changes['stage']['new'] ? self::mapStageDeal($changes['stage']['new']) : null;
    
            if (isset($stageNew)) {
                if ($stageNew !== 'tercapai' && $stageNew !== 'gagal') {
                    return $userName . ' memindahkan tahap Deals ' . $dealsName .
                        ' dari ' . $stageOld . ' menjadi ' . $stageNew;
                
                } elseif ($stageNew == 'tercapai') {
                    $valueActual = number_format($changes['value_actual']['new'], 0, ',', '.')?? '';
                    return 'Deals ' . $dealsName . ' sebesar Rp' . $valueActual . 
                        ' berhasil tercapai oleh ' . $userName;
                    
                } elseif ($stageNew == 'gagal') {
                    $valueEstimated = number_format($changes['value_estimated']['new'], 0, ',', '.')?? '';
                    return 'Deals ' . $dealsName . ' dengan perkiraan sebesar Rp' . $valueEstimated . ' gagal didapatkan oleh ' . $userName;
                }
            }
        }

        return $userName . ' memperbarui data Deals ' . $dealsName;
    }
}
