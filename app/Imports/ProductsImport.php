<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;

class ProductsImport implements ToCollection, WithHeadingRow
{
    use Importable;

    protected $owner;
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

    public function __construct($owner)
    {
        $this->owner = $owner;
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

            // Cari duplikat menggunakan hash map
            // Cek jika baris secara keseluruhan duplikat
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
            
            // Pengecekan nama duplikat
            if (!empty($rowArray['nama_produk'])) {
                if (isset($nameMap[$rowArray['nama_produk']])) {
                    $property[] = 'Nama Produk';
                    $errorMessages[] = 'Nama produk sudah digunakan dalam file pada baris ke-' . ($nameMap[$rowArray['nama_produk']] + 2);
                } else {
                    $nameMap[$rowArray['nama_produk']] = $index;
                }
            }

            // Pengecekan kode produk duplikat
            if (!empty($rowArray['kode_produk'])) {
                if (isset($codeMap[$rowArray['kode_produk']])) {
                    $property[] = 'Kode Produk';
                    $errorMessages[] = 'Kode produk sudah digunakan dalam file di baris ke-' . ($codeMap[$rowArray['kode_produk']] + 2);
                } else {
                    $codeMap[$rowArray['kode_produk']] = $index;
                }
            }

            // Jika ada error
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

            // Validasi data menggunakan Validator
            $validator = Validator::make($rowArray, [
                'nama_produk' => 'required|string|max:100|unique:products,name',
                'kode_produk' => 'required|string|max:100',
                'kategori_produk' => 'nullable|in:barang,jasa',
                'jumlah_produk' => 'required_if:kategori_produk,barang|numeric|min:0|prohibited_if:kategori_produk,jasa',
                'satuan_produk' => 'required_if:kategori_produk,barang|in:box,pcs,unit|prohibited_if:kategori_produk,jasa',
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
                'jumlah_produk.min' => 'Jumlah produk harus lebih dari 0.',
                'jumlah_produk.prohibited_if' => 'Jumlah produk harus kosong jika kategorinya jasa.',
                'satuan_produk.required_if' => 'Satuan produk tidak boleh kosong.',
                'satuan_produk.in' => 'Satuan produk harus berupa salah satu: box, pcs, unit.',
                'satuan_produk.prohibited_if' => 'Satuan produk harus kosong jika kategorinya jasa.',
                'harga_produk.required' => 'Harga tidak boleh kosong.',
                'harga_produk.numeric' => 'Harga harus berupa angka.',
                'harga_produk.min' => 'Harga harus lebih dari 0.',
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
        return empty(array_filter($row->toArray(), function ($value) {
            return !is_null($value) && $value !== '';
        }));
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

        // Cek apakah semua heading sesuai dengan yang diharapkan
        if ($fileHeadings !== $expectedHeadings) {
            throw new \Exception('File tidak sesuai dengan template yang diberikan.');
        }
    }
}