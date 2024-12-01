<?php

namespace App\Helpers;

use App\Models\CustomersCompaniesReport;
use App\Models\CustomersReport;

class ReportsHelper
{   
    public static function recordAddedLeads($userCompanyId, $customer)
    {
        $date = now()->toDateString();
        $report = CustomersReport::firstOrCreate(
            ['date' => $date, 'user_company_id' => $userCompanyId],
            self::defaultCustomersReport($userCompanyId)
        );

        $report->increment('added_leads');
        $report->increment('total_leads');
        
        if ($customer->status === 'cold') {
            $report->increment('total_leads_cold');
        } elseif ($customer->status === 'warm') {
            $report->increment('total_leads_warm');
        } elseif ($customer->status === 'hot') {
            $report->increment('total_leads_hot');
        }

        $report->save();
    }

    public static function recordAddedContact($userCompanyId, $customer)
    {
        $date = now()->toDateString();
        $report = CustomersReport::firstOrCreate(
            ['date' => $date, 'user_company_id' => $userCompanyId],
            self::defaultCustomersReport($userCompanyId)
        );
        
        $report->increment('added_contact');
        $report->increment('total_contact');

        if ($customer->status === 'cold') {
            $report->increment('total_contact_cold');
        } elseif ($customer->status === 'warm') {
            $report->increment('total_contact_warm');
        } elseif ($customer->status === 'hot') {
            $report->increment('total_contact_hot');
        }

        $report->save();
    }

    public static function recordConversionContact($userCompanyId, $customer)
    {
        $date = now()->toDateString();
        $report = CustomersReport::firstOrCreate(
            ['date' => $date, 'user_company_id' => $userCompanyId],
            self::defaultCustomersReport($userCompanyId)
        );
        
        $report->increment('converted_contact');
        $report->increment('total_contact');
        $report->decrement('total_leads', max(0, $report->total_leads - 1));

        if ($customer->status === 'cold') {
            $report->decrement('total_leads_cold', max(0, $report->total_leads_cold - 1));
            $report->increment('total_contact_cold');
        } elseif ($customer->status === 'warm') {
            $report->decrement('total_leads_warm', max(0, $report->total_leads_warm - 1));
            $report->increment('total_contact_warm');
        } elseif ($customer->status === 'hot') {
            $report->decrement('total_leads_hot', max(0, $report->total_leads_hot - 1));
            $report->increment('total_contact_hot');
        }

        $report->save();
    }

    //not use
    // public static function recordRemovedLeads($userCompanyId, $customer)
    // {
    //     $date = now()->toDateString();
    //     $report = CustomersReport::firstOrCreate(
    //         ['date' => $date, 'user_company_id' => $userCompanyId],
    //         self::defaultCustomersReport()
    //     );
        
    //     $report->increment('removed_leads');
    //     $report->decrement('total_leads', max(0, $report->total_leads - 1));

    //     if ($customer->status === 'cold') {
    //         $report->decrement('total_leads_cold', max(0, $report->total_leads_cold - 1));
    //     } elseif ($customer->status === 'warm') {
    //         $report->decrement('total_leads_warm', max(0, $report->total_leads_warm - 1));
    //     } elseif ($customer->status === 'hot') {
    //         $report->decrement('total_leads_hot', max(0, $report->total_leads_hot - 1));
    //     }

    //     $report->save();
    // }

    //not use
    // public static function recordRemovedContact($userCompanyId, $customer)
    // {
    //     $date = now()->toDateString();
    //     $report = CustomersReport::firstOrCreate(
    //         ['date' => $date, 'user_company_id' => $userCompanyId],
    //         self::defaultCustomersReport()
    //     );
        
    //     $report->increment('removed_contact');
    //     $report->decrement('total_contact', max(0, $report->total_contact - 1));

    //     if ($customer->status === 'cold') {
    //         $report->decrement('total_contact_cold', max(0, $report->total_contact_cold - 1));
    //     } elseif ($customer->status === 'warm') {
    //         $report->decrement('total_contact_warm', max(0, $report->total_contact_warm - 1));
    //     } elseif ($customer->status === 'hot') {
    //         $report->decrement('total_contact_hot', max(0, $report->total_contact_hot - 1));
    //     }

    //     $report->save();
    // }

    private static function defaultCustomersReport($userCompanyId)
    {
        return [
            'user_company_id' => $userCompanyId,
            'added_leads' => 0,
            'added_contact' => 0,
            'converted_contact' => 0,
            'removed_leads' => 0,
            'removed_contact' => 0,
            'total_leads_cold' => 0,
            'total_leads_warm' => 0,
            'total_leads_hot' => 0,
            'total_leads' => 0,
            'total_contact_cold' => 0,
            'total_contact_warm' => 0,
            'total_contact_hot' => 0,
            'total_contact' => 0,
        ];
    }

    public static function recordAddedCompany($userCompanyId, $company)
    {
        $date = now()->toDateString();
        $report = CustomersCompaniesReport::firstOrCreate(
            ['date' => $date, 'user_company_id' => $userCompanyId],
            self::defaultCustomersCompaniesReport($userCompanyId)
        );

        $report->increment('added_customers_companies');
        $report->increment('total_customers_companies');

        if ($company->status === 'cold') {
            $report->increment('total_customers_companies_cold');
        } elseif ($company->status === 'warm') {
            $report->increment('total_customers_companies_warm');
        } elseif ($company->status === 'hot') {
            $report->increment('total_customers_companies_hot');
        }

        $report->save();
    }

    public static function recordRemovedCompany($userCompanyId, $company)
    {
        $date = now()->toDateString();
        $report = CustomersCompaniesReport::firstOrCreate(
            ['date' => $date, 'user_company_id' => $userCompanyId],
            self::defaultCustomersCompaniesReport($userCompanyId)
        );

        $report->increment('removed_customers_companies');
        $report->decrement('total_customers_companies', max(0, $report->total_customers_companies - 1));

        if ($company->status === 'cold') {
            $report->decrement('total_customers_companies_cold', max(0, $report->total_customers_companies_cold - 1));
        } elseif ($company->status === 'warm') {
            $report->decrement('total_customers_companies_warm', max(0, $report->total_customers_companies_warm - 1));
        } elseif ($company->status === 'hot') {
            $report->decrement('total_customers_companies_hot', max(0, $report->total_customers_companies_hot - 1));
        }

        $report->save();
    }

    private static function defaultCustomersCompaniesReport($userCompanyId)
    {
        return [
            'user_company_id' => $userCompanyId,
            'added_customers_companies' => 0,
            'removed_customers_companies' => 0,
            'total_customers_companies_cold' => 0,
            'total_customers_companies_warm' => 0,
            'total_customers_companies_hot' => 0,
            'total_customers_companies' => 0,
        ];
    }
}
