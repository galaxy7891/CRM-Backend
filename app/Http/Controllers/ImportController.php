<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponseResource;
use App\Imports\CustomerImport;
use App\Imports\OrganizationImport;
use App\Imports\ProductImport;
use App\Models\Customer;
use App\Models\Organization;
use App\Models\Product;
use Illuminate\Http\Request;
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
                'file.required' => 'File wajib diisi.',
                'file.mimes' => 'File harus sesuai format.',
                'file.max' => 'Ukuran file maksimal 2mb.',
            ]);

            switch ($type) {
                case 'customer':
                    $import = new CustomerImport($user->email);
                    $model = Customer::class;
                    break;

                case 'organization':
                    $import = new OrganizationImport($user->email);
                    $model = Organization::class;
                    break;

                case 'product':
                    $import = new ProductImport($user->email);
                    $model = Product::class;
                    break;

                default:
                    return new ApiResponseResource(false, 'Invalid import type.', []);
            }

            Excel::import($import, $request->file('file'));

            $validData = $import->getValidData();
            $invalidData = $import->getInvalidData();
            $summaryCounts = $import->getsummaryCounts();

            $data = [
                'validData' => $validData,
                'invalidData' => $invalidData,
                'summaryCounts' => $summaryCounts,
            ];

            if (empty($invalidData)) {

                return new ApiResponseResource(
                    true,
                    'Data aman dan tidak ditemukan data rusak.',
                    [
                        'validData' => $validData,
                        'invalidData' => $invalidData,
                        'summaryCounts' => $summaryCounts,
                    ]
                );
            }

            return new ApiResponseResource(
                false,
                'Terdapat data yang rusak',
                [
                    'validData' => $validData,
                    'invalidData' => $invalidData,
                    'summaryCounts' => $summaryCounts,
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
