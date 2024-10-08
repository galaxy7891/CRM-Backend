<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Deal;
use Illuminate\Http\Request;
use App\Http\Resources\DealResource;
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
            return new DealResource(
                true, // success
                'Daftar Deal', // message
                $deals // data
            );
        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'name' => 'required|string|max:255',
            'deals_customer' => 'required|string|max:255',
            'description' => 'nullable|string',
            'tag' => 'required|string|max:255',
            'stage' => 'required|in:qualificated,proposal,negotiate,won,lose',
            'open_date' => 'required|date',
            'close_date' => 'nullable|date',
            'expected_close_date' => 'required|date',
            'payment_expected' => 'nullable|numeric',
            'payment_category' => 'required|in:once,hours,daily,weekly,monthly,quarter,yearly',
            'payment_duration' => 'nullable|integer',
            'owner' => 'required|string|max:255',
        ], [
            'customer_id.required' => 'ID pelanggan wajib diisi.',
            'customer_id.exists' => 'ID pelanggan tidak ditemukan.',
            'name.required' => 'Nama wajib diisi.',
            'name.string' => 'Nama harus berupa huruf.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'deals_customer.required' => 'Pelanggan wajib diisi.',
            'deals_customer.string' => 'Pelanggan harus berupa huruf.',
            'deals_customer.max' => 'Pelanggan maksimal 255 karakter.',
            'description.string' => 'Deskripsi harus berupa huruf.',
            'tag.required' => 'Tag wajib diisi.',
            'tag.string' => 'Tag harus berupa huruf.',
            'tag.max' => 'Tag maksimal 255 karakter.',
            'stage.required' => 'Status wajib diisi.',
            'stage.in' => 'Status tidak valid.',
            'open_date.required' => 'Tanggal buka buka wajib diisi.',
            'open_date.date' => 'Tanggal buka buka harus berupa tanggal.',
            'close_date.date' => 'Tanggal tutup harus berupa tanggal.',
            'expected_close_date.required' => 'Jatuh tempo wajib diisi.',
            'expected_close_date.date' => 'Jatuh tempo harus berupa tanggal.',
            'payment_expected.numeric' => 'Pembayaran yang diharapkan harus berupa angka.',
            'payment_category.required' => 'Kategori pembayaran wajib diisi.',
            'payment_category.in' => 'Kategori pembayaran tidak valid.',
            'payment_duration.integer' => 'Durasi pembayaran harus berupa angka.',
            'owner.required' => 'Pemilik wajib diisi.',
            'owner.string' => 'Pemilik harus berupa huruf.',
            'owner.max' => 'Pemilik maksimal 255 karakter.',
        ]);

        // check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => null
            ], 400);
        }

        // create customer 
        try {
            $deal = Deal::createDeal($request->all());
            return new DealResource(
                true, // success
                'Deal ditambahkan', // message
                $deal // data
            );
        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
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
                return response()->json([
                    'success ' => false,
                    'message' => 'Data Customer Tidak Ditemukan!',
                    'data' => null
                ], 404);
            }
            return new DealResource(
                true, // success
                'Data Customer Ditemukan!', // message
                $deal // data
            );
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
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
            return response()->json([
                'success' => false,
                'message' => 'Deal tidak ditemukan',
                'data' => null
            ], 404);
        }


        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'name' => 'required|string|max:255',
            'deals_customer' => 'required|string|max:255',
            'description' => 'nullable|string',
            'tag' => 'required|string|max:255',
            'stage' => 'required|in:qualificated,proposal,negotiate,won,lose',
            'open_date' => 'required|date',
            'close_date' => 'nullable|date',
            'expected_close_date' => 'required|date',
            'payment_expected' => 'nullable|numeric',
            'payment_category' => 'required|in:once,hours,daily,weekly,monthly,quarter,yearly',
            'payment_duration' => 'nullable|integer',
            'owner' => 'required|string|max:255',
        ], [
            'customer_id.required' => 'ID pelanggan wajib diisi.',
            'customer_id.exists' => 'ID pelanggan tidak ditemukan.',
            'name.required' => 'Nama wajib diisi.',
            'name.string' => 'Nama harus berupa huruf.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'deals_customer.required' => 'Pelanggan wajib diisi.',
            'deals_customer.string' => 'Pelanggan harus berupa huruf.',
            'deals_customer.max' => 'Pelanggan maksimal 255 karakter.',
            'description.string' => 'Deskripsi harus berupa huruf.',
            'tag.required' => 'Tag wajib diisi.',
            'tag.string' => 'Tag harus berupa huruf.',
            'tag.max' => 'Tag maksimal 255 karakter.',
            'stage.required' => 'Status wajib diisi.',
            'stage.in' => 'Status tidak valid.',
            'open_date.required' => 'Tanggal buka buka wajib diisi.',
            'open_date.date' => 'Tanggal buka buka harus berupa tanggal.',
            'close_date.date' => 'Tanggal tutup harus berupa tanggal.',
            'expected_close_date.required' => 'Jatuh tempo wajib diisi.',
            'expected_close_date.date' => 'Jatuh tempo harus berupa tanggal.',
            'payment_expected.numeric' => 'Pembayaran yang diharapkan harus berupa angka.',
            'payment_category.required' => 'Kategori pembayaran wajib diisi.',
            'payment_category.in' => 'Kategori pembayaran tidak valid.',
            'payment_duration.integer' => 'Durasi pembayaran harus berupa angka.',
            'owner.required' => 'Pemilik wajib diisi.',
            'owner.string' => 'Pemilik harus berupa huruf.',
            'owner.max' => 'Pemilik maksimal 255 karakter.',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => null
            ], 422);
        }

        try {
            $deal = Deal::updateDeal($request->all(), $id);
            return new DealResource(
                true, // successs
                'Data Deal Berhasil Diubah!', // message
                $deal // data
            );
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
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
                return response()->json([
                    'success' => false,
                    'message' => 'Deal tidak ditemukan',
                    'data' => null
                ], 404);
            }

            // Delete the customer
            $deal->delete();

            // Return response with first and last name
            return new DealResource(
                true, // success
                "Deal {$deal->name} Berhasil Dihapus!", // message
                null // data
            );
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
