<?php

namespace App\Imports;

use App\Http\Resources\ApiResponseResource;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;

class ProductImport implements ToCollection, WithHeadingRow
{
    use Importable;

    protected $owner;
    protected $invalidData = [];
    protected $validData = [];
    protected $summaryCounts = [
        'valid_data' => 0,
        'empty_rows' => 0,
        'validation_errors' => 0,
        'duplicate_name' => 0,
        'duplicate_code' => 0,
        'duplicate_data' => 0,
    ];

    // Getter untuk valid data
    public function getValidData()
    {
        return $this->validData;
    }

    // Getter untuk invalid data
    public function getInvalidData()
    {
        return $this->invalidData;
    }

    // Getter untuk error counts
    public function getsummaryCounts()
    {
        return $this->summaryCounts;
    }

    public function __construct($owner)
    {
        $this->owner = $owner;
    }

    public function collection($rows)
    {
        $this->headingRowValidator($rows->first());

        $namaMap = []; 
        $codeMap = []; 
        $rowMap = []; 
        $categoryMapping = [
            'barang' => 'stuff',
            'jasa' => 'service',
        ];

        foreach ($rows as $index => $row) {
            $rowArray = $row->toArray();
            $errorMessages = [];
            if (isset($row['kategori_produk'])) {
                $categoryStatus = strtolower($row['kategori_produk']);
                if (array_key_exists($categoryStatus, $categoryMapping)) {
                    $row['kategori_produk'] = $categoryMapping[$categoryStatus];
                }
            }
            

            // Periksa apakah baris kosong
            if ($this->isEmptyRow($row)) {
                $this->invalidData[] = [
                    'row' => $index + 1,
                    'data' => [
                        'name' => $row['nama_produk'] ?? null,
                        'category' => $row['kategori_produk'] ?? null,
                        'code' => $row['kode_produk'] ?? null,
                        'quantity' => $row['jumlah_produk'] ?? null,
                        'unit' => $row['satuan_produk'] ?? null,
                        'price' => $row['harga_produk'] ?? null,
                        'description' => $row['deskripsi'] ?? null,
                    ],
                    'message' => 'Data kosong'
                ];
                $this->summaryCounts['empty_rows']++;
                continue;
            }

            // Cari duplikat menggunakan hash map
            // Pengecekan nama duplikat
            if (isset($namaMap[$rowArray['nama_produk']])) {
                $errorMessages[] = 'Nama produk sudah digunakan dalam file (duplikat di baris ' . ($namaMap[$rowArray['nama_produk']] + 1) . ')';
                $this->summaryCounts['duplicate_name']++;
            } else {
                $namaMap[$rowArray['nama_produk']] = $index;
            }

            // Pengecekan kode produk duplikat
            if (isset($codeMap[$rowArray['kode_produk']])) {
                $errorMessages[] = 'Kode produk sudah digunakan dalam file (duplikat di baris ' . ($codeMap[$rowArray['kode_produk']] + 1) . ')';
                $this->summaryCounts['duplicate_code']++;
            } else {
                $codeMap[$rowArray['kode_produk']] = $index;
            }

            // Cek jika baris secara keseluruhan duplikat
            $rowKey = json_encode($rowArray); 
            if (isset($rowMap[$rowKey])) {
                $errorMessages[] = 'Data duplikat ditemukan (duplikat di baris ' . ($rowMap[$rowKey] + 1) . ')';
                $this->summaryCounts['duplicate_data']++;
            } else {
                $rowMap[$rowKey] = $index;
            }

            // Jika ada error
            if (!empty($errorMessages)) {
                $this->invalidData[] = [
                    'row' => $index + 1,
                    'data' => [
                        'name' => $row['nama_produk'],
                        'category' => $row['kategori_produk'],
                        'code' => $row['kode_produk'],
                        'quantity' => $row['jumlah_produk'],
                        'unit' => $row['satuan_produk'],
                        'price' => $row['harga_produk'],
                        'description' => $row['deskripsi'],
                    ],
                    'message' => $errorMessages
                ];
                continue;
            }

            // Validasi data menggunakan Validator
            $validator = Validator::make($rowArray, [
                'nama_produk' => 'required|string|max:100|unique:products,name',
                'kode_produk' => 'required|string|max:100',
                'kategori_produk' => 'nullable|in:stuff,services',
                'jumlah_produk' => 'required_if:kategori_produk,stuff|numeric|min:0|prohibited_if:kategori_produk,services',
                'satuan_produk' => 'required_if:kategori_produk,stuff|in:box,pcs,unit|prohibited_if:kategori_produk,services',
                'harga_produk' => 'required|numeric|min:0|max_digits:20',
                'deskripsi' => 'required|string',
            ], [
                'nama_produk.required' => 'Nama produk wajib diisi.',
                'nama_produk.string' => 'Nama produk harus berupa teks.',
                'nama_produk.max' => 'Nama produk maksimal 100 karakter.',
                'nama_produk.unique' => 'Nama produk sudah terdaftar.',
                'kode_produk.required' => 'Kode wajib diisi.',
                'kode_produk.string' => 'Kode harus berupa string.',
                'kode_produk.max' => 'Kode terlalu panjang.',
                'kategori_produk.required' => 'Kategori produk wajib diisi salah satu: stuff atau services.',
                'kategori_produk.in' => 'Kategori produk harus pilih salah satu : stuff atau services.',
                'jumlah_produk.required_if' => 'Jumlah produk wajib diisi.',
                'jumlah_produk.numeric' => 'Jumlah produk harus berupa angka.',
                'jumlah_produk.min' => 'Jumlah produk harus lebih dari 0.',
                'jumlah_produk.prohibited_if' => 'Jumlah produk harus kosong jika kategorinya services.',
                'satuan_produk.required_if' => 'Satuan produk wajib diisi.',
                'satuan_produk.in' => 'Satuan produk harus pilih salah satu: box, pcs, unit.',
                'satuan_produk.prohibited_if' => 'Satuan produk harus kosong jika kategorinya services.',
                'harga_produk.required' => 'Harga wajib diisi.',
                'harga_produk.numeric' => 'Harga harus berupa angka.',
                'harga_produk.min' => 'Harga harus lebih dari 0.',
                'harga_produk.max_digits' => 'Harga maksimal 20 digit.',
                'deskripsi.string' => 'Deskripsi harus berupa teks.',
            ]);
            if ($validator->fails()) {
                $this->invalidData[] = [
                    'row' => $index + 1,
                    'data' => [
                        'name' => $row['nama_produk'],
                        'category' => $row['kategori_produk'],
                        'code' => $row['kode_produk'],
                        'quantity' => $row['jumlah_produk'],
                        'unit' => $row['satuan_produk'],
                        'price' => $row['harga_produk'],
                        'description' => $row['deskripsi'],
                    ],
                    'message' => $validator->errors()->all()
                ];
                $this->summaryCounts['validation_errors']++;
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

            $this->summaryCounts['valid_data']++;
        }

        // Feedback response to the user
        return [
            'validData' => $this->validData,
            'invalidData' => $this->invalidData,
            'summaryCounts' => $this->summaryCounts,
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
        $expectedHeadings = ['nama_produk', 'kode_produk','kategori_produk', 'jumlah_produk', 'satuan_produk', 'harga_produk', 'deskripsi'];
        $fileHeadings = array_keys($row->toArray());

        // Cek apakah semua heading sesuai dengan yang diharapkan
        if ($fileHeadings !== $expectedHeadings) {
            throw new \Exception('File tidak sesuai dengan template yang diberikan.');
        }
    }

}
