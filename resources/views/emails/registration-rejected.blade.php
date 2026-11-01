<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Application Update</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f1f5f9;
            margin: 0;
            padding: 0;
            color: #334155;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            background-color: #f1f5f9;
            padding: 40px 15px;
            box-sizing: border-box;
        }
        .container {
            max-width: 580px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 36px 30px;
            text-align: center;
            border-bottom: 3px solid #ef4444;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #ffffff;
        }
        .header p {
            margin: 6px 0 0;
            font-size: 13px;
            color: #94a3b8;
            letter-spacing: 0.3px;
        }
        .body {
            padding: 36px 32px 30px;
        }
        .badge-wrap {
            text-align: center;
            margin-bottom: 24px;
        }
        .status-badge {
            display: inline-block;
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
            padding: 8px 20px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 14px;
        }
        .message {
            font-size: 15px;
            line-height: 1.65;
            color: #475569;
            margin: 0 0 22px;
        }
        .reason-box {
            background-color: #fff1f2;
            border: 1px solid #ffe4e6;
            border-left: 4px solid #ef4444;
            border-radius: 8px;
            padding: 16px 18px;
            margin-bottom: 24px;
        }
        .reason-label {
            font-size: 12px;
            font-weight: 800;
            color: #be123c;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 6px;
        }
        .reason-text {
            font-size: 14px;
            color: #881337;
            line-height: 1.5;
            margin: 0;
            font-style: italic;
        }
        .info-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 26px;
        }
        .divider {
            height: 1px;
            background-color: #e2e8f0;
            margin: 26px 0;
        }
        .tips {
            font-size: 13px;
            color: #64748b;
            line-height: 1.6;
        }
        .tips strong {
            color: #334155;
        }
        .cta-wrap {
            text-align: center;
            margin: 26px 0 10px;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff !important;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            padding: 12px 28px;
            border-radius: 8px;
        }
        .footer {
            background-color: #f8fafc;
            padding: 24px 30px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
        }
        .footer p {
            margin: 4px 0;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1>Research Repository System</h1>
                <p>Institutional Scholarly & Capstone Portal</p>
            </div>
            
            <div class="body">
                <div class="badge-wrap">
                    <span class="status-badge">&#10005; Registration Not Approved</span>
                </div>

                <h2 class="greeting">Hello, {{ $user->first_name ?? $user->name }}!</h2>
                
                <p class="message">
                    Thank you for applying for an account on the Research Repository. After reviewing your submitted credentials, an administrator was unable to approve your registration at this time.
                </p>

                @if(!empty($rejectionReason))
                <div class="reason-box">
                    <div class="reason-label">Administrator Feedback / Reason:</div>
                    <p class="reason-text">"{{ $rejectionReason }}"</p>
                </div>
                @endif

                <div class="info-card">
                    <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                        <tr>
                            <td style="padding: 6px 0; font-size: 13px; color: #64748b; font-weight: 500;">Applicant Name:</td>
                            <td style="padding: 6px 0; font-size: 13px; color: #0f172a; font-weight: 600; text-align: right;">{{ $user->name }}</td>
                        </tr>
                        @if($user->student_id_number)
                        <tr>
                            <td style="padding: 6px 0; font-size: 13px; color: #64748b; font-weight: 500; border-top: 1px dashed #e2e8f0;">Student ID:</td>
                            <td style="padding: 6px 0; font-size: 13px; color: #0f172a; font-weight: 600; text-align: right; border-top: 1px dashed #e2e8f0;">{{ $user->student_id_number }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td style="padding: 6px 0; font-size: 13px; color: #64748b; font-weight: 500; border-top: 1px dashed #e2e8f0;">Email Address:</td>
                            <td style="padding: 6px 0; font-size: 13px; color: #0f172a; font-weight: 600; text-align: right; border-top: 1px dashed #e2e8f0;">{{ $user->email }}</td>
                        </tr>
                    </table>
                </div>

                <div class="tips">
                    <strong>Next Steps:</strong>
                    <ul style="margin: 8px 0 0; padding-left: 20px;">
                        <li>If your ID picture was blurry or unreadable, you may re-register with a clear, well-lit photo of your valid student ID card.</li>
                        <li>Ensure your registered student ID number matches your official institutional enrollment records.</li>
                        <li>If you believe this was an error, please reach out to the campus library or system administrator.</li>
                    </ul>
                </div>

                <div class="cta-wrap">
                    <a href="{{ route('register') }}" class="cta-button" target="_blank">
                        Submit a New Registration
                    </a>
                </div>
            </div>

            <div class="footer">
                <p>&copy; {{ date('Y') }} Research Repository System. All rights reserved.</p>
                <p>This is an automated system notification. Please do not reply directly to this email.</p>
            </div>
        </div>
    </div>
</body>
</html>
