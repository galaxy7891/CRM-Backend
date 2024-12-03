<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResponseResource;
use App\Models\CustomersCompaniesReport;
use App\Models\CustomersReport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    
     /**
     * Get conversion contact data for the chart.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */    
    public function getConversionContact(Request $request)
    {
        try {
            $user = auth()->user();
            $currentYear = now()->year;

            $group_by = $request->input('group_by', 'bulanan');
            $year = $request->input('year', $currentYear); 

            if ($group_by === 'bulanan') {

                $data = CustomersReport::getMonthlyConversionContact($user->user_company_id, $year);
                $months = [
                    'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
                    'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des',
                ];

                $resultData = collect($months)->map(function ($month, $index) use ($data) {
                    $monthIndex = $index + 1; 
                    $found = $data->firstWhere('month', $monthIndex);

                    return $found ? (int) $found->total_convert : 0;
                });

                $result = [
                    'categories' => $months,
                    'series' => [
                        [
                            'name' => 'Konversi',
                            'data' => $resultData
                        ]
                    ]
                ];

            } else if ($group_by === 'tahunan') {
                $startYear = $currentYear - 4;

                $data = CustomersReport::getYearlyConversionContact($user->user_company_id, $startYear);
                $years = range($startYear, $currentYear);

                $resultData = collect($years)->map(function ($year) use ($data) {
                    $found = $data->firstWhere('year', $year);

                    return $found ? (int) $found->total_convert : 0;
                });

                $result = [
                    'categories' => $years,
                    'series' => [
                        [
                            'name' => 'Konversi',
                            'data' => $resultData
                        ]
                    ]
                ];
            }

            return new ApiResponseResource(
                true, 
                'Laporan konversi kontak',
                $result
            );
        } catch (\Exception $e){
            return new ApiResponseResource(
                true,
                $e->getMessage(),
                null
            );
        }
    }


     /**
     * Get status report data for the chart.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */    
    public function getStatusReport(Request $request)
    {
        try {
            $user = auth()->user();
            $userCompanyId = $user->user_company_id;
            $currentYear = now()->year;

            $category = $request->input('category', 'leads');
            $status = $request->input('status', 'semua');
            $group_by = $request->input('group_by', 'bulanan');
            $year = $request->input('year', $currentYear);
            
            if ($category === 'leads' || $category === 'kontak'){
                $model = CustomersReport::class;
            } else{
                $model = CustomersCompaniesReport::class;
            }

            if ($group_by === 'tahunan') {
                $startYear = $currentYear - 4;
                $data = $model::getStatusData($userCompanyId, $category, $status, $group_by, $year, $startYear);
                $categories = range($startYear, $currentYear);
            } else {
                $data = $model::getStatusData($userCompanyId, $category, $status, $group_by, $year);
                $categories = [
                    'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
                    'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des',
                ];
            }

            $resultData = collect($categories)->map(function ($period, $index) use ($data, $status, $group_by) {
                $periodKey = $group_by === 'bulanan' ? $index + 1 : $period;
                $found = $data->firstWhere('period', $periodKey);
                
                if ($status === 'semua') {
                    return [
                        'rendah' => $found ? (int) $found->rendah : 0,
                        'sedang' => $found ? (int) $found->sedang : 0,
                        'tinggi' => $found ? (int) $found->tinggi : 0,
                    ];
                } else {
                    return [
                        "$status" => $found ? (int) $found->total : 0,
                    ];
                }
            });

            $series = ($status === 'semua') ? [
                [
                    'name' => 'Rendah',
                    'data' => $resultData->pluck('rendah')->toArray()
                ],
                [
                    'name' => 'Sedang',
                    'data' => $resultData->pluck('sedang')->toArray()
                ],
                [
                    'name' => 'Tinggi',
                    'data' => $resultData->pluck('tinggi')->toArray()
                ]
            ] : [
                [
                    'name' => ucfirst($status),
                    'data' => $resultData->pluck($status)->toArray()
                ]
            ];

            $result = [
                'categories' => $categories,
                'series' => $series
            ];

            return new ApiResponseResource(
                true, 
                'Laporan berdasarkan status',
                $result
            );

        } catch (\Exception $e) {
            return new ApiResponseResource(
                false,
                $e->getMessage(),
                null
            );
        }
    }
}
