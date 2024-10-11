<?php

namespace App\Imports;

use App\Models\Organization;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;

class OrganizationImport implements ToCollection, WithHeadingRow
{
    use Importable;

    protected $owner;
    protected $invalidData = [];
    protected $validData = [];
    protected $summaryCounts = [
        'valid_data' => 0,
        'empty_rows' => 0,
        'validation_errors' => 0,
        'duplicate_email' => 0,
        'duplicate_phone' => 0,
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
        $emailMap = []; 
        $phoneMap = []; 
        $rowMap = []; 

        foreach ($rows as $index => $row) {
            $rowArray = $row->toArray();
            $errorMessages = [];

            // Periksa apakah baris kosong
            if ($this->isEmptyRow($row)) {
                $this->invalidData[] = [
                    'row' => $index + 1,
                    'data' => [
                        'name' => $row['nama_perusahaan'] ?? null,
                        'industry' => $row['jenis_industri'] ?? null,
                        'email' => $row['email'] ?? null,
                        'status' => $row['status'] ?? null,
                        'phone' => $row['nomor_telepon'] ?? null,
                        'owner' => $this->owner,
                        'website' => $row['website'] ?? null,
                        'address' => $row['alamat'] ?? null,
                        'country' => $row['negara'] ?? null,
                        'city' => $row['kota'] ?? null,
                        'subdistrict' => $row['kecamatan'] ?? null,
                        'village' => $row['kelurahan'] ?? null,
                        'zip_code' => $row['kode_pos'] ?? null,
                    ],
                    'message' => 'Data kosong'
                ];
                $this->summaryCounts['empty_rows']++;
                continue;
            }

            // Cari duplikat menggunakan hash map
            // Pengecekan email duplikat
            if (isset($emailMap[$rowArray['email']])) {
                $errorMessages[] = 'Email sudah digunakan dalam file (duplikat di baris ' . ($emailMap[$rowArray['email']] + 1) . ')';
                $this->summaryCounts['duplicate_email']++;
            } else {
                $emailMap[$rowArray['email']] = $index;
            }

            // Pengecekan nomor telepon duplikat
            if (isset($phoneMap[$rowArray['nomor_telepon']])) {
                $errorMessages[] = 'Nomor telepon sudah digunakan dalam file (duplikat di baris ' . ($phoneMap[$rowArray['nomor_telepon']] + 1) . ')';
                $this->summaryCounts['duplicate_phone']++;
            } else {
                $phoneMap[$rowArray['nomor_telepon']] = $index;
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
                        'name' => $row['nama_perusahaan'],
                        'industry' => $row['jenis_industri'],
                        'email' => $row['email'],
                        'status' => $row['status'],
                        'phone' => $row['nomor_telepon'],
                        'owner' => $this->owner,
                        'website' => $row['website'],
                        'address' => $row['alamat'],
                        'country' => $row['negara'],
                        'city' => $row['kota'],
                        'subdistrict' => $row['kecamatan'],
                        'village' => $row['kelurahan'],
                        'zip_code' => $row['kode_pos'],
                    ],
                    'message' => $errorMessages
                ];
                continue;
            }

            // Validasi data menggunakan Validator
            $validator = Validator::make($rowArray, [
                'nama_perusahaan' => 'required|unique:organizations,name|string|max:100',
                'jenis_industri' => 'nullable|string|max:50',
                'email' => 'nullable|email|unique:organizations,email|max:100',
                'status' => 'required|in:hot,warm,cold',
                'nomor_telepon' => 'nullable|numeric|max_digits:15|unique:organizations,phone',
                'website' => 'nullable|string|max:255',
                'negara' => 'nullable|string|max:50',
                'kota' => 'nullable|string|max:100',
                'kecamatan' => 'nullable|string|max:100',
                'kelurahan' => 'nullable|string|max:100',
                'kode_pos' => 'nullable|max:5',
                'alamat' => 'nullable|string|max:100',
            ]);

            if ($validator->fails()) {
                $this->invalidData[] = [
                    'row' => $index + 1,
                    'data' => [
                        'name' => $row['nama_perusahaan'],
                        'industry' => $row['jenis_industri'],
                        'email' => $row['email'],
                        'status' => $row['status'],
                        'phone' => $row['nomor_telepon'],
                        'owner' => $this->owner,
                        'website' => $row['website'],
                        'address' => $row['alamat'],
                        'country' => $row['negara'],
                        'city' => $row['kota'],
                        'subdistrict' => $row['kecamatan'],
                        'village' => $row['kelurahan'],
                        'zip_code' => $row['kode_pos'],
                    ],
                    'message' => $validator->errors()->all()
                ];
                $this->summaryCounts['validation_errors']++;
                continue;
            }

            // Simpan data valid
            $this->validData[] = [
                'name' => $row['nama_perusahaan'],
                'industry' => $row['jenis_industri'],
                'email' => $row['email'],
                'status' => $row['status'],
                'phone' => $row['nomor_telepon'],
                'owner' => $this->owner,
                'website' => $row['website'],
                'address' => $row['alamat'],
                'country' => $row['negara'],
                'city' => $row['kota'],
                'subdistrict' => $row['kecamatan'],
                'village' => $row['kelurahan'],
                'zip_code' => $row['kode_pos'],
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
}
