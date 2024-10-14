<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResponseResource;
use App\Mail\TemplateForgetPassword;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\Organization;
use App\Models\PasswordResetToken;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $users = User::latest()->paginate(25);

            return new ApiResponseResource(
                true, 
                'Daftar Customer',                  
                $users
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
        try {
            $user = User::find($id);
            
            if (!$user) {
                return new ApiResponseResource(
                    false,
                    'Data User Tidak Ditemukan!',
                    null
                );
            }

            return new ApiResponseResource(
                true,
                'Data User Ditemukan!',
                $user
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
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return new ApiResponseResource(
                false,
                'User tidak ditemukan',
                null
            );
        }

        $validator = Validator::make($request->all(), [
            'company_id' => 'nullable|uuid',
            'email' => 'required|email|unique:users,email',
            'first_name' => 'required|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'phone' => 'required|numeric|max_digits:15|unique:users, phone',
            'job_position' => 'required|max:50',
            'role' => 'required|in:super_admin,admin,employee',
            'gender' => 'nullable|in:male,female,other',
        ], [
            'company_id.uuid' => 'ID Company harus berupa UUID yang valid.',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email harus valid',
            'email.unique' => 'Email sudah terdaftar',
            'first_name.required' => 'Nama depan wajib diisi',
            'first_name.string' => 'Nama depan harus berupa teks',
            'first_name.max' => 'Nama depan maksimal 50 karakter',
            'last_name.required' => 'Nama belakang wajib diisi',
            'last_name.string' => 'Nama belakang harus berupa teks',
            'last_name.max' => 'Nama belakang maksimal 50 karakter',
            'phone.required' => 'Nomor telepon wajib diisi',
            'phone.numeric' => 'Nomor telepon harus berupa angka',
            'phone.max_digits' => 'Nomor telepon maksimal 15 angka',
            'phone.unique' => 'Nomor telepon sudah terdaftar.',
            'job_position.required' => 'Posisi pekerjaan wajib diisi',
            'job_position.max' => 'Posisi pekerjaan maksimal 50 karakter',
            'role.required' => 'Akses user harus diisi',
            'role.in' => 'Akses harus pilih salah satu: rendah, sedang, atau tinggi.',
            'gender.in' => 'Gender harus pilih salah satu: Laki-laki, Perempuan, Lain-lain.',
        ]);

        if ($validator->fails()) {
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null
            );
        }

        try {
            $user = User::updateCustomer($request->all(), $id);
            
            return new ApiResponseResource(
                true, 
                "Data User {$user->first_name}{$user->last_name} Berhasil Diubah!",
                $user
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
    public function destroy($id)
    {
        try {

            $customer = User::find($id);
            if (!$customer) {
                return new ApiResponseResource(
                    false,
                    'Customer tidak ditemukan',
                    null
                );
            }
            
            $first_name = $customer->first_name;
            $last_name = $customer->last_name;
            $customer->delete();

            return new ApiResponseResource(
                true, 
                "Customer {$first_name} {$last_name} Berhasil Dihapus!",
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

    /**
     * Send a password reset link to the given email address.
     *
     * @param \Illuminate\Http\Request $request 
     *
     * @return \Illuminate\Http\JsonResponse 
     */
    public function sendResetLink(Request $request)
    {

        $validator = Validator::make($request->only('email'), [
            'email' => 'required|email|exists:users,email|max:100'
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email harus valid',
            'email.exists' => 'Email belum terdaftar',
            'email.max' => 'Email maksimal 100 karakter',
        ]);
        
        if($validator->fails()){
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null
            );          
        }

        $recentResetPassword = PasswordResetToken::getRecentResetPasswordToken($request->email);

        if ($recentResetPassword){
            $remainingTime = PasswordResetToken::getRemainingTime($recentResetPassword);

            return new ApiResponseResource(
                false,
                'Dapat mengirim ulang link reset password dalam ' .     "{$remainingTime['minutes']} menit, dan {$remainingTime['seconds']} detik.",
                null
            );

        }

        try {

            $email = $request->email;
            $token = Str::uuid()->toString();
            
            $user = User::findByEmail($email);
            $nama = $user->first_name . ' ' . $user->last_name;

            $dataUser = [
                'email' => $email,
                'token' => $token
            ];
            
            $url = url('/reset-password?email=' . urlencode($email) . '&token=' . $token);
            Mail::to($email)->send(new TemplateForgetPassword($email, $url, $nama));
            
            PasswordResetToken::createPasswordResetToken($dataUser);

            return new ApiResponseResource(
                true,
                'Link Reset Password telah dikirim ke email anda.',
                [
                    'email' => $email
                ]
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
     * Reset the user's password using the provided token and new password.
     *
     * @param \Illuminate\Http\Request $request 
     *
     * @return \Illuminate\Http\JsonResponse 
     */
    public function reset(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email|max:100',
            'token' => 'required',
            'new_password' => 'required|min:8',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email harus valid',
            'email.exists' => 'Email belum terdaftar',
            'email.max' => 'Email maksimal 100 karakter',
            'token.required' => 'Token harus diisi',
            'new_password.required' => 'Password baru wajib diisi',
            'new_password.min' => 'Password baru minimal 8 digit'
        ]);

        if($validator->fails()){
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null 
            );
        }

        if (!PasswordResetToken::findPasswordResetToken($request->only('email', 'token'))) {
            return new ApiResponseResource(
                false,
                'Token reset password tidak valid atau telah kadaluarsa.',
                null
            );
        }

        try {

            $user = User::findByEmail($request->email);
            $user->updatePassword($request->new_password);
            PasswordResetToken::deletePasswordResetToken($request->email);
    
            return new ApiResponseResource(
                true,
                'Password berhasil diubah.',
                null 
            );

        } catch (\Exception $e){

            return new ApiResponseResource(
                false,
                $e->getMessage(),
                null
            );  

        }
    }

    /**
     * Get Summary data for dashboard user
     *
     * @return \Illuminate\Http\JsonResponse 
     */
    public function getSummary()
    {
        $user = auth()->user();
        $nama = $user->first_name . ' ' . $user->last_name;

        $greetingMessage = \App\Helpers\TimeGreetingHelper::getGreeting() . ', ' . $nama;

        $leadsCount = Customer::countCustomerByCategory($user->email, 'leads');
        $contactsCount = Customer::countCustomerByCategory($user->email, 'contact');

        $organizationsCount = Organization::countOrganization($user->email);
        
        $dealsQualification = Deal::countDealsByStage($user->email, 'qualificated');
        $dealsProposal = Deal::countDealsByStage($user->email, 'proposal');
        $dealsNegotiation = Deal::countDealsByStage($user->email, 'negotiate');
        $dealsWon = Deal::countDealsByStage($user->email, 'won');
        $dealsLost = Deal::countDealsByStage($user->email, 'lose');

        return new ApiResponseResource(
            true,
            $greetingMessage,
            [
                'user' => $nama,
                'leads' => $leadsCount,
                'contacts' => $contactsCount,
                'organizations' => $organizationsCount,
                'deals_pipeline' => [
                    'qualification' => $dealsQualification,
                    'proposal' => $dealsProposal,
                    'negotiation' => $dealsNegotiation,
                    'won' => $dealsWon,
                    'lose' => $dealsLost,
                ]
            ]
        );
    }
}
