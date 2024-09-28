<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;

class OtpService
{
    /**
     * Generate OTP and send email.
     *
     * @param string $email
     * @return void
     */
    public function generateAndSendOtp(string $email): void
    {
        $otp = rand(100000, 999999);

        session(['otp' => $otp]);
        session(['otp_expires_at' => now()->addMinutes(5)]);

        Mail::to($email)->send(new OtpMail($otp));
    }
}
