<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponseResource;
use App\Imports\CustomersImport;
use App\Imports\OrganizationsImport;
use App\Imports\ProductsImport;
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
                'file.required' => 'File tidak boleh kosong.',
                'file.mimes' => 'File harus sesuai format.',
                'file.max' => 'Ukuran file maksimal 2mb.',
            ]);

            switch ($type) {
                case 'leads':
                    $model = 'customer';
                    $import = new CustomersImport($user->email, 'leads');
                    break;
                
                case 'contact':
                    $model = 'customer';
                    $import = new CustomersImport($user->email, 'contact');
                    break;

                case 'organization':
                    $import = new OrganizationsImport($user->email);
                    $model = 'organization';
                    break;

                case 'product':
                    $import = new ProductsImport($user->email);
                    $model = 'product';
                    break;

                default:
                    return new ApiResponseResource(
                        false, 
                        'Invalid import type.', 
                        []
                    );
            }

            Excel::import($import, $request->file('file'));

            $validData = $import->getValidData();
            $invalidData = $import->getInvalidData();
            $summaryCounts = $import->getsummaryCounts();

            $perPage = 25;
            $page = LengthAwarePaginator::resolveCurrentPage();
            $validDataPaginated = new LengthAwarePaginator(
                array_slice($validData, ($page - 1) * $perPage, $perPage), 
                count($validData), 
                $perPage, 
                $page, 
                ['path' => LengthAwarePaginator::resolveCurrentPath()]
            );
            $invalidDataPaginated = new LengthAwarePaginator(
                array_slice($invalidData, ($page - 1) * $perPage, $perPage), 
                count($invalidData), 
                $perPage, 
                $page, 
                ['path' => LengthAwarePaginator::resolveCurrentPath()]
            );

            $data = [
                'model' => $model,
                'validData' => $validDataPaginated,
                'invalidData' => $invalidDataPaginated,
                'summaryCounts' => $summaryCounts,
            ];

            if (empty($invalidData)) {
                return new ApiResponseResource(
                    true,
                    'Data aman dan tidak ditemukan data rusak.',
                    $data
                );
            }

            return new ApiResponseResource(
                false,
                'Terdapat data yang rusak',
                $data
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
