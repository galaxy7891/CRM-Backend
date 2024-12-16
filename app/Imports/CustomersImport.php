<?php

namespace App\Imports;

use App\Models\CustomersCompany;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class CustomersImport implements ToCollection, WithHeadingRow
{
    use Importable;

    protected $owner;
    protected $customerCategory;
    protected $failedData = [];
    protected $validData = [];
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

    public function __construct($owner, $customerCategory)
    {
        $this->owner = $owner;
        $this->customerCategory = $customerCategory;
    }

    public function collection($rows)
    {
        $this->headingRowValidator($rows->first());

        $emailMap = [];
        $phoneMap = [];
        $rowMap = [];
        $statusMapping = [
            'tinggi' => 'hot',
            'sedang' => 'warm',
            'rendah' => 'cold',
        ];
        $propertyMapping = [
            'nama_perusahaan' => 'Nama Perusahaan', 
            'nama_depan' => 'Nama Depan', 
            'nama_belakang' => 'Nama Belakang', 
            'pekerjaan' => 'Pekerjaan', 
            'deskripsi' => 'Deskripsi', 
            'status' => 'Status', 
            'tanggal_lahir' => 'Tanggal Lahir', 
            'email' => 'Email', 
            'nomor_telepon' => 'Nomor Telepon',
            'alamat' => 'Alamat', 
            'negara' => 'Negara', 
            'provinsi' => 'Provinsi', 
            'kota' => 'Kota', 
            'kecamatan' => 'Kecamatan', 
            'kelurahan' => 'Kelurahan', 
            'kode_pos' => 'Kode Pos',
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
            
            // Check duplicate data 
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
                    $errorMessages[] = 'Nomor telepon sudah digunakan dalam file di baris ke-' . ($phoneMap[$rowArray    ['nomor_telepon']] + 2);
                } else {
                    $phoneMap[$rowArray['nomor_telepon']] = $index;
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
            
            // Convert Excel serial date to Carbon instance
            if (!empty($rowArray['tanggal_lahir'])) {
                if (is_numeric($rowArray['tanggal_lahir'])) {
                    $rowArray['tanggal_lahir'] = \Carbon\Carbon::instance(Date::excelToDateTimeObject($rowArray['tanggal_lahir']))->format('Y-m-d');
                }
            }

            $validator = Validator::make($rowArray, [
                'nama_depan' => 'required|string|max:50',
                'nama_belakang' => 'nullable|string|max:50',
                'pekerjaan' => 'nullable|string|max:100',
                'deskripsi' => 'nullable|string',
                'status' => 'required|in:tinggi,sedang,rendah',
                'tanggal_lahir' => 'nullable|date',
                'email' => 'nullable|email|max:100|'. Rule::unique('customers', 'email')->whereNull('deleted_at'),
                'nomor_telepon' => 'required|numeric|max_digits:15|'. Rule::unique('customers', 'phone')->whereNull('deleted_at'),
                'provinsi' => 'nullable|string|max:100',
                'kota' => 'nullable|string|max:100',
                'kecamatan' => 'nullable|string|max:100',
                'kelurahan' => 'nullable|string|max:100',
                'kode_pos' => 'nullable|max:5',
                'alamat' => 'nullable|string|max:100',
            ], [
                'nama_depan.required' => 'Nama depan tidak boleh kosong.',
                'nama_depan.string' => 'Nama depan harus berupa teks',
                'nama_depan.max' => 'Nama depan maksimal 50 karakter',
                'nama_belakang.string' => 'Nama belakang harus berupa teks',
                'nama_belakang.max' => 'Nama belakang maksimal 50 karakter',
                'pekerjaan.string' => 'Pekerjaan harus berupa teks.',
                'pekerjaan.max' => 'Pekerjaan maksimal 100 karakter.',
                'deskripsi.string' => 'Pekerjaan maksimal 100 karakter.',
                'status.required' => 'Status tidak boleh kosong.',
                'status.in' => 'Status harus berupa salah satu: tinggi, sedang, atau rendah.',
                'tanggal_lahir.date' => 'Tanggal lahir harus berupa tanggal yang valid.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email sudah terdaftar.',
                'email.max' => 'Email maksimal 100 karakter.',
                'nomor_telepon.required' => 'Nomor telepon tidak boleh kosong.',
                'nomor_telepon.numeric' => 'Nomor telepon harus berupa angka.',
                'nomor_telepon.max_digits' => 'Nomor telepon maksimal 15 angka.',
                'nomor_telepon.unique' => 'Nomor telepon sudah terdaftar.',
                'provinsi.string' => 'Provinsi harus berupa teks.',
                'provinsi.max' => 'Provinsi maksimal 100 karakter.',
                'kota.string' => 'Kota harus berupa teks.',
                'kota.max' => 'Kota maksimal 100 karakter.',
                'kecamatan.string' => 'Kecamatan harus berupa teks.',
                'kecamatan.max' => 'Kecamatan maksimal 100 karakter.',
                'kelurahan.string' => 'Desa/Kelurahan harus berupa teks.',
                'kelurahan.max' => 'Desa/Kelurahan maksimal 100 karakter.',
                'kode_pos.string' => 'Kode pos harus berupa teks.',
                'kode_pos.max' => 'Kode pos maksimal 10 karakter.',
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
            if ($this->customerCategory === 'contact'){
                $customersCompany = CustomersCompany::whereRaw('LOWER(name) = ?', [strtolower($row['nama_perusahaan'])])->first();
                if (!$customersCompany) {
                    $this->failedData[] = [
                        'row' => $index + 2,
                        'data' => [
                            'property' => 'Nama Perusahaan',
                            'fail' => 'Perusahaan belum terdaftar',
                        ],
                    ];
    
                    $this->summaryData['total_data']++;
                    $this->summaryData['invalid_data']++;
                    continue;
                } 
                $customerCompanyId = $customersCompany->id;
            } else {
                $customerCompanyId = null;
            }

            $this->validData[] = [
                'customers_company_id' => $customerCompanyId,
                'first_name' => $row['nama_depan'],
                'last_name' => $row['nama_belakang'],
                'customerCategory' => $this->customerCategory,
                'job' => $row['pekerjaan'],
                'description' => $row['deskripsi'],
                'status' => $row['status'],
                'birthdate' => $rowArray['tanggal_lahir'],
                'email' => $row['email'],
                'phone' => $row['nomor_telepon'],
                'owner' => $this->owner,
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
            'nama_depan', 
            'nama_belakang', 
            'pekerjaan', 
            'deskripsi', 
            'status', 
            'tanggal_lahir', 
            'email', 
            'nomor_telepon',
            'alamat',
            'provinsi', 
            'kota', 
            'kecamatan', 
            'kelurahan', 
            'kode_pos'
        ];
    
        if ($this->customerCategory === 'contact') {
            array_unshift($expectedHeadings, 'nama_perusahaan');
        }

        $fileHeadings = array_keys($row->toArray());

        $missingColumns = array_diff($expectedHeadings, $fileHeadings);
        if (!empty($missingColumns)) {
            $errorMessage = 'Dokumen tidak sesuai dengan template yang diberikan. Kolom berikut hilang: ' . implode(", ", $missingColumns);
            
            throw new \Exception($errorMessage);
        }
    }
}
