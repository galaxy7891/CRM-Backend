<?php

namespace App\Http\Controllers;

use App\Helpers\ActionMapperHelper;
use App\Http\Resources\ApiResponseResource;
use App\Models\UsersCompany;
use Illuminate\Http\Request;

class AccountsTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    // {
    //     $user = auth()->user();
    //     if (!$user) {
    //         return new ApiResponseResource(
    //             false,
    //             'Unauthorized',
    //             null
    //         );
    //     }
        
    //     try {
    //         $query = UsersCompany::where('user_company_id', $user->user_company_id)
    //             ->where('id', '!=', $user->id);
            
    //         $employees = $this->applyFilters($request, $query);
    //         if (!$employees) {
    //             return new ApiResponseResource(
    //                 false,
    //                 'Data karyawan tidak ditemukan',
    //                 null
    //             );
    //         }

    //         $employees->getCollection()->transform(function ($employee) {
    //             $employee->role = ActionMapperHelper::mapRole($employee->role);
    //             $employee->gender = ActionMapperHelper::mapGender($employee->gender);
    //             return $employee;
    //         });
            
    //         return new ApiResponseResource(
    //             true,
    //             'Daftar Karyawan',
    //             $employees
    //         );
            
    //     } catch (\Exception $e) {
    //         return new ApiResponseResource(
    //             false,
    //             $e->getMessage(),
    //             null
    //         );
    //     }
    // }
    {}
    
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
