<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;

class ProductsImport implements ToCollection, WithHeadingRow
{
    use Importable;

    protected $userCompanyId;
    protected $validData = [];
    protected $failedData = [];
    protected $summaryData = [
        'total_data' => 0, 
        'valid_data' => 0,
        'invalid_data' => 0,
    ];

    // Getter for valid data
    public function getValidData()
    {
        return $this->validData;
    }

    // Getter for invalid data
    public function getFailedData()
    {
        return $this->failedData;
    }

    // Getter for summary data
    public function getSummaryData()
    {
        return $this->summaryData;
    }

    public function __construct($userCompanyId)
    {
        $this->userCompanyId = $userCompanyId;
    }

    public function collection($rows)
    {
        $this->headingRowValidator($rows->first());

        $nameMap = []; 
        $codeMap = []; 
        $rowMap = []; 
        $categoryMapping = [
            'barang' => 'stuff',
            'jasa' => 'service',
        ];
        $propertyMapping = [
            'nama_produk' => 'Nama Produk',
            'kode_produk' => 'Kode Produk',
            'kategori_produk' => 'Kategori Produk',
            'jumlah_produk' => 'Jumlah Produk',
            'satuan_produk' => 'Satuan Produk',
            'harga_produk' => 'Harga Produk',
            'deskripsi' => 'Deskripsi',
        ];

        foreach ($rows as $index => $row) {
            $rowArray = $row->toArray();
            $property = [];
            $errorMessages = [];
            
            if ($this->isEmptyRow($row)) {
                continue;
            }

            if (!empty($rowArray['kategori_produk'])) {
                if (isset($row['kategori_produk'])) {
                    $categoryStatus = strtolower($row['kategori_produk']);
                    if (array_key_exists($categoryStatus, $categoryMapping)) {
                        $row['kategori_produk'] = $categoryMapping[$categoryStatus];
                    }
                }
            }

            // check duplicate data
            $rowKey = json_encode($rowArray); 
            if (isset($rowMap[$rowKey]) && !empty($rowMap[$rowKey])) {
                $this->failedData[] = [
                    'row' => $index + 2,
                    'data' => [
                        'property' => 'Semua Properti',
                        'fail' => 'Semua properti pada data duplikat dengan baris ke-' . ($rowMap[$rowKey] + 2),
                    ],
                ];
                $this->summaryData['total_data']++;
                $this->summaryData['invalid_data']++;
                continue;
            } else {
                $rowMap[$rowKey] = $index;
            }
            
            // check duplicate name
            if (!empty($rowArray['nama_produk'])) {
                if (isset($nameMap[$rowArray['nama_produk']])) {
                    $property[] = 'Nama Produk';
                    $errorMessages[] = 'Nama produk sudah digunakan dalam file pada baris ke-' . ($nameMap[$rowArray['nama_produk']] + 2);
                } else {
                    $nameMap[$rowArray['nama_produk']] = $index;
                }
            }

            // check duplicate code product
            if (!empty($rowArray['kode_produk'])) {
                if (isset($codeMap[$rowArray['kode_produk']])) {
                    $property[] = 'Kode Produk';
                    $errorMessages[] = 'Kode produk sudah digunakan dalam file di baris ke-' . ($codeMap[$rowArray['kode_produk']] + 2);
                } else {
                    $codeMap[$rowArray['kode_produk']] = $index;
                }
            }

            if (!empty($errorMessages)) {
                $this->failedData[] = [
                    'row' => $index + 2,
                    'data' => [
                        'property' => $property,
                        'fail' => $errorMessages,
                    ],
                ];
                $this->summaryData['total_data']++;
                $this->summaryData['invalid_data']++;
                continue;
            }

            $validator = Validator::make($rowArray, [
                'nama_produk' => 'required|string|max:100|'. Rule::unique('products', 'name')->whereNull('deleted_at'),
                'kode_produk' => 'required|string|max:100|'.  Rule::unique('products', 'code')->whereNull('deleted_at'),
                'kategori_produk' => 'nullable|in:barang,jasa',
                'jumlah_produk' => 'required_if:kategori_produk,barang|prohibited_if:kategori_produk,jasa|nullable|numeric|min:0',
                'satuan_produk' => 'required_if:kategori_produk,barang|prohibited_if:kategori_produk,jasa|nullable|in:box,pcs,unit',
                'harga_produk' => 'required|numeric|min:0|max_digits:20',
                'deskripsi' => 'required|string',
            ], [ 
                'nama_produk.required' => 'Nama produk tidak boleh kosong.',
                'nama_produk.string' => 'Nama produk harus berupa teks.',
                'nama_produk.max' => 'Nama produk maksimal 100 karakter.',
                'nama_produk.unique' => 'Nama produk sudah terdaftar.',
                'kode_produk.required' => 'Kode produk tidak boleh kosong.',
                'kode_produk.string' => 'Kode produk harus berupa string.',
                'kode_produk.max' => 'Kode terlalu panjang.',
                'kategori_produk.required' => 'Kategori produk tidak boleh kosong.',
                'kategori_produk.in' => 'Kategori produk harus berupa salah satu: barang atau jasa.',
                'jumlah_produk.required_if' => 'Jumlah produk tidak boleh kosong.',
                'jumlah_produk.numeric' => 'Jumlah produk harus berupa angka.',
                'jumlah_produk.min' => 'Jumlah produk minimal berisi 1.',
                'jumlah_produk.prohibited_if' => 'Jumlah produk harus kosong jika kategorinya jasa.',
                'satuan_produk.required_if' => 'Satuan produk tidak boleh kosong.',
                'satuan_produk.in' => 'Satuan produk harus berupa salah satu: box, pcs, unit.',
                'satuan_produk.prohibited_if' => 'Satuan produk harus kosong jika kategorinya jasa.',
                'harga_produk.required' => 'Harga tidak boleh kosong.',
                'harga_produk.numeric' => 'Harga harus berupa angka.',
                'harga_produk.min' => 'Harga minimal berisi 1.',
                'harga_produk.max_digits' => 'Harga maksimal 20 digit.',
                'deskripsi.string' => 'Deskripsi harus berupa teks.',
            ]);
            if ($validator->fails()) {
                $failedRules = $validator->failed();
                foreach ($failedRules as $key => $failures) {
                    $property[] = $propertyMapping[$key] ?? $key;
                }

                $this->failedData[] = [
                    'row' => $index + 2,
                    'data' => [
                        'property' => $property,
                        'fail' => $validator->errors()->all(),
                    ],
                ];

                $this->summaryData['total_data']++;
                $this->summaryData['invalid_data']++;
                continue;
            }

            // Simpan data valid
            $this->validData[] = [
                'name' => $row['nama_produk'],
                'user_company_id'=> $this->userCompanyId, 
                'category' => $row['kategori_produk'],
                'code' => $row['kode_produk'],
                'quantity' => $row['jumlah_produk'],
                'unit' => $row['satuan_produk'],
                'price' => $row['harga_produk'],
                'description' => $row['deskripsi'],
            ];

            $this->summaryData['total_data']++;
            $this->summaryData['valid_data']++;
        }

        return [
            'validData' => $this->validData,
            'failedData' => $this->failedData,
            'summaryData' => $this->summaryData,
        ];
    }

    private function isEmptyRow($row) 
    {
        return collect($row->toArray())->filter(function ($value) {
            return !is_null($value) && trim($value) !== '';
        })->isEmpty();
    }

    public function headingRowValidator($row)
    {
        $expectedHeadings = [
            'nama_produk',
            'kode_produk',
            'kategori_produk', 
            'jumlah_produk', 
            'satuan_produk', 
            'harga_produk', 
            'deskripsi'
        ];
        $fileHeadings = array_keys($row->toArray());

        $missingColumns = array_diff($expectedHeadings, $fileHeadings);
        $extraColumns = array_diff($fileHeadings, $expectedHeadings);

        if (!empty($missingColumns) || !empty($extraColumns)) {
            $errorMessage = 'Dokumen tidak sesuai dengan template yang diberikan. Kolom berikut hilang: ' . implode(", ", $missingColumns);
            
            throw new \Exception($errorMessage);
        }
    }
}
