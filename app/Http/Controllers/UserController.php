<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Mail\TemplateForgetPassword;
use App\Models\PasswordResetToken;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
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

            return new UserResource(
                true, 
                'Daftar Customer',                  
                $users
            );

        } catch (\Exception $e) {

            return new UserResource(
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
                return new UserResource(
                    false,
                    'Data User Tidak Ditemukan!',
                    null
                );
            }

            return new UserResource(
                true,
                'Data User Ditemukan!',
                $user
            );
        
        } catch (\Exception $e) {

            return new UserResource(
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
            return new UserResource(
                false,
                'User tidak ditemukan',
                null
            );
        }

        $validator = Validator::make($request->all(), [
            'company_id' => 'nullable|uuid',
            'email' => 'required|email|unique:users, email',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:',
            'job_position' => 'nullable|string|max:255',
            'role' => 'required|in:super_admin,admin,employee',
            'gender' => 'nullable|in:male,female,other',
        ], [
            'company_id.uuid' => 'ID Company harus berupa UUID yang valid.',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terpakai',
            'first_name.required' => 'Nama depan wajib diisi.',
            'last_name.required' => 'Nama belakang wajib diisi.',
            'role.required' => 'Akses user harus diisi',
            'role.in' => 'Akses harus berupa salah satu: rendah, sedang, atau tinggi.',
            'gender.in' => 'Gender harus berupa salah satu: Laki-laki, Perempuan, Lain-lain.',
        ]);

        if ($validator->fails()) {
            return new UserResource(
                false,
                $validator->errors(),
                null
            );
        }

        try {
            $user = User::updateCustomer($request->all(), $id);
            
            return new UserResource(
                true, 
                `Data User {$user->first_name}{$user->last_name} Berhasil Diubah!`,
                $user
            );

        } catch (\Exception $e) {

            return new UserResource(
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
                return new UserResource(
                    false,
                    'Customer tidak ditemukan',
                    null
                );
            }
            
            $first_name = $customer->first_name;
            $last_name = $customer->last_name;
            $customer->delete();

            return new UserResource(
                true, 
                "Customer {$first_name} {$last_name} Berhasil Dihapus!",
                null
            );

        } catch (\Exception $e) {

            return new UserResource(
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
            'email' => 'required|email|exists:users,email'
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email harus valid',
            'email.exists' => 'Email belum terdaftar',
        ]);
        
        if($validator->fails()){
            return new UserResource(
                false,
                $validator->errors(),
                null
            );          
        }

        $recentResetPassword = PasswordResetToken::getRecentResetPasswordToken($request->email);

        if ($recentResetPassword){
            $remainingTime = PasswordResetToken::getRemainingTime($recentResetPassword);

            return new UserResource(
                false,
                'Dapat mengirim ulang link reset password dalam ' . "{$remainingTime['minutes']} menit, dan {$remainingTime['seconds']} detik.",
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

            return new UserResource(
                true,
                'Link Reset Password telah dikirim ke email anda.',
                ['email' => $email]
            );

        } catch (\Exception $e) {

            return new UserResource(
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
            'email' => 'required|email',
            'token' => 'required',
            'new_password' => 'required|min:8',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email harus valid',
            'token.required' => 'Token harus diisi',
            'new_password.required' => 'Password baru wajib diisi',
            'new_password.min' => 'Password baru minimal 8 digit'
        ]);

        if($validator->fails()){
            return new UserResource(
                false,
                $validator->errors(),
                null 
            );
        }

        if (!PasswordResetToken::findPasswordResetToken($request->only('email', 'token'))) {
            return new UserResource(
                false,
                'Token reset password tidak valid atau telah kadaluarsa.',
                null
            );
        }

        try {

            $user = User::findByEmail($request->email);
            $user->updatePassword($request->new_password);
            PasswordResetToken::deletePasswordResetToken($request->email);
    
            return new UserResource(
                true,
                'Password berhasil diubah.',
                null 
            );

        } catch (\Exception $e){

            return new UserResource(
                false,
                $e->getMessage(),
                null
            );  

        }
    }
}
