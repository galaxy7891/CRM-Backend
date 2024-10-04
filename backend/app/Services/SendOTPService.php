<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use App\Mail\TemplateOTPEmail;
use App\Models\Otp;

class SendOTPService
{
    /**
     * Generate OTP and send email.
     *
     * @param string $email
     * @return void
     */
    public function generateAndSendOtp(string $email): void
    {
        $dataOtp = [
            'email' => $email,
            'code' => rand(100000, 999999)
        ];

        Otp::createOTP($dataOtp);

        Mail::to($email)->send(new TemplateOTPEmail($dataOtp['code']));
    }
}