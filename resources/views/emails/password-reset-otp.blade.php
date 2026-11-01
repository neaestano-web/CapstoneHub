<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #4f46e5;
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .body {
            padding: 30px;
            color: #333333;
        }
        .body p {
            font-size: 15px;
            line-height: 1.6;
        }
        .otp-box {
            text-align: center;
            margin: 30px 0;
        }
        .otp-code {
            display: inline-block;
            background-color: #f0f0ff;
            border: 2px dashed #4f46e5;
            border-radius: 8px;
            padding: 16px 40px;
            font-size: 36px;
            font-weight: bold;
            letter-spacing: 10px;
            color: #4f46e5;
        }
        .note {
            font-size: 13px;
            color: #888888;
            text-align: center;
            margin-top: 10px;
        }
        .footer {
            background-color: #f9f9f9;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #aaaaaa;
            border-top: 1px solid #eeeeee;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Research Repository</h1>
        </div>
        <div class="body">
            <p>Hello, <strong>{{ $userName }}</strong>!</p>
            <p>We received a request to reset your password. Please use the OTP code below to proceed:</p>

            <div class="otp-box">
                <span class="otp-code">{{ $otp }}</span>
            </div>

            <p class="note">This OTP code is valid for <strong>10 minutes</strong>. Do not share this code with anyone.</p>

            <p>If you did not request a password reset, please ignore this email.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Research Repository. All rights reserved.
        </div>
    </div>
</body>
</html>