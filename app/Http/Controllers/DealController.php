<?php

namespace App\Http\Controllers;

use App\Helpers\ActionMapperHelper;
use App\Http\Resources\ApiResponseResource;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\DealsProduct;
use App\Models\Product;
use App\Services\DataLimitService;
use App\Traits\Filter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\TryCatch;

class DealController extends Controller
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
            $search = $request->input('search');
            $query = Deal::whereHas('user', function ($ownerQuery) use ($user) {
                $ownerQuery->where('user_company_id', $user->user_company_id);
            })->with([
                'dealsProducts.product' => function ($productQuery) {
                    $productQuery->select('id', 'name', 'price', 'quantity');
                },
                'customer' => function ($customerQuery) {
                    $customerQuery->select('id', 'first_name', 'last_name');
                },
                'customersCompany' => function ($companyQuery) {
                    $companyQuery->select('id', 'name');
                },
            ]);

            $query = $this->applyFiltersDeals($request, $query);
            $query2 = Deal::search($query, $search);
            $deals = $this->applyFilters($request, $query2);
            if ($deals->isEmpty()) {
                return new ApiResponseResource(
                    false,
                    'Data deals tidak ditemukan',
                    null
                );
            }
            
            $deals->getCollection()->transform(function ($deal) {
                $deal->status = ActionMapperHelper::mapStatus($deal->status);
                $deal->stage = ActionMapperHelper::mapStageDeal($deal->stage);
                $deal->payment_category = ActionMapperHelper::mapPaymentCategory($deal->payment_category);
                $deal->category = ActionMapperHelper::mapCategoryDeals($deal->category);
                
                $dealsProduct = $deal->dealsProducts->first();
                if ($dealsProduct) {
                    $deal->product = [
                        'product_id' => $dealsProduct->product_id,
                        'name' => $dealsProduct->product->name ?? null,
                        'price' => $dealsProduct->product->price ?? null,
                        'quantity' => $dealsProduct->quantity ?? $dealsProduct->product->quantity,
                        'unit' => $dealsProduct->unit,
                    ];
                } else {
                    $deal->product = null;
                }

                if ($deal->customer) {
                    $deal->customer_name = $deal->customer->first_name . ' ' . $deal->customer->last_name;
                } else {
                    $deal->customer_name = null;
                }

                if ($deal->customersCompany) {
                    $deal->customers_company_name = $deal->customersCompany->name;
                } else {
                    $deal->customers_company_name = null;
                }

                unset($deal->dealsProducts);
                unset($deal->customer);
                unset($deal->customersCompany);
                return $deal;
            });

            return new ApiResponseResource( 
                true, 
                'Daftar deals', 
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
        $user = auth()->user();
        if (!$user) {
            return new ApiResponseResource(
                false,
                'Unauthorized',
                null
            );
        }
        
        $rules = [
            'name' => 'required|string|max:100',
            'category' => 'required|in:Pelanggan,Perusahaan',
            'customer_id' => 'required_if:category,Pelanggan|prohibited_if:category,Perusahaan|nullable|exists:customers,id',
            'customers_company_id' => 'required_if:category,Perusahaan|prohibited_if:category,Pelanggan|nullable|exists:customers_companies,id',
            'product_id' => 'required|exists:products,id',
            'payment_category' => 'required|in:Sekali,Hari,Bulan,Tahun',
            'stage' => 'required|in:Kualifikasi,Proposal,Negosiasi,Tercapai,Gagal',
            'status' => 'required|in:Rendah,Sedang,Tinggi',
            'tag' => 'nullable|string|max:255',
            'owner' => 'required|email|max:100|exists:users,email',
            'description' => 'nullable|string|max:200',
        ];
        
        Validator::extend('valid_quantity', function ($attribute, $value, $parameters, $validator) {
            $productId = request()->input('product_id');
            $product = Product::find($productId);
            
            if ($product && $product->quantity !== null) {
                return $value <= $product->quantity;
            }

            return true;
        });
        if (Product::getCategoryById($request->product_id) === 'service') {
            $rules += [
                'quantity' => 'prohibited',
                'unit' => 'prohibited',
            ];
        } else {
            $rules += [
                'quantity' => 'required|numeric|min:1|valid_quantity',
                'unit' => 'required|in:box,pcs,unit',
            ];
        }
        
        if ($request->payment_category === 'sekali') {
            $rules += ['payment_duration' => 'prohibited'];
        } else {
            $rules += ['payment_duration' => 'required|numeric|min:1'];
        }
        
        if ($request->stage === 'tercapai') {
            $rules += [
                'value_estimated' => 'prohibited',
                'value_actual' => 'required|numeric|max_digits:20',
                'expected_close_date' => 'prohibited',
                'close_date' => 'required|date',
            ];

        } else {
            $rules += [
                'value_estimated' => 'required|numeric|max_digits:20',
                'value_actual' => 'prohibited',
                'expected_close_date' => 'required|date',
                'close_date' => 'prohibited',
            ];
        }

        $messages = [
            'name.required' => 'Nama deals tidak boleh kosong', 
            'name.string' => 'Nama deals harus berupa teks', 
            'name.max' => 'Nama deals maksimal berisi 100 karakter', 
            'category.required' => 'Kategori deals tidak boleh kosong', 
            'category.in' => 'Kategori pembeli harus berupa pilih salah satu: Pelanggan atau Perusahaan', 
            'customer_id.required_if' => 'Nama pelanggan tidak boleh kosong jika kategori pembeli adalah Pelanggan', 
            'customer_id.prohibited_if' => 'Nama pelanggan harus kosong jika kategori pembeli adalah Perusahaan', 
            'customer_id.exists' => 'Nama pelanggan tidak tersedia', 
            'customers_company_id.required_if' => 'Nama perusahaan  tidak boleh kosong jika kategori pembeli adalah Perusahaan', 
            'customers_company_id.prohibited_if' => 'Nama perusahaan harus kosong jika kategori pembeli adalah Pelanggan', 
            'customers_company_id.exists' => 'Nama perusahaan tidak tersedia', 
            'product_id.required' => 'Nama produk wajib dipilih', 
            'product_id.exists' => 'Nama produk yang dipilih tidak tersedia', 
            'quantity.required' => 'Jumlah produk tidak boleh kosong jika produk termasuk barang', 
            'quantity.prohibited' => 'Jumlah produk tidak boleh diisi jika produk termasuk jasa', 
            'quantity.numeric' => 'Jumlah produk harus berupa angka', 
            'quantity.min' => 'Jumlah produk minimal berisi 1',
            'quantity.valid_quantity' => 'Jumlah produk tidak boleh melebihi stok yang tersedia', 
            'unit.required' => 'Satuan tidak boleh kosong jika produk termasukbarang', 
            'unit.prohibited' => 'Satuan produk tidak boleh diisi jika produk termasuk jasa', 
            'unit.in' => 'Satuan produk wajib pilih salah satu: box, pcs, atau unit',
            'payment_category.required' => 'Kategori pembayaran tidak boleh kosong',
            'payment_category.in' => 'Kategori pembayaran harus berupa pilih salah satu: Sekali, Hari, Bulan, atau Tahun',
            'payment_duration.required' => 'Durasi pembayaran tidak boleh kosong',
            'payment_duration.prohibited' => 'Durasi pembayaran tidak boleh diisi jika kategori pembayaran adalah sekali',
            'payment_duration.numeric' => 'Durasi pembayaran harus berupa angka',
            'payment_duration.min' => 'Durasi pembayaran minimal berisi 1',
            'value_estimated.required' => 'Nilai perkiraan tidak boleh kosong jika tahapan belum tercapai',
            'value_estimated.prohibited' => 'Nilai perkiraan tidak boleh diisi jika tahapannya tercapai',
            'value_estimated.max_digits' => 'Nilai perkiraan maksimal 20 digit',
            'value_actual.required' => 'Nilai sebenarnya tidak boleh kosong jika tahapan tercapai',
            'value_actual.prohibited' => 'Nilai sebenarnya tidak boleh diisi jika tahapannya belum tercapai',
            'value_actual.max_digits' => 'Nilai sebenarnya maksimal 20 digit',
            'stage.required' => 'Tahapan tidak boleh kosong',
            'stage.in' => 'Tahapan harus berupa pilih salah satu: Kualifikasi, Proposal, Negosiasi, Tercapai, atau Gagal',
            'expected_close_date.required' => 'Tanggal perkiraan penutupan tidak boleh kosong jika tahapan belum tercapai',
            'expected_close_date.prohibited' => 'Tanggal perkiraan penutupan tidak boleh diisi jika tahapan tercapai',
            'close_date.required' => 'Tanggal penutupan tidak boleh kosong jika tahapan tercapai',
            'close_date.prohibited' => 'Tanggal penutupan tidak boleh diisi jika tahapan belum tercapai',
            'owner.required' => 'Penanggung jawab tidak boleh kosong',
            'owner.email' => 'Penanggung jawab harus berupa email valid',
            'owner.max' => 'Penanggung jawab maksimal 100 karakter',
            'owner.exists' => 'Penanggung jawab tidak tersedia',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return new ApiResponseResource(
                false, 
                $validator->errors(), 
                null
            );
        }

        $validatedData = $validator->validated();
        $dataDeals = array_merge(
            $validatedData,
            [
                'status' => ActionMapperHelper::mapStatusToDatabase($validatedData['status']),
                'stage' => ActionMapperHelper::mapStageDealToDatabase($validatedData['stage']),
                'payment_category' => ActionMapperHelper::mapPaymentCategoryToDatabase($validatedData['payment_category']),
                'category' => ActionMapperHelper::mapCategoryDealsToDatabase($validatedData['category']),
            ]
        );

        $dataDealsProduct = [
            'product_id' => $validatedData['product_id'],
            'quantity' => $validatedData['quantity'] ?? null,
            'unit' => $validatedData['unit'] ?? null,
        ];

        try {
            $deal = Deal::createDeal($dataDeals);
            if ($request->customer_id) {
                $customer = Customer::find($request->customer_id);
                if ($customer && $customer->customerCategory === 'leads') {
                    Customer::convert(null, $request->customer_id);
                }
            }
            $dealsProduct = DealsProduct::createDealsProducts($dataDealsProduct, $deal->id);
            
            return new ApiResponseResource(
                true,
                'Data deals berhasil ditambahkan',
                array_merge(
                    $deal->toArray(),
                    $dealsProduct->toArray(),
                    ['deals_product_id' => $dealsProduct->id],
                    ['id' => $deal->id]
                )
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
        $user = auth()->user();
        if (!$user) {
            return new ApiResponseResource(
                false,
                'Unauthorized',
                null
            );
        }

        try {
            $deal = Deal::whereHas('user', function ($query) use ($user) {
                $query->where('user_company_id', $user->user_company_id);
            })->with([
                'dealsProducts.product' => function ($productQuery) {
                    $productQuery->select('id', 'name', 'price', 'quantity');
                },
                'customer' => function ($customerQuery) {
                    $customerQuery->select('id', 'first_name', 'last_name');
                },
                'customersCompany' => function ($companyQuery) {
                    $companyQuery->select('id', 'name');
                },
            ])->find($id);

            if (!$deal) {
                return new ApiResponseResource(
                    false, 
                    'Data deals tidak ditemukan!',
                    null 
                ); 
            }
            
            $deal->status = ActionMapperHelper::mapStatus($deal->status);
            $deal->stage = ActionMapperHelper::mapStageDeal($deal->stage);
            $deal->payment_category = ActionMapperHelper::mapPaymentCategory($deal->payment_category);
            $deal->category = ActionMapperHelper::mapCategoryDeals($deal->category);
            
            $dealsProduct = $deal->dealsProducts->first();
            if ($dealsProduct) {
                $deal->product = [
                    'product_id' => $dealsProduct->product_id,
                    'name' => $dealsProduct->product->name ?? null,
                    'price' => $dealsProduct->product->price ?? null,
                    'quantity' => $dealsProduct->quantity ?? $dealsProduct->product->quantity,
                    'unit' => $dealsProduct->unit,
                ];
            } else {
                $deal->product = null;
            }

            if ($deal->customer) {
                $deal->customer_name = $deal->customer->first_name . ' ' . $deal->customer->last_name;
            } else {
                $deal->customer_name = null;
            }

            if ($deal->customersCompany) {
                $deal->customers_company_name = $deal->customersCompany->name;
            } else {
                $deal->customers_company_name = null;
            }

            unset($deal->dealsProducts);
            unset($deal->customer);
            unset($deal->customersCompany);
            
            return new ApiResponseResource(
                true,
                'Data deals', 
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
     * Show value deals for a specific stage
     */
    public function value(Request $request){
        $user = auth()->user();
        if (!$user) {
            return new ApiResponseResource(
                false,
                'Unauthorized',
                null
            );
        }

        try {
            $deals = Deal::sumValueEstimatedByStage($user->email, $user->role, $user->user_company_id);
            
            return new ApiResponseResource(
                true,
                'Value deals',
                $deals
            );

        } catch(\Exception $e){

        }
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $dealsId)
    {
        $deal = Deal::findDealsById($dealsId);
        if (!$deal) {
            return new ApiResponseResource(
                false, 
                'Deal tidak ditemukan',
                null
            );
        }
        
        $rules = [
            'name' => 'sometimes|required|string|max:100',
            'category' => 'sometimes|required|in:Pelanggan,Perusahaan',
            'customer_id' => 'sometimes|required_if:category,Pelanggan|prohibited_if:category,Perusahaan|nullable|exists:customers,id',
            'customers_company_id' => 'sometimes|required_if:category,Perusahaan|prohibited_if:category,Pelanggan|nullable|exists:customers_companies,id',
            'product_id' => 'sometimes|required|exists:products,id',
            'payment_category' => 'sometimes|required|in:Sekali,Hari,Bulan,Tahun',
            'stage' => 'sometimes|required|in:Kualifikasi,Proposal,Negosiasi,Tercapai,Gagal',
            'status' => 'sometimes|required|in:Rendah,Sedang,Tinggi',
            'tag' => 'sometimes|nullable|string|max:255',
            'owner' => 'sometimes|required|email|max:100|exists:users,email',
            'description' => 'sometimes|nullable|string|max:200',
        ];
        Validator::extend('valid_quantity', function ($attribute, $value, $parameters, $validator) {
            $productId = request()->input('product_id');
            $product = Product::find($productId);
            
            if ($product && $product->quantity !== null) {
                return $value <= $product->quantity;
            }
            
            return true;
        });
        if (Product::getCategoryById($request->product_id) === 'service') {
            $rules += [
                'quantity' => 'prohibited',
                'unit' => 'prohibited',
            ];
        } else {
            $rules += [
                'quantity' => 'required|numeric|min:1|valid_quantity',
                'unit' => 'required|in:box,pcs,unit',
            ];
        }
        
        if ($request->payment_category === 'sekali') {
            $rules += ['payment_duration' => 'prohibited'];
        } else {
            $rules += ['payment_duration' => 'required|numeric|min:1'];
        }
        
        if ($request->stage === 'tercapai') {
            $rules += [
                'value_actual' => 'required|numeric|max_digits:20',
                'close_date' => 'required|date',
            ];

        } else {
            $rules += [
                'value_estimated' => 'required|numeric|max_digits:20',
                'value_actual' => 'prohibited',
                'expected_close_date' => 'required|date',
                'close_date' => 'prohibited',
            ];
        }
        
        $messages = [
            'name.required' => 'Nama deals tidak boleh kosong',
            'name.string' => 'Nama deals harus berupa teks',
            'name.max' => 'Nama deals maksimal berisi 100 karakter',
            'category.required' => 'Kategori deals tidak boleh kosong',
            'category.in' => 'Kategori pembeli harus berupa pilih salah satu: Pelanggan atau Perusahaan',
            'customer_id.required_if' => 'Nama pelanggan tidak boleh kosong jika kategori pembeli adalah Pelanggan',
            'customer_id.prohibited_if' => 'Nama pelanggan harus kosong jika kategori pembeli adalah Perusahaan',
            'customer_id.exists' => 'Nama pelanggan tidak tersedia',
            'customers_company_id.required_if' => 'Nama perusahaan  tidak boleh kosong jika kategori pembeli adalah Perusahaan',
            'customers_company_id.prohibited_if' => 'Nama perusahaan harus kosong jika kategori pembeli adalah Pelanggan',
            'customers_company_id.exists' => 'Nama perusahaan tidak tersedia',
            'product_id.required' => 'Nama produk wajib dipilih',
            'product_id.exists' => 'Nama produk yang dipilih tidak tersedia',
            'quantity.required' => 'Jumlah produk tidak boleh kosong jika produk termasuk barang',
            'quantity.prohibited' => 'Jumlah produk tidak boleh diisi jika produk termasuk jasa',
            'quantity.numeric' => 'Jumlah produk harus berupa angka',
            'quantity.min' => 'Jumlah produk minimal berisi 1',
            'quantity.valid_quantity' => 'Jumlah produk tidak boleh melebihi stok yang tersedia',
            'unit.required' => 'Satuan tidak boleh kosong jika produk termasukbarang',
            'unit.prohibited' => 'Satuan produk tidak boleh diisi jika produk termasuk jasa',
            'unit.in' => 'Satuan produk wajib pilih salah satu: box, pcs, atau unit',
            'payment_category.required' => 'Kategori pembayaran tidak boleh kosong',
            'payment_category.in' => 'Kategori pembayaran harus berupa pilih salah satu: Sekali, Hari, Bulan, atau Tahun',
            'payment_duration.required' => 'Durasi pembayaran tidak boleh kosong',
            'payment_duration.prohibited' => 'Durasi pembayaran tidak boleh diisi jika kategori pembayaran adalah sekali',
            'payment_duration.numeric' => 'Durasi pembayaran harus berupa angka',
            'payment_duration.min' => 'Durasi pembayaran minimal berisi 1',
            'value_estimated.required' => 'Nilai perkiraan tidak boleh kosong jika tahapan belum tercapai',
            'value_estimated.prohibited' => 'Nilai perkiraan tidak boleh diisi jika tahapannya tercapai',
            'value_estimated.max_digits' => 'Nilai perkiraan maksimal 20 digit',
            'value_actual.required' => 'Nilai sebenarnya tidak boleh kosong jika tahapan tercapai',
            'value_actual.prohibited' => 'Nilai sebenarnya tidak boleh diisi jika tahapannya belum tercapai',
            'value_actual.max_digits' => 'Nilai sebenarnya maksimal 20 digit',
            'stage.required' => 'Tahapan tidak boleh kosong',
            'stage.in' => 'Tahapan harus berupa pilih salah satu: Kualifikasi, Proposal, Negosiasi, Tercapai, atau Gagal',
            'expected_close_date.required' => 'Tanggal perkiraan penutupan tidak boleh kosong jika tahapan belum tercapai',
            'expected_close_date.prohibited' => 'Tanggal perkiraan penutupan tidak boleh diisi jika tahapan tercapai',
            'close_date.required' => 'Tanggal penutupan tidak boleh kosong jika tahapan tercapai',
            'close_date.prohibited' => 'Tanggal penutupan tidak boleh diisi jika tahapan belum tercapai',
            'owner.required' => 'Penanggung jawab tidak boleh kosong',
            'owner.email' => 'Penanggung jawab harus berupa email valid',
            'owner.max' => 'Penanggung jawab maksimal 100 karakter',
            'owner.exists' => 'Penanggung jawab tidak tersedia',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return new ApiResponseResource(
                false, 
                $validator->errors(), 
                null
            );
        }

        $validatedData = $validator->validated();
        $dataDeals = array_merge(
            $validatedData,
            [
                'status' => ActionMapperHelper::mapStatusToDatabase($validatedData['status']),
                'stage' => ActionMapperHelper::mapStageDealToDatabase($validatedData['stage']),
                'payment_category' => ActionMapperHelper::mapPaymentCategoryToDatabase($validatedData['payment_category']),
                'category' => ActionMapperHelper::mapCategoryDealsToDatabase($validatedData['category']),
            ]
        );

        $dataDealsProduct = [
            'product_id' => $validatedData['product_id'],
            'quantity' => $validatedData['quantity'] ?? null,
            'unit' => $validatedData['unit'] ?? null,
        ];

        try {
            if ($validatedData['stage'] === 'tercapai') {
                $product = Product::find($validatedData['product_id']);
                if ($product && $product['category'] === 'stuff') {
                    $newQuantity = $product->quantity - ($validatedData['quantity'] ?? 0);
                    if ($newQuantity < 0) {
                        return new ApiResponseResource(
                            false,
                            'Jumlah produk di stok tidak mencukupi untuk memenuhi deals ini',
                            null
                        );
                    }
                    $product->update(['quantity' => $newQuantity]);
                }
            }

            $deal = Deal::updateDeal($dataDeals, $dealsId);
            $dealsProoducts = DealsProduct::where('deals_id', $deal->id)->first();
            $dealsProduct = DealsProduct::updateDealsProducts($dataDealsProduct, $dealsProoducts->id);
            
            return new ApiResponseResource(
                true,
                'Data deals berhasil ditambahkan',
                array_merge(
                    $deal->toArray(),
                    $dealsProduct->toArray(),
                    ['deals_product_id' => $dealsProduct->id],
                    ['id' => $deal->id]
                )
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
     * Update only the stage of the specified deal.
     */
    public function updateStage(Request $request, $dealsId)
    {
        $deal = Deal::findDealsById($dealsId);
        if (!$deal) {
            return new ApiResponseResource(
                false, 
                'Deal tidak ditemukan',
                null
            );
        }

        $validator = Validator::make($request->all(), [
            'stage' => 'required|in:Kualifikasi,Proposal,Negosiasi,Tercapai,Gagal',
        ], [
            'stage.required' => 'Tahapan tidak boleh kosong',
            'stage.in' => 'Tahapan harus berupa salah satu: Kualifikasi, Proposal, Negosiasi, Tercapai, atau Gagal',
        ]);

        if ($validator->fails()) {
            return new ApiResponseResource(
                false, 
                $validator->errors(), 
                null
            ); 
        } 

        $validatedData = $validator->validated();

        try {
            if ($validatedData['stage'] === 'tercapai') {
            $deal->update([
                    'stage' => ActionMapperHelper::mapStageDealToDatabase($validatedData['stage']),
                    'close_date' => now()->format('Y-m-d'),
                    'value_actual' => $deal->value_estimated,
                ]);

            } else {
                $deal->update([
                    'stage' => ActionMapperHelper::mapStageDealToDatabase($validatedData['stage']),
                ]);
            }

            return new ApiResponseResource(
                true,
                'Tahapan deals berhasil diperbarui',
                null
            );

        } catch (\Exception $e) {
            return new ApiResponseResource(
                false, 
                'Terjadi kesalahan saat memperbarui tahapan: ' . $e->getMessage(),
                null
            );
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('id', []);

        if (empty($ids) || !is_array($ids)) {
            return new ApiResponseResource(
                false,
                'Pilih data yang ingin dihapus terlebih dahulu',
                null
            );
        }
         
        try {
            DealsProduct::whereIn('deals_id', $ids)->delete();
            $deletedDealsCount = Deal::whereIn('id', $ids)->delete();

            if ($deletedDealsCount > 0) {
                return new ApiResponseResource(
                    true,
                    $deletedDealsCount . ' data deals berhasil dihapus',
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
