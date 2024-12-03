<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomersCompaniesReport extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    protected $fillable = [
        'id',
        'user_company_id',
        'added_customers_companies',
        'removed_customers_companies',
        'total_customers_companies_cold',
        'total_customers_companies_warm',
        'total_customers_companies_hot',
        'total_customers_companies',
        'date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * Get status data.
     */
    public static function getStatusData($userCompanyId, $category, $status, $group_by, $year, $startYear = null)
    {
        $statusFields = [
            'rendah' => [
                'leads' => 'total_leads_cold',
                'contact' => 'total_contact_cold',
                'perusahaan_pelanggan' => 'total_customers_companies_cold'
            ],
            'sedang' => [
                'leads' => 'total_leads_warm',
                'contact' => 'total_contact_warm',
                'perusahaan_pelanggan' => 'total_customers_companies_warm'
            ],
            'tinggi' => [
                'leads' => 'total_leads_hot',
                'contact' => 'total_contact_hot',
                'perusahaan_pelanggan' => 'total_customers_companies_hot'
            ]
        ];

        $query = self::query()
            ->where('user_company_id', $userCompanyId);

        if ($group_by === 'bulanan') {
            $query->selectRaw("MONTH(date) as period")
                ->whereYear('date', $year)
                ->groupBy('period')
                ->orderBy('period');
        } else {
            $query->selectRaw("YEAR(date) as period")
                ->whereYear('date', '>=', $startYear)
                ->groupBy('period')
                ->orderBy('period');
        }

        if ($status === 'semua') {
            $query->selectRaw(
                "SUM({$statusFields['rendah'][$category]}) as rendah, 
                SUM({$statusFields['sedang'][$category]}) as sedang, 
                SUM({$statusFields['tinggi'][$category]}) as tinggi"
            );
        } else {
            $field = $statusFields[$status][$category];
            $query->selectRaw("SUM($field) as total");
        }

        return $query->get();
    }
}
