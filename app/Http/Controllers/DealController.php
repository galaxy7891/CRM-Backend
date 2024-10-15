<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResponseResource;
use App\Models\Deal;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DealController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $deals = Deal::latest()->paginate(25);
            return new ApiResponseResource(
                true,
                'Daftar Deal',
                $deals
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
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'name' => 'required|string|max:100',
            'deals_customer' => 'required|string|max:100',
            'description' => 'nullable|string',
            'status' => 'required|in:hot,warm,cold',
            'tag' => 'nullable|string|max:255',
            'stage' => 'required|in:qualificated,proposal,negotiate,won,lose',
            'open_date' => 'required|date',
            'close_date' => 'nullable|date',
            'expected_close_date' => 'required|date',
            'value_estimated' => 'nullable|numeric|max_digits:20',
            'payment_category' => 'required|in:once,daily,monthly,yearly',
            'payment_duration' => 'nullable|integer',
            'owner' => 'required|email|max:100',
        ], [
            'customer_id.required' => 'ID pelanggan wajib diisi.',
            'customer_id.exists' => 'ID pelanggan tidak ditemukan.',
            'name.required' => 'Nama wajib diisi.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'deals_customer.required' => 'Pelanggan wajib diisi.',
            'deals_customer.string' => 'Pelanggan harus berupa teks.',
            'deals_customer.max' => 'Pelanggan maksimal 100 karakter.',
            'description.string' => 'Deskripsi harus berupa teks.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status harus pilih salah satu: hot, warm, atau cold.',
            'tag.string' => 'Tag harus berupa teks.',
            'tag.max' => 'Tag maksimal 255 karakter.',
            'stage.required' => 'Status wajib diisi.',
            'stage.in' => 'Status harus pilih salah satu: qualificated,proposal,negotiate,won,lose.',
            'open_date.required' => 'Tanggal buka wajib diisi.',
            'open_date.date' => 'Tanggal buka harus berupa tanggal.',
            'close_date.date' => 'Tanggal tutup harus berupa tanggal.',
            'expected_close_date.required' => 'Perkiraan tanggal tutup wajib diisi.',
            'expected_close_date.date' => 'Perkiraan tanggal tutup harus berupa tanggal.',
            'value_estimated.numeric' => 'Perkiraan pembayaran harus berupa angka.',
            'value_estimated.max_digits' => 'Perkiraan pembayaran maksimal 20 digit.',
            'payment_category.required' => 'Kategori pembayaran wajib diisi.',
            'payment_category.in' => 'Kategori pembayaran harus pilih salah satu: once,daily,monthly,yearly.',
            'payment_duration.integer' => 'Durasi pembayaran harus berupa angka.',
            'owner.required' => 'Pemilik wajib diisi.',
            'owner.emaik' => 'Pemilik harus berupa email.',
            'owner.max' => 'Pemilik maksimal 100 karakter.',
        ]);

        // check if validation fails
        if ($validator->fails()) {
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null
            );
        }

        // create customer 
        try {
            $deal = Deal::createDeal($request->all());
            return new ApiResponseResource(
                true,
                'Deal ditambahkan',
                $deal 
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
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $deal = Deal::find($id);
            if (is_null($deal)) {
                return new ApiResponseResource(
                    false,
                    'Data Customer Tidak Ditemukan!',
                    null
                );
            }

            return new ApiResponseResource(
                true,
                'Data Customer Ditemukan!', 
                $deal 
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Check if customer exists
        $deal = Deal::find($id);
        if (!$deal) {
            return new ApiResponseResource(
                false, 
                'Deal tidak ditemukan',
                null
            );
        }


        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'name' => 'required|string|max:100',
            'deals_customer' => 'required|string|max:100',
            'description' => 'nullable|string',
            'status' => 'required|in:hot,warm,cold',
            'tag' => 'nullable|string|max:255',
            'stage' => 'required|in:qualificated,proposal,negotiate,won,lose',
            'open_date' => 'required|date',
            'close_date' => 'nullable|date',
            'expected_close_date' => 'required|date',
            'value_estimated' => 'nullable|numeric|max_digits:20',
            'payment_category' => 'required|in:once,daily,monthly,yearly',
            'payment_duration' => 'nullable|integer',
            'owner' => 'required|email|max:100',
        ], [
            'customer_id.required' => 'ID pelanggan wajib diisi.',
            'customer_id.exists' => 'ID pelanggan tidak ditemukan.',
            'name.required' => 'Nama wajib diisi.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'deals_customer.required' => 'Pelanggan wajib diisi.',
            'deals_customer.string' => 'Pelanggan harus berupa teks.',
            'deals_customer.max' => 'Pelanggan maksimal 100 karakter.',
            'description.string' => 'Deskripsi harus berupa teks.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status harus pilih salah satu: hot, warm, atau cold.',
            'tag.string' => 'Tag harus berupa teks.',
            'tag.max' => 'Tag maksimal 255 karakter.',
            'stage.required' => 'Status wajib diisi.',
            'stage.in' => 'Status harus pilih salah satu: qualificated,proposal,negotiate,won,lose.',
            'open_date.required' => 'Tanggal buka wajib diisi.',
            'open_date.date' => 'Tanggal buka harus berupa tanggal.',
            'close_date.date' => 'Tanggal tutup harus berupa tanggal.',
            'expected_close_date.required' => 'Perkiraan tanggal tutup wajib diisi.',
            'expected_close_date.date' => 'Perkiraan tanggal tutup harus berupa tanggal.',
            'value_estimated.numeric' => 'Perkiraan pembayaran harus berupa angka.',
            'value_estimated.max_digits' => 'Perkiraan pembayaran maksimal 20 digit.',
            'payment_category.required' => 'Kategori pembayaran wajib diisi.',
            'payment_category.in' => 'Kategori pembayaran harus pilih salah satu: once,daily,monthly,yearly.',
            'payment_duration.integer' => 'Durasi pembayaran harus berupa angka.',
            'owner.required' => 'Pemilik wajib diisi.',
            'owner.emaik' => 'Pemilik harus berupa email.',
            'owner.max' => 'Pemilik maksimal 100 karakter.',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return new ApiResponseResource(
                false, 
                $validator->errors(),
                null 
            );
        }

        try {
            $deal = Deal::updateDeal($request->all(), $id);
            return new ApiResponseResource(
                true, 
                'Data Deal Berhasil Diubah!', 
                $deal 
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
    public function destroy($id)
    {
        try {

            // Check if deal exists
            $deal = Deal::find($id);
            if (!$deal) {
                return new ApiResponseResource(
                    false, 
                    'Deal tidak ditemukan',
                    null
                );
            }

            // Delete the customer
            $deal->delete();

            // Return response with first and last name
            return new ApiResponseResource(
                true, 
                "Deal {$deal->name} Berhasil Dihapus!", 
                null 
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
