<?php

namespace App\Http\Controllers;

use App\Helpers\ActionMapperHelper;
use App\Http\Resources\ApiResponseResource;
use App\Models\Deal;
use App\Traits\Filter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DealController extends Controller
{
    use Filter;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Deal::query();
            
            $deals = $this->applyFilters($request, $query);
            $mappeddeals = $deals->map(function ($deal) {
                $deal->status = ActionMapperHelper::mapStatus($deal->status);
                return $deal;
            });

            return new ApiResponseResource(
                true,
                'Daftar deals',
                $mappeddeals
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
            'customer_id.required' => 'ID pelanggan tidak boleh kosong.',
            'customer_id.exists' => 'ID pelanggan tidak ditemukan.',
            'name.required' => 'Nama tidak boleh kosong.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'description.string' => 'Deskripsi harus berupa teks.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status harus pilih salah satu: hot, warm, atau cold.',
            'tag.string' => 'Tag harus berupa teks.',
            'tag.max' => 'Tag maksimal 255 karakter.',
            'stage.required' => 'Status tidak boleh kosong.',
            'stage.in' => 'Status harus pilih salah satu: qualificated,proposal,negotiate,won,lose.',
            'open_date.required' => 'Tanggal buka tidak boleh kosong.',
            'open_date.date' => 'Tanggal buka harus berupa tanggal.',
            'close_date.date' => 'Tanggal tutup harus berupa tanggal.',
            'expected_close_date.required' => 'Perkiraan tanggal tutup tidak boleh kosong.',
            'expected_close_date.date' => 'Perkiraan tanggal tutup harus berupa tanggal.',
            'value_estimated.numeric' => 'Perkiraan pembayaran harus berupa angka.',
            'value_estimated.max_digits' => 'Perkiraan pembayaran maksimal 20 digit.',
            'payment_category.required' => 'Kategori pembayaran tidak boleh kosong.',
            'payment_category.in' => 'Kategori pembayaran harus pilih salah satu: once,daily,monthly,yearly.',
            'payment_duration.integer' => 'Durasi pembayaran harus berupa angka.',
            'owner.required' => 'Pemilik tidak boleh kosong.',
            'owner.emaik' => 'Pemilik harus berupa email.',
            'owner.max' => 'Pemilik maksimal 100 karakter.',
        ]);
        if ($validator->fails()) {
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null
            );
        }

        try {
            $deal = Deal::createDeal($request->all());
            return new ApiResponseResource(
                true,
                'Data deals berhasil ditambahkan',
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
            $deals = Deal::findDealsById($id);
            if (is_null($deals)) {
                return new ApiResponseResource(
                    false,
                    'Data deals tidak ditemukan!',
                    null
                );
            }
            $deals->status = ActionMapperHelper::mapStatus($deals->status);
            
            return new ApiResponseResource(
                true,
                'Data deals', 
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $deal = Deal::findDealsById($id);
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
            'customer_id.required' => 'ID pelanggan tidak boleh kosong.',
            'customer_id.exists' => 'ID pelanggan tidak ditemukan.',
            'name.required' => 'Nama tidak boleh kosong.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'description.string' => 'Deskripsi harus berupa teks.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status harus pilih salah satu: hot, warm, atau cold.',
            'tag.string' => 'Tag harus berupa teks.',
            'tag.max' => 'Tag maksimal 255 karakter.',
            'stage.required' => 'Status tidak boleh kosong.',
            'stage.in' => 'Status harus pilih salah satu: qualificated,proposal,negotiate,won,lose.',
            'open_date.required' => 'Tanggal buka tidak boleh kosong.',
            'open_date.date' => 'Tanggal buka harus berupa tanggal.',
            'close_date.date' => 'Tanggal tutup harus berupa tanggal.',
            'expected_close_date.required' => 'Perkiraan tanggal tutup tidak boleh kosong.',
            'expected_close_date.date' => 'Perkiraan tanggal tutup harus berupa tanggal.',
            'value_estimated.numeric' => 'Perkiraan pembayaran harus berupa angka.',
            'value_estimated.max_digits' => 'Perkiraan pembayaran maksimal 20 digit.',
            'payment_category.required' => 'Kategori pembayaran tidak boleh kosong.',
            'payment_category.in' => 'Kategori pembayaran harus pilih salah satu: once,daily,monthly,yearly.',
            'payment_duration.integer' => 'Durasi pembayaran harus berupa angka.',
            'owner.required' => 'Pemilik tidak boleh kosong.',
            'owner.emaik' => 'Pemilik harus berupa email.',
            'owner.max' => 'Pemilik maksimal 100 karakter.',
        ]);
        if ($validator->fails()) {
            return new ApiResponseResource(
                false, 
                $validator->errors(),
                null 
            );
        }

        try {
            $deals = Deal::updateDeal($request->all(), $id);
            return new ApiResponseResource(
                true, 
                'Data deals berhasil diubah!', 
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
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id = $request->input('id', []);
        if (empty($id)) {
            return new ApiResponseResource(
                true,
                "Pilih data yang ingin dihapus terlebih dahulu",
                null
            );
        }
        
        try {
            $deletedCount = Deal::whereIn('id', $id)->delete();
            if ($deletedCount > 0) {
                return new ApiResponseResource(
                    true,
                    $deletedCount . ' data deals berhasil dihapus',
                    null
                );
            }

        return new ApiResponseResource(
                false,
                'Data deals tidak ditemukan',
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
