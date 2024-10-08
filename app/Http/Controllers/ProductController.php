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
            'name' => 'required|string|max:100|unique:products,name ',
            'category' => 'required|string|max:100',
            'code' => 'required|string|max:100',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|in:box,pcs,unit',
            'price' => 'required|numeric|min:0|max_digits:20',
            'description' => 'nullable|string',
            'photo_product' => 'nullable|max:2048',
        ], [
            'name.required' => 'Nama produk wajib diisi.',
            'name.string' => 'Nama produk harus berupa teks.',
            'name.max' => 'Nama produk maksimal 100 karakter.',
            'name.unique' => 'Nama produk sudah terdaftar.',
            'category.required' => 'Kategori produk wajib diisi.',
            'category.string' => 'Kategori produk harus berupa teks.',
            'category.max' => 'Kategori produk maksimal 100 karakter.',
            'code.required' => 'Kode wajib diisi.',
            'code.string' => 'Kode harus berupa string.',
            'code.max' => 'Kode terlalu panjang.',
            'quantity.required' => 'Jumlah wajib diisi.',
            'quantity.numeric' => 'Jumlah harus berupa angka.',
            'quantity.min' => 'Jumlah harus lebih dari 0.',
            'unit.required' => 'Unit wajib diisi.',
            'unit.in' => 'Unit harus pilih salah satu: box, pcs, unit.',
            'price.required' => 'Harga wajib diisi.',
            'price.numeric' => 'Harga harus berupa angka.',
            'price.min' => 'Harga harus lebih dari 0.',
            'price.max_digits' => 'Harga maksimal 20 digit.',
            'description.string' => 'Harga maksimal 20 digit.',
            'photo_product.max' => 'Foto produk maksimal 2 mb.',
        ]);

        //check if validation fails
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
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|in:box,pcs,unit',
            'price' => 'required|numeric|min:0|max_digits:20',
            'description' => 'nullable|string',
            'photo_product' => 'nullable|max:2048',
        ], [
            'name.required' => 'Nama produk wajib diisi.',
            'name.string' => 'Nama produk harus berupa teks.',
            'name.max' => 'Nama produk maksimal 100 karakter.',
            'name.unique' => 'Nama produk sudah terdaftar.',
            'category.required' => 'Kategori produk wajib diisi.',
            'category.string' => 'Kategori produk harus berupa teks.',
            'category.max' => 'Kategori produk maksimal 100 karakter.',
            'code.required' => 'Kode wajib diisi.',
            'code.string' => 'Kode harus berupa string.',
            'code.max' => 'Kode terlalu panjang.',
            'quantity.required' => 'Jumlah wajib diisi.',
            'quantity.numeric' => 'Jumlah harus berupa angka.',
            'quantity.min' => 'Jumlah harus lebih dari 0.',
            'unit.required' => 'Unit wajib diisi.',
            'unit.in' => 'Unit harus pilih salah satu: box, pcs, unit.',
            'price.required' => 'Harga wajib diisi.',
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
