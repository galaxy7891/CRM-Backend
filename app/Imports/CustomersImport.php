<?php

namespace App\Imports;

use App\Models\Organization;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;

class CustomersImport implements ToCollection, WithHeadingRow
{
    use Importable;

    protected $owner;
    protected $customerCategory;
    protected $invalidData = [];
    protected $validData = [];
    protected $summaryCounts = [
        'valid_data' => 0,
        'validation_errors' => 0,
        'organization_not_found' => 0,
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
                    $errorMessages[] = 'Nomor telepon sudah digunakan dalam file (duplikat di baris ' . ($phoneMap[$rowArray    ['nomor_telepon']] + 1) . ')';
                    $this->summaryCounts['duplicate_phone']++;
                } else {
                    $phoneMap[$rowArray['nomor_telepon']] = $index;
                }
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
                        'organization_name' => $row['nama_perusahaan'],
                        'first_name' => $row['nama_depan'],
                        'last_name' => $row['nama_belakang'],
                        'customerCategory' => $this->customerCategory,
                        'job' => $row['pekerjaan'],
                        'description' => $row['deskripsi'],
                        'status' => $row['status'],
                        'birthdate' => \Carbon\Carbon::parse($row['tanggal_lahir']),
                        'email' => $row['email'],
                        'phone' => $row['nomor_telepon'],
                        'owner' => $this->owner,
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
                'nama_depan' => 'required|string|max:50',
                'nama_belakang' => 'nullable|string|max:50',
                'pekerjaan' => 'nullable|string|max:100',
                'deskripsi' => 'nullable|string',
                'status' => 'required|in:hot,warm,cold',
                'tanggal_lahir' => 'nullable|date',
                'email' => 'nullable|email|unique:customers,email|max:100',
                'nomor_telepon' => 'required|numeric|max_digits:15|unique:customers,phone',
                'negara' => 'nullable|string|max:50',
                'provinsi' => 'nullable|string|max:100',
                'kota' => 'nullable|string|max:100',
                'kecamatan' => 'nullable|string|max:100',
                'kelurahan' => 'nullable|string|max:100',
                'kode_pos' => 'nullable|max:5',
                'alamat' => 'nullable|string|max:100',
            ], [
                'nama_depan.required' => 'Nama depan wajib diisi',
                'nama_depan.string' => 'Nama depan harus berupa teks',
                'nama_depan.max' => 'Nama depan maksimal 50 karakter',
                'nama_belakang.string' => 'Nama belakang harus berupa teks',
                'nama_belakang.max' => 'Nama belakang maksimal 50 karakter',
                'pekerjaan.string' => 'Pekerjaan harus berupa teks.',
                'pekerjaan.max' => 'Pekerjaan maksimal 100 karakter.',
                'deskripsi.string' => 'Pekerjaan maksimal 100 karakter.',
                'status.required' => 'Status pelanggan wajib dipilih.',
                'status.in' => 'Status harus berupa pilih salah satu: hot, warm, atau cold.',
                'tanggal_lahir.date' => 'Tanggal lahir harus berupa tanggal yang valid.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email sudah terdaftar.',
                'email.max' => 'Email maksimal 100 karakter.',
                'nomor_telepon.required' => 'Nomor telepon wajib diisi.',
                'nomor_telepon.numeric' => 'Nomor telepon harus berupa angka.',
                'nomor_telepon.max_digits' => 'Nomor telepon maksimal 15 angka.',
                'nomor_telepon.unique' => 'Nomor telepon sudah terdaftar.',
                'negara.string' => 'Asal negara harus berupa teks.',
                'negara.max' => 'Asal negara maksimal 50 karakter.',
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
                $this->invalidData[] = [
                    'row' => $index + 1,
                    'data' => [
                        'organization_name' => $row['nama_perusahaan'],
                        'first_name' => $row['nama_depan'],
                        'last_name' => $row['nama_belakang'],
                        'customerCategory' => $this->customerCategory,
                        'job' => $row['pekerjaan'],
                        'description' => $row['deskripsi'],
                        'status' => $row['status'],
                        'birthdate' => \Carbon\Carbon::parse($row['tanggal_lahir']),
                        'email' => $row['email'],
                        'phone' => $row['nomor_telepon'],
                        'owner' => $this->owner,
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

            $organization = Organization::whereRaw('LOWER(name) = ?', [strtolower($row['nama_perusahaan'])])->first();
            if (!$organization) {
                $this->invalidData[] = [
                    'row' => $index + 1,
                    'data' => [
                        'organization_name' => $row['nama_perusahaan'],
                        'first_name' => $row['nama_depan'],
                        'last_name' => $row['nama_belakang'],
                        'customerCategory' => $this->customerCategory,
                        'job' => $row['pekerjaan'],
                        'description' => $row['deskripsi'],
                        'status' => $row['status'],
                        'birthdate' => \Carbon\Carbon::parse($row['tanggal_lahir']),
                        'email' => $row['email'],
                        'phone' => $row['nomor_telepon'],
                        'owner' => $this->owner,
                        'address' => $row['alamat'],
                        'country' => $row['negara'],
                        'province' => $row['provinsi'],
                        'city' => $row['kota'],
                        'subdistrict' => $row['kecamatan'],
                        'village' => $row['kelurahan'],
                        'zip_code' => $row['kode_pos'],
                    ],
                    'message' => 'Organisasi tidak terdaftar'
                ];
                $this->summaryCounts['organization_not_found']++;
                continue;
            }

            $this->validData[] = [
                'organization_name' => $row['nama_perusahaan'],
                'first_name' => $row['nama_depan'],
                'last_name' => $row['nama_belakang'],
                'customerCategory' => $this->customerCategory,
                'job' => $row['pekerjaan'],
                'description' => $row['deskripsi'],
                'status' => $row['status'],
                'birthdate' => \Carbon\Carbon::parse($row['tanggal_lahir']),
                'email' => $row['email'],
                'phone' => $row['nomor_telepon'],
                'owner' => $this->owner,
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
            'nama_depan', 
            'nama_belakang', 
            'pekerjaan', 
            'deskripsi', 
            'status', 
            'tanggal_lahir', 
            'email', 
            'nomor_telepon',
            'alamat', 
            'negara', 
            'provinsi', 
            'kota', 
            'kecamatan', 
            'kelurahan', 
            'kode_pos'
        ];
        $fileHeadings = array_keys($row->toArray());

        if ($fileHeadings !== $expectedHeadings) {
            throw new \Exception('File tidak sesuai dengan template yang diberikan.');
        }
    }
}
