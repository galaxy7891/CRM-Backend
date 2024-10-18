<?php

namespace App\Imports;

use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;

class OrganizationsImport implements ToCollection, WithHeadingRow
{
    use Importable;

    protected $owner;
    protected $invalidData = [];
    protected $validData = [];
    protected $summaryCounts = [
        'valid_data' => 0,
        'validation_errors' => 0,
        'duplicate_name' => 0,
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
        $this->headingRowValidator($rows->first());

        $nameMap = []; 
        $emailMap = []; 
        $phoneMap = []; 
        $rowMap = []; 
        $statusMapping = [
            'tinggi' => 'hot',
            'sedang' => 'warm',
            'rendah' => 'cold',
        ];

        foreach ($rows as $index => $row) {
            $rowArray = $row->toArray();
            $errorMessages = [];
            
            if ($this->isEmptyRow($row)) {
                continue;
            }

            if (!empty($rowArray['status'])) {
                if (isset($row['status'])) {
                    $lowerStatus = strtolower($row['status']);
                    if (array_key_exists($lowerStatus, $statusMapping)) {
                        $row['status'] = $statusMapping[$lowerStatus];
                    }
                }
            }

            // Cari duplikat menggunakan hash map
            // Pengecekan nama perusahaan duplikat
            if (!empty($rowArray['nama_perusahaan'])) {
                if (isset($nameMap[$rowArray['nama_perusahaan']])) {
                    $errorMessages[] = 'Nama perusahaan sudah digunakan dalam file (duplikat di baris ' . ($nameMap[$rowArray['nama_perusahaan']] + 1) . ')';
                    $this->summaryCounts['duplicate_name']++;
                } else {
                    $nameMap[$rowArray['nama_perusahaan']] = $index;
                }
            }

            // Pengecekan email duplikat
            if (!empty($rowArray['email'])) {
                if (isset($emailMap[$rowArray['email']])) {
                    $errorMessages[] = 'Email sudah digunakan dalam file (duplikat di baris ' . ($emailMap[$rowArray['email']] + 1) . ')';
                    $this->summaryCounts['duplicate_email']++;
                } else {
                    $emailMap[$rowArray['email']] = $index;
                }
            }

            // Pengecekan nomor telepon duplikat
            if (!empty($rowArray['nomor_telepon'])) {
                if (isset($phoneMap[$rowArray['nomor_telepon']])) {
                    $errorMessages[] = 'Nomor telepon sudah digunakan dalam file (duplikat di baris ' . ($phoneMap[$rowArray['nomor_telepon']] + 1) . ')';
                    $this->summaryCounts['duplicate_phone']++;
                } else {
                    $phoneMap[$rowArray['nomor_telepon']] = $index;
                }
            }

            // Cek jika baris secara keseluruhan duplikat
            $rowKey = json_encode($rowArray); 
            if (isset($rowMap[$rowKey]) && !empty($rowMap[$rowKey])) {
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
                        'province' => $row['provinsi'],
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
            $validator = Validator::make($row->toArray(), [
                'nama_perusahaan' => 'required|unique:organizations,name|string|max:100',
                'jenis_industri' => 'nullable|string|max:50',
                'status' => 'required|in:hot,warm,cold',
                'email' => 'nullable|email|unique:organizations,email|max:100',
                'nomor_telepon' => 'nullable|numeric|max_digits:15|unique:organizations,phone',
                'website' => 'nullable|string|max:255',
                'negara' => 'nullable|string|max:50',
                'provinsi' => 'nullable|string|max:100',
                'kota' => 'nullable|string|max:100',
                'kecamatan' => 'nullable|string|max:100',
                'kelurahan' => 'nullable|string|max:100',
                'kode_pos' => 'nullable|max:5',
                'alamat' => 'nullable|string|max:100',
            ], [
                'nama_perusahaan.required' => 'Nama perusahaan wajib diisi.',
                'nama_perusahaan.unique' => 'Nama perusahaan sudah terdaftar.',
                'nama_perusahaan.string' => 'Nama perusahaan harus berupa teks.',
                'nama_perusahaan.max' => 'Nama maksimal 100 karakter.',
                'jenis_industri.string' => 'Jenis industri harus berupa teks.',
                'jenis_industri.max' => 'Jenis industri maksimal 50 karakter.',
                'status.required' => 'Status wajib diisi.',
                'status.in' => 'Status harus pilih salah satu dari: hot, warm, cold.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email sudah terdaftar.',
                'email.max' => 'Email maksimal 100 karakter.',
                'nomor_telepon.numeric' => 'Nomor telepon harus berupa angka.',
                'nomor_telepon.max_digits' => 'Nomor telepon maksimal 15 angka.',
                'nomor_telepon.unique' => 'Nomor telepon sudah terdaftar.',
                'website.string' => 'Website harus berupa teks.',
                'website.max' => 'Website maksimal 255 karakter.',
                'negara.string' => 'Asal negara harus berupa teks.',
                'negara.max' => 'Asal negara maksimal 50 karakter.',
                'kota.string' => 'Kota harus berupa teks.',
                'kota.max' => 'Kota maksimal 100 karakter.',
                'kecamatan.string' => 'Kecamatan harus berupa teks.',
                'kecamatan.max' => 'Kecamatan maksimal 100 karakter.',
                'kelurahan.string' => 'Desa/Kelurahan harus berupa teks.',
                'kelurahan.max' => 'Desa/Kelurahan maksimal 100 karakter.',
                'kode_pos.string' => 'Kode pos harus berupa teks.',
                'kode_pos.max' => 'Kode pos maksimal 5 karakter.',
                'alamat.string' => 'Alamat harus berupa teks.',
                'alamat.max' => 'Alamat maksimal 100 karakter.',
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
                        'province' => $row['provinsi'],
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
                'province' => $row['provinsi'],
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
    
    public function headingRowValidator($row)
    {
        $expectedHeadings = [
            'nama_perusahaan', 
            'jenis_industri', 
            'email', 
            'status', 
            'nomor_telepon', 
            'website', 
            'alamat', 
            'negara', 
            'provinsi', 
            'kota',
            'kecamatan', 
            'kelurahan', 
            'kode_pos'
        ];
        $fileHeadings = array_keys($row->toArray());

        // Cek apakah semua heading sesuai dengan yang diharapkan
        if ($fileHeadings !== $expectedHeadings) {
            throw new \Exception('File tidak sesuai dengan template yang diberikan.');
        }
    }
}
