<?php

namespace App\Imports;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;

class CustomersCompanyImport implements ToCollection, WithHeadingRow
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
    protected $summaryCounts = [
        'valid_data' => 0,
        'validation_errors' => 0,
        'duplicate_name' => 0,
        'duplicate_email' => 0,
        'duplicate_phone' => 0,
        'duplicate_data' => 0,
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
        $emailMap = []; 
        $phoneMap = []; 
        $websiteMap = []; 
        $rowMap = []; 
        $statusMapping = [
            'tinggi' => 'hot',
            'sedang' => 'warm',
            'rendah' => 'cold',
        ];
        $propertyMapping = [
            'nama_perusahaan' => 'Nama Perusahaan', 
            'jenis_industri' => 'Jenis Industri', 
            'email' => 'Email', 
            'status' => 'Status', 
            'nomor_telepon' => 'Nomor Telepon', 
            'website' => 'Website', 
            'alamat' => 'Alamat', 
            'negara' => 'Negara', 
            'provinsi' => 'Provinsi', 
            'kota' => 'Kota',
            'kecamatan' => 'Kecamatan', 
            'kelurahan' => 'Kelurahan', 
            'kode_pos' => 'Kode Pos'
        ];

        foreach ($rows as $index => $row) {
            $rowArray = $row->toArray();
            $property = [];
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

            // check duplicate company name
            if (!empty($rowArray['nama_perusahaan'])) {
                if (isset($nameMap[$rowArray['nama_perusahaan']])) {
                    $property[] = 'Nama Perusahaan';
                    $errorMessages[] = 'Nama perusahaan sudah digunakan dalam file di baris ke-' . ($nameMap[$rowArray['nama_perusahaan']] + 2);
                } else {
                    $nameMap[$rowArray['nama_perusahaan']] = $index;
                }
            }

            // check duplicate email 
            if (!empty($rowArray['email'])) {
                if (isset($emailMap[$rowArray['email']])) {
                    $property[] = 'Email';
                    $errorMessages[] = 'Email sudah digunakan dalam file di baris ke-' . ($emailMap[$rowArray['email']] + 2);
                } else {
                    $emailMap[$rowArray['email']] = $index;
                }
            }

            // check duplicate phone
            if (!empty($rowArray['nomor_telepon'])) {
                if (isset($phoneMap[$rowArray['nomor_telepon']])) {
                    $property[] = 'Nomor Telepon';
                    $errorMessages[] = 'Nomor telepon sudah digunakan dalam file di baris ke-' . ($phoneMap[$rowArray['nomor_telepon']] + 2);
                } else {
                    $phoneMap[$rowArray['nomor_telepon']] = $index;
                }
            }
            
            // check duplicate website
            if (!empty($rowArray['website'])) {
                if (isset($websiteMap[$rowArray['website']])) {
                    $property[] = 'Website';
                    $errorMessages[] = 'Website sudah digunakan dalam file di baris ke-' . ($websiteMap[$rowArray['website']] + 2);
                } else {
                    $websiteMap[$rowArray['website']] = $index;
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
                'nama_perusahaan' => 'required|string|max:100|unique_customerscompanies_name',
                'jenis_industri' => 'nullable|string|max:50',
                'status' => 'required|in:Tinggi,Sedang,Rendah',
                'email' => 'nullable|email|max:100|unique_customerscompanies_email',
                'nomor_telepon' => 'nullable|numeric|max_digits:15|unique_customerscompanies_phone',
                'website' => 'nullable|string|max:255|unique_customerscompanies_website',
                'provinsi' => 'nullable|string|max:100',
                'kota' => 'nullable|string|max:100',
                'kecamatan' => 'nullable|string|max:100',
                'kelurahan' => 'nullable|string|max:100',
                'kode_pos' => 'nullable|max:5',
                'alamat' => 'nullable|string|max:100',
            ], [
                'nama_perusahaan.required' => 'Nama perusahaan tidak boleh kosong.',
                'nama_perusahaan.unique_customerscompanies_name' => 'Nama perusahaan sudah terdaftar.',
                'nama_perusahaan.string' => 'Nama perusahaan harus berupa teks.',
                'nama_perusahaan.max' => 'Nama maksimal 100 karakter.',
                'jenis_industri.string' => 'Jenis industri harus berupa teks.',
                'jenis_industri.max' => 'Jenis industri maksimal 50 karakter.',
                'status.required' => 'Status tidak boleh kosong.',
                'status.in' => 'Status harus pilih salah satu dari: Rendah, Sedang, atau Tinggi.',
                'email.email' => 'Format email tidak valid.',
                'email.unique_customerscompanies_email' => 'Email sudah terdaftar.',
                'email.max' => 'Email maksimal 100 karakter.',
                'nomor_telepon.numeric' => 'Nomor telepon harus berupa angka.',
                'nomor_telepon.max_digits' => 'Nomor telepon maksimal 15 angka.',
                'nomor_telepon.unique_customerscompanies_phone' => 'Nomor telepon sudah terdaftar.',
                'website.unique_customerscompanies_website' => 'Website sudah terdaftar.',
                'website.string' => 'Website harus berupa teks.',
                'website.max' => 'Website maksimal 255 karakter.',
                'provinsi.string' => 'Provinsi harus berupa teks.',
                'provinsi.max' => 'Provinsi maksimal 100 karakter.',
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

            $this->validData[] = [
                'name' => $row['nama_perusahaan'],
                'industry' => $row['jenis_industri'],
                'email' => $row['email'],
                'status' => $row['status'],
                'phone' => $row['nomor_telepon'],
                'owner' => $this->owner,
                'website' => $row['website'],
                'address' => $row['alamat'],
                'province' => $row['provinsi'],
                'city' => $row['kota'],
                'subdistrict' => $row['kecamatan'],
                'village' => $row['kelurahan'],
                'zip_code' => $row['kode_pos'],
            ];

            $this->summaryData['total_data']++;
            $this->summaryData['valid_data']++;
        }

        // Feedback response to the user
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
            'nama_perusahaan', 
            'jenis_industri', 
            'email', 
            'status', 
            'nomor_telepon', 
            'website', 
            'alamat', 
            'provinsi', 
            'kota',
            'kecamatan', 
            'kelurahan', 
            'kode_pos'
        ];
        $fileHeadings = array_keys($row->toArray());

        $missingColumns = array_diff($expectedHeadings, $fileHeadings);
        $extraColumns = array_diff($fileHeadings, $expectedHeadings);

        if ($missingColumns || $extraColumns) {
            $errorMessage = 'Dokumen tidak sesuai dengan template yang diberikan.';
            $errorMessage .= $missingColumns ? ' Kolom hilang: ' . implode(', ', $missingColumns) . '.' : '';
            $errorMessage .= $extraColumns ? ' Kolom tidak dikenal: ' . implode(', ', $extraColumns) . '.' : '';
            
            throw new \Exception($errorMessage);
        }
    }
}
