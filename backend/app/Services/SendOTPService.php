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
        $code = rand(100000, 999999);

        Otp::createOTP($email, $code);

        Mail::to($email)->send(new TemplateOTPEmail($code));
    }
}