<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Kode OTP</title>
    <style>
        /* Import Google Fonts */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #ECF2F3;
            color: #1A1A1A;
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh;
        }

        .container {
            /* width: 100%; */
            max-width: 600px;
            margin: 0 auto;
            background-color: #FDFDFD;
            border-radius: 10px;
        }

        .header {
            height: 100px;
            padding: 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
 
        .header img {
            height: 60px;
            max-width: 60%;
            margin: 0 auto;
            display: block;
        }

        .content {
            padding: 16px;
        }

        .content h3 {
            font-size: 20px;
            font-weight: 500;
            margin-bottom: 36px;
            color: #1A1A1A;
        }

        .content p {
            font-size: 12px;
            color: #1A1A1A;
        }

        .otp-code {
            text-align: center;
            margin: 36px 0;
        }

        .otp-code div {
            background-color: #FFBD39;
            color: #000000;
            font-size: 16px;
            font-weight: bold;
            padding: 8px 16px;
            border-radius: 10px;
            margin: 0 4px;
            display: inline-block;
        }
        
        hr {
            margin: 0 16px;
            border: none;
            height: 1px;
            background-color: #D9D9D9;
        }

        .footer {
            font-size: 10px;
            color: #1A1A1A;
            text-align: center;
            margin: 28px 0;
        }

        .footer p {
            margin-bottom: 16px;
        }

    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <img src="https://res.cloudinary.com/dg8iiqd5p/image/upload/v1729498415/logo_crm_ethh7i.png" alt="logo">
        </div>
        <hr>

        <!-- Content -->
        <div class="content">
            <h3>Verifikasi Kode OTP untuk Registrasi Akun</h3>
            <p>Halo {{ $nama }},</p>
            <p>Silakan masukkan kode ini untuk melanjutkan proses registrasi akun Anda. Kode OTP ini hanya berlaku selama 5 menit. Harap segera masukkan kode tersebut pada website LoyalCust.</p>

            <div class="otp-code">
                @foreach (str_split($otp) as $digit)
                    <div class="otp">{{ $digit }}</div>
                @endforeach
            </div>

            <p>Demi menjaga kerahasiaan data Anda, mohon jangan membagikan kode OTP kepada orang lain.</p>
            <p>Salam Hormat,<br>Tim LoyalCust</p>
        </div>

        <hr> <br>
        <hr>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; 2024 LOYALCUST, All rights reserved</p>
        </div>
    </div>
</body>
</html>
