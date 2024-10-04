<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $product = Product::latest()->paginate(10);

            return new ProductResource(
                true, // success
                'Daftar Product', // message
                $product // data
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:products,name|string|max:255',
            'category' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'photo_product' => 'nullable|max:2048',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.string' => 'Nama harus berupa string.',
            'name.max' => 'Nama terlalu panjang.',
            'category.required' => 'Kategori wajib diisi.',
            'category.string' => 'Kategori harus berupa string.',
            'category.max' => 'Kategori terlalu panjang.',
            'code.required' => 'Kode wajib diisi.',
            'code.string' => 'Kode harus berupa string.',
            'code.max' => 'Kode terlalu panjang.',
            'quantity.required' => 'Jumlah wajib diisi.',
            'quantity.numeric' => 'Jumlah harus berupa angka.',
            'quantity.min' => 'Jumlah harus lebih dari 0.',
            'unit.required' => 'Unit wajib diisi.',
            'unit.string' => 'Unit harus berupa string.',
            'unit.max' => 'Unit terlalu panjang.',
            'price.required' => 'Harga wajib diisi.',
            'price.numeric' => 'Harga harus berupa angka.',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => null
            ], 422);
        }

        try {
            $product = Product::createProduct($request->all());
            return new ProductResource(
                true, // success
                'Product berhasil ditambahkan', // message
                $product // data
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
            $product = Product::find($id);
            if (!$product) {
                return new ProductResource(false, 'Data Product Tidak Ditemukan!', null);
            }
            return new ProductResource(
                true, // success
                'Data Product Ditemukan!', // message
                $product // datas
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $product = product::find($id);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product Tidak Ditemukan',
                'data' => null
            ], 404);
        }
        $validator = Validator::make($request->all(), [
            [
                'name' => 'required|string|max:255',
                'category' => 'required|string|max:255',
                'code' => 'required|string|max:255',
                'quantity' => 'required|numeric|min:0',
                'unit' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'description' => 'nullable|string',
                'photo_product' => 'nullable|max:2048',
            ]
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.string' => 'Nama harus berupa string.',
            'name.max' => 'Nama terlalu panjang.',
            'category.required' => 'Kategori wajib diisi.',
            'category.string' => 'Kategori harus berupa string.',
            'category.max' => 'Kategori terlalu panjang.',
            'code.required' => 'Kode wajib diisi.',
            'code.string' => 'Kode harus berupa string.',
            'code.max' => 'Kode terlalu panjang.',
            'quantity.required' => 'Jumlah wajib diisi.',
            'quantity.numeric' => 'Jumlah harus berupa angka.',
            'quantity.min' => 'Jumlah harus lebih dari 0.',
            'unit.required' => 'Unit wajib diisi.',
            'unit.string' => 'Unit harus berupa string.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => null
            ], 500);
        }
        try {
            $product = Product::updateProduct($request->all(), $id);
                return new ProductResource(
                    true, // success
                    'Data Product Berhasil Diubah', // message
                    $product // data
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
            $product = Product::find($id);
            if (!$product) {
                return response()->json([
                    'success' => false, // success
                    'message' => 'Product Tidak Ditemukan', // message
                    'data' => null // data
                ], 404);
            }

            // Delete the product
            $product->delete();
            return new ProductResource(
                true,
                "Produk {$product->name} Berhasil Dihapus!",
                null
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, // success
                'message' => $e->getMessage(), // message
                'data' => null // data
            ], 500);
        }
    }
}
