<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponseResource;
use App\Imports\CustomersCompanyImport;
use App\Imports\CustomersImport;
use App\Imports\ProductsImport;
use App\Models\Customer;
use App\Models\CustomersCompany;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function import(Request $request, $type)
    {
        try {
            $user = auth()->user();
            $request->validate([
                'file' => 'required|mimes:xlsx,csv|max:2048',
            ], [
                'file.required' => 'Dokumen tidak boleh kosong.',
                'file.mimes' => 'Dokumen harus sesuai format.',
                'file.max' => 'Ukuran Dokumen maksimal 2mb.',
            ]);
            
            switch ($type) {
                case 'leads':
                    $import = new CustomersImport($user->email, 'leads');
                    $model = 'customer';
                    break;
                
                case 'contact':
                    $import = new CustomersImport($user->email, 'contact');
                    $model = 'customer';
                    break;

                case 'customers_companies':
                    $import = new CustomersCompanyImport($user->email);
                    $model = 'customers_companies';
                    break;

                case 'products':
                    $import = new ProductsImport($user->email);
                    $model = 'product';
                    break;

                default:
                    return new ApiResponseResource(
                        false, 
                        "Invalid request '../{$type}'",
                        null
                    );
            }

            $originalFileName =$request->file('file')->getClientOriginalName();
            Excel::import($import, $request->file('file'));

            $validData = $import->getValidData();
            $failedData = $import->getFailedData();
            $summaryData = $import->getSummaryData();

            $perPage = 25;
            $page = LengthAwarePaginator::resolveCurrentPage();
            $validDataPaginated = new LengthAwarePaginator(
                array_slice($validData, ($page - 1) * $perPage, $perPage), 
                count($validData), 
                $perPage, 
                $page, 
                ['path' => LengthAwarePaginator::resolveCurrentPath()]
            );
            $failedDataPaginated = new LengthAwarePaginator(
                array_slice($failedData, ($page - 1) * $perPage, $perPage), 
                count($failedData), 
                $perPage, 
                $page, 
                ['path' => LengthAwarePaginator::resolveCurrentPath()]
            );

            Carbon::setLocale('id');
            $formattedDate = now()->translatedFormat('l, d F Y');

            if (empty($failedData)) {
                foreach ($validData as $data) {
                    switch ($model) {
                        case 'customer':
                            Customer::createCustomer($data);
                            break;
                        case 'customers_companies':
                            CustomersCompany::createCustomersCompany($data);
                            break;
                        case 'product':
                            Product::createProduct($data);
                            break;
                    }
                }

                return new ApiResponseResource(
                    true,
                    'Berhasil import data!',
                    [
                        'file' => $originalFileName,
                        'data_type' => $model,
                        'date' => $formattedDate,
                        'summaryData' => $summaryData,
                        'validData' => $validDataPaginated,
                    ]
                );
            }

            return new ApiResponseResource(
                false,
                'Terdapat data yang rusak',
                [
                    'file' => $originalFileName,
                    'data_type' => $model,
                    'date' => $formattedDate,
                    'summaryData' => $summaryData,
                    'failedData' => $failedDataPaginated,
                ]
            );
        
        } catch(\Exception $e){
            return new ApiResponseResource(
                false,
                $e->getMessage(),
                null
            );
        }
    }
}
