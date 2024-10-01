<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    /*************  ✨ Codeium Command ⭐  *************/
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /******  c153a343-a1bf-4ce5-bd4b-47395dc02e4e  *******/    public function index()
    {
        $customers = Customer::latest()->paginate(10);

        return new CustomerResource(true, 'List Customer', $customers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'organization_id' => 'required|uuid',
            'user_id' => 'required|uuid',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'customerCategory' => 'required|in:leads,contact',
            'job' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:hot,warm,cold',
            'birthdate' => 'nullable|date',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'required|string|max:15|unique:customers,phone',
            'owner' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'subdistrict' => 'nullable|string|max:255',
            'village' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:10',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //create customer

        $customers = Customer::create([
            'id' => Str::uuid(),
            'organization_id' => $request->organization_id,
            'user_id' => $request->user_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'customerCategory' => $request->customerCategory,
            'job' => $request->job,
            'description' => $request->description,
            'status' => $request->status,
            'birthdate' => $request->birthdate,
            'email' => $request->email,
            'phone' => $request->phone,
            'owner' => $request->owner,
            'address' => $request->address,
            'country' => $request->country,
            'city' => $request->city,
            'subdistrict' => $request->subdistrict,
            'village' => $request->village,
            'zip_code' => $request->zip_code,
        ]);

        return new CustomerResource(true, 'Data Customer Berhasil Ditambahkan!', $customers);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $customers = Customer::find($id);
        if (is_null($customers)) {
            return new CustomerResource(false, 'Data Customer Tidak Ditemukan!', null);
        }
        return new CustomerResource(true, 'Data Customer Ditemukan!', $customers);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'organization_id' => 'required|uuid',
            'user_id' => 'required|uuid',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'customerCategory' => 'required|in:leads,contact',
            'job' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:hot,warm,cold',
            'birthdate' => 'nullable|date',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'required|string|max:15|unique:customers,phone',
            'owner' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'subdistrict' => 'nullable|string|max:255',
            'village' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:10',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Check if customer exists
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json(['message' => 'Customer tidak ditemukan'], 404);
        }

        $customer->update([
            'organization_id' => $request->organization_id,
            'user_id' => $request->user_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'customerCategory' => $request->customerCategory,
            'job' => $request->job,
            'description' => $request->description,
            'status' => $request->status,
            'birthdate' => $request->birthdate,
            'email' => $request->email,
            'phone' => $request->phone,
            'owner' => $request->owner,
            'address' => $request->address,
            'country' => $request->country,
            'city' => $request->city,
            'subdistrict' => $request->subdistrict,
            'village' => $request->village,
            'zip_code' => $request->zip_code,
        ]);

        return new CustomerResource(true, 'Data Customer Berhasil Diubah!', $customer);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Customer tidak ditemukan', 'data' => null], 404);
        }

        // Delete the customer
        $customer->delete();

        // Return response with first and last name
        return new CustomerResource(true, "Customer {$customer->first_name} {$customer->last_name} Berhasil Dihapus!", null);
    }
}
