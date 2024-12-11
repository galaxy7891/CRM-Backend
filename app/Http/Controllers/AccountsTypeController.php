<?php

namespace App\Http\Controllers;

use App\Helpers\ActionMapperHelper;
use App\Http\Resources\ApiResponseResource;
use App\Models\AccountsType;
use App\Models\UsersCompany;
use App\Traits\Filter;
use Illuminate\Http\Request;

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

            $query = $this->applyFiltersAccountsType($request, $query);
            $accountTypes = $this->applyFilters($request, $query);
            if (!$accountTypes) {
                return new ApiResponseResource(
                    false,
                    'Data pelanggan tidak ditemukan',
                    null
                );
            }

            $accountTypes->getCollection()->transform(function ($accountType) {
                $accountType->account_type = ActionMapperHelper::mapAccountsTypes($accountType->account_type);
                return $accountType;
            });

            return new ApiResponseResource(
                true,
                'Daftar pelanggan',
                $accountTypes
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
