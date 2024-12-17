<?php

namespace App\Http\Controllers;

use App\Helpers\ActionMapperHelper;
use App\Http\Resources\ApiResponseResource;
use App\Models\AccountsType;
use App\Models\UsersCompany;
use App\Traits\Filter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountsTypeController extends Controller
{
    use Filter;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return new ApiResponseResource(
                false,
                'Unauthorized',
                null
            );
        }

        try {
            $query = AccountsType::with('userCompany');

            // $query = $this->applyFiltersAccountsType($request, $query);
            $accountsTypes = $this->applyFilters($request, $query);
            if (!$accountsTypes) {
                return new ApiResponseResource(
                    false,
                    'Data pelanggan tidak ditemukan',
                    null
                );
            }

            $accountsTypes->getCollection()->transform(function ($accountsType) {
                $accountsType->account_type = ActionMapperHelper::mapAccountsTypes($accountsType->account_type);
                return $accountsType;
            });

            return new ApiResponseResource(
                true,
                'Daftar pelanggan',
                $accountsTypes
            );
        } catch (\Exception $e) {
            return new ApiResponseResource(
                false,
                $e->getMessage(),
                null
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $accountsTypeId)
    {
        $accountsType = AccountsType::find($accountsTypeId);
        if (!$accountsType) {
            return new ApiResponseResource(
                false,
                'Data pelanggan tidak ditemukan.',
                null
            );
        }

        $validator = Validator::make($request->all(), [
            'account_type' => 'sometimes|required|in:Percobaan,Reguler,Profesional,Bisnis,Tidak Aktif',
            'user_company_id' => 'sometimes|required',
            'quantity' => 'required_if:account_type,Percobaan,Reguler,Profesional,Bisnis|prohibited_if:account_type,Tidak Aktif|nullable|numeric|min:1',
            'category' => 'required_if:account_type,Percobaan,Reguler,Profesional,Bisnis|prohibited_if:account_type,Tidak Aktif|nullable|in:Hari,Bulan,Tahun',
        ], [
            'account_type.required' => 'Tipe pelanggan tidak boleh kosong',
            'account_type.in' => 'Tipe pelanggan harus pilih salah satu: Percobaan, Reguler, Profesional, Bisnis, atau Tidak Aktif',
            'user_company_id.required' => 'Nama perusahaan tidak boleh kosong',
            'quantity.required_if' => 'Batas langganan tidak boleh kosong jika tipe akun selain Tidak Aktif',
            'quantity.prohibited_if' => 'Batas langganan tidak boleh diisi jika tipe akun Tidak Aktif',
            'quantity.numeric' => 'Batas langganan harus berupa angka',
            'quantity.min' => 'Batas langganan minimal berisi 1', 
            'category.required_if' => 'Batas langganan tidak boleh kosong jika tipe akun selain Tidak Aktif', 
            'category.prohibited_if' => 'Batas langganan tidak boleh diisi jika tipe akun Tidak Aktif', 
            'category.in' => 'Batas langganan harus pilih salah satu: Hari, Bulan, Tahun', 
        ]);
        if ($validator->fails()) {
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null
            );
        }

        $accountsTypeData = $request->all();
        if (isset($accountsTypeData['account_type'])) {
            $accountsTypeData['account_type'] = ActionMapperHelper::mapAccountsTypesToDatabase($accountsTypeData['account_type']);
        }

        if (isset($accountsTypeData['category']) && isset($accountsTypeData['quantity'])) {
            $accountsTypeData['start_date'] = now();
            if ($accountsType->account_type === $accountsTypeData['account_type']) {
                $accountsTypeData['start_date'] = $accountsType->start_date;
            }

            $quantity = (int)$accountsTypeData['quantity'];
            switch ($accountsTypeData['category']) {
                case 'hari':
                    $endDate = now()->addDays($quantity);
                    break;
                case 'bulan':
                    $endDate = now()->addMonths($quantity);
                    break;
                case 'tahun':
                    $endDate = now()->addYears($quantity);
                    break;
                default:
                    $endDate = null;
            }

            $accountsTypeData['end_date'] = $endDate;
        } else {
            $accountsTypeData['start_date'] =  null;
            $accountsTypeData['end_date'] = null;
        }

        try {
            $accountsType = AccountsType::updateAccountsType($accountsTypeData, $accountsTypeId);
            return new ApiResponseResource(
                true,
                "Data pelanggan berhasil diubah",
                $accountsType
            );
        } catch (\Exception $e) {
            return new ApiResponseResource(
                false,
                $e->getMessage(),
                null
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
