<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResponseResource;
use App\Models\Product;

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
            return new ApiResponseResource(
                true,
                'Daftar Product',
                $product
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
            'name' => 'required|string|max:100|unique:products,name',
            'category' => 'required|string|max:100',
            'code' => 'required|string|max:100',
            'quantity' => 'required_if:category,stuff|numeric|min:0|prohibited_if:category,services',
            'unit' => 'required_if:category,stuff|in:box,pcs,unit|prohibited_if:category,services',
            'price' => 'required|numeric|min:0|max_digits:20',
            'description' => 'nullable|string',
            'photo_product' => 'nullable|max:2048',
        ], [
            'name.required' => 'Nama produk tidak boleh kosong.',
            'name.string' => 'Nama produk harus berupa teks.',
            'name.max' => 'Nama produk maksimal 100 karakter.',
            'name.unique' => 'Nama produk sudah terdaftar.',
            'category.required' => 'Kategori produk tidak boleh kosong.',
            'category.string' => 'Kategori produk harus berupa teks.',
            'category.max' => 'Kategori produk maksimal 100 karakter.',
            'code.required' => 'Kode tidak boleh kosong.',
            'code.string' => 'Kode harus berupa string.',
            'code.max' => 'Kode terlalu panjang.',
            'quantity.required_if' => 'Jumlah produk tidak boleh kosong.',
            'quantity.numeric' => 'Jumlah produk harus berupa angka.',
            'quantity.min' => 'Jumlah produk harus lebih dari 0.',
            'quantity.prohibited_if' => 'Jumlah produk harus kosong jika kategorinya services.',
            'unit.required_if' => 'Satuan produk tidak boleh kosong.',
            'unit.in' => 'Satuan produk harus pilih salah satu: box, pcs, unit.',
            'unit.prohibited_if' => 'Satuan produk harus kosong jika kategorinya services.',
            'price.required' => 'Harga tidak boleh kosong.',
            'price.numeric' => 'Harga harus berupa angka.',
            'price.min' => 'Harga harus lebih dari 0.',
            'price.max_digits' => 'Harga maksimal 20 digit.',
            'description.string' => 'Harga maksimal 20 digit.',
            'photo_product.max' => 'Foto produk maksimal 2 mb.',
        ]);
        if ($validator->fails()) {
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null
            );
        }

        try {
            $product = Product::createProduct($request->all());
            return new ApiResponseResource(
                true,
                'Product berhasil ditambahkan',
                $product
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
            $product = Product::find($id);
            if (!$product) {
                return new ApiResponseResource(
                    false, 
                    'Data Product Tidak Ditemukan!', 
                    null
                );
            }

            return new ApiResponseResource(
                true,
                'Data Product Ditemukan!',
                $product
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
        $product = product::find($id);
        if (!$product) {
            return new ApiResponseResource(
                false, 
                'Product Tidak Ditemukan',
                null
            );
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:products,name ',
            'category' => 'required|string|max:100',
            'code' => 'required|string|max:100',
            'quantity' => 'required_if:category,stuff|numeric|min:0|prohibited_if:category,services',
            'unit' => 'required_if:category,stuff|in:box,pcs,unit|prohibited_if:category,services',
            'price' => 'required|numeric|min:0|max_digits:20',
            'description' => 'nullable|string',
            'photo_product' => 'nullable|max:2048',
        ], [
            'name.required' => 'Nama produk tidak boleh kosong.',
            'name.string' => 'Nama produk harus berupa teks.',
            'name.max' => 'Nama produk maksimal 100 karakter.',
            'name.unique' => 'Nama produk sudah terdaftar.',
            'category.required' => 'Kategori produk tidak boleh kosong.',
            'category.string' => 'Kategori produk harus berupa teks.',
            'category.max' => 'Kategori produk maksimal 100 karakter.',
            'code.required' => 'Kode tidak boleh kosong.',
            'code.string' => 'Kode harus berupa string.',
            'code.max' => 'Kode terlalu panjang.',
            'quantity.required_if' => 'Jumlah produk tidak boleh kosong.',
            'quantity.numeric' => 'Jumlah produk harus berupa angka.',
            'quantity.min' => 'Jumlah produk harus lebih dari 0.',
            'quantity.prohibited_if' => 'Jumlah produk harus kosong jika kategorinya services.',
            'unit.required_if' => 'Satuan produk tidak boleh kosong.',
            'unit.in' => 'Satuan produk harus pilih salah satu: box, pcs, unit.',
            'unit.prohibited_if' => 'Satuan produk harus kosong jika kategorinya services.',
            'price.required' => 'Harga tidak boleh kosong.',
            'price.numeric' => 'Harga harus berupa angka.',
            'price.min' => 'Harga harus lebih dari 0.',
            'price.max_digits' => 'Harga maksimal 20 digit.',
            'description.string' => 'Harga maksimal 20 digit.',
            'photo_product.max' => 'Foto produk maksimal 2 mb.',
        ]);
        if ($validator->fails()) {
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null
            );
        }
        
        try {
            $product = Product::updateProduct($request->all(), $id);
            return new ApiResponseResource(
                true,
                'Data Product Berhasil Diubah',
                $product
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
            $product = Product::find($id);
            if (!$product) {
                return new ApiResponseResource(
                    false, 
                    'Product Tidak Ditemukan',
                    null
                );
            }

            // Delete the product
            $product = Product::deleteProduct($id);

            // Return response with first and last name 
            return new ApiResponseResource(
                true,
                "Produk {$product->name} Berhasil Dihapus!",
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
