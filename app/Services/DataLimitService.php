<?php

namespace App\Services;

use App\Models\AccountsType;
use App\Models\Customer;
use App\Models\CustomersCompany;
use App\Models\Deal;
use App\Models\Product;
use Illuminate\Support\Facades\Config;

class DataLimitService
{   
    public static function checkCustomersLimit($userCompanyId)
    {
        $account = self::getAccountType($userCompanyId);
        $limits = Config::get("account_limits.{$account}");
        
        $customersCount = (int) Customer::countCustomers($userCompanyId);
        $customersCompanyCount = (int) CustomersCompany::countCustomersCompany($userCompanyId);
        $totalCustomers = $customersCount + $customersCompanyCount;
        
        return [
            'isExceeded' => $totalCustomers >= $limits['customers'],
            'message' => "Jumlah Customer sudah mencapai limit (> {$limits['customers']}) untuk tipe akun Anda. Hubungi customer support untuk masalah lebih lanjut.",
        ];
    }

    public static function checkDealsLimit($userCompanyId)
    {
        $account = self::getAccountType($userCompanyId);
        $limits = Config::get("account_limits.{$account}");

        $dealsCount = (int) Deal::countDeals($userCompanyId);

        return [
            'isExceeded' => $dealsCount >= $limits['deals'],
            'message' => "Jumlah Deals sudah mencapai limit (> {$limits['deals']}) untuk tipe akun Anda. Hubungi customer support untuk masalah lebih lanjut.",
        ];
    }

    public static function checkProductsLimit($userCompanyId)
    {
        $account = self::getAccountType($userCompanyId);
        $limits = Config::get("account_limits.{$account}");

        $productCount = (int) Product::countProducts($userCompanyId);

        return [
            'isExceeded' => $productCount >= $limits['products'],
            'message' => "Jumlah Produk sudah mencapai limit (> {$limits['products']}) untuk tipe akun Anda. Hubungi customer support untuk masalah lebih lanjut.",
        ];
    }

    private static function getAccountType($userCompanyId)
    {
        $account = AccountsType::where('user_company_id', $userCompanyId)->first();
        return $account ? $account->account_type : 'trial';
    }
}
