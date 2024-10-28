<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResponseResource;
use App\Models\Product;
use App\Traits\Filter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    use Filter;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Product::query();
            
            $query = $this->applyFilters($request, $query);

            $product = $query->paginate(25);
            return new ApiResponseResource(
                true,
                'Daftar data produk',
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
            'photo_product' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
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
            'photo_product.image' => 'Foto produk harus berupa gambar.',
            'photo_product.max' => 'Foto produk tidak sesuai format.',
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
                "Data produk {$request->name} berhasil ditambahkan",
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
    public function show($productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return new ApiResponseResource(
                false, 
                'Data produk tidak ditemukan.',
                null
            );
        }
        
        try {
            return new ApiResponseResource(
                true,
                "Data produk {$product->name}",
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
    public function update(Request $request, $productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return new ApiResponseResource(
                false, 
                'Data produk tidak ditemukan.',
                null
            );
        }
        
        $validator = Validator::make($request->all(), [
            'name' => "sometimes|required|string|max:100|unique:products,name,$productId",
            'category' => 'sometimes|required|string|max:100',
            'code' => 'sometimes|required|string|max:100',
            'quantity' => 'sometimes|required_if:category,stuff|numeric|min:0|prohibited_if:category,services',
            'unit' => 'sometimes|required_if:category,stuff|in:box,pcs,unit|prohibited_if:category,services',
            'price' => 'sometimes|required|numeric|min:0|max_digits:20',
            'description' => 'nullable|string',
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
        ]);
        if ($validator->fails()) {
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null
            );
        }
        
        try {
            $updatedProduct = Product::updateProduct($request->all(), $productId);
            return new ApiResponseResource(
                true,
                "Data produk {$updatedProduct->name} berhasil diubah",
                $updatedProduct
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
     * Update photo profile in cloudinary.
     */
    public function updatePhotoProduct(Request $request, $productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return new ApiResponseResource(
                false, 
                'Data produk tidak ditemukan',
                null
            );
        }

        $validator = Validator::make($request->all(), [
            'photo_product' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'photo_product.required' => 'Foto produk tidak boleh kosong.',
            'photo_product.image' => 'Foto produk harus berupa gambar.',
            'photo_product.mimes' => 'Foto produk tidak sesuai format.',
            'photo_product.max' => 'Foto produk maksimal 2mb.',
        ]);
        if ($validator->fails()) {
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null
            );
        }

        try {
            $photoData = $product->updatePhotoProduct($request->file('photo'), $productId); 

            return new ApiResponseResource(
                true,
                "Foto produk {$product->name} berhasil diperbarui",
                $photoData
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
    public function destroy($productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return new ApiResponseResource(
                false, 
                'Data  produk tidak ditemukan',
                null
            );
        }

        try {
            $name = $product->name;
            $product = Product::deleteProduct($productId);

            // Return response with first and last name 
            return new ApiResponseResource(
                true,
                "Data produk {$name} berhasil dihapus",
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
