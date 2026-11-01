<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Approved</title>
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
            border-bottom: 3px solid #10b981;
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
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
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
        .info-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 26px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 7px 0;
            font-size: 14px;
            border-bottom: 1px dashed #e2e8f0;
        }
        .info-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .info-row:first-child {
            padding-top: 0;
        }
        .info-label {
            color: #64748b;
            font-weight: 500;
        }
        .info-value {
            color: #0f172a;
            font-weight: 600;
            text-align: right;
        }
        .cta-wrap {
            text-align: center;
            margin: 30px 0 20px;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff !important;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            padding: 14px 34px;
            border-radius: 8px;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
        }
        .cta-subtext {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 10px;
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
                    <span class="status-badge">&#10003; Account Approved</span>
                </div>

                <h2 class="greeting">Hello, {{ $user->first_name ?? $user->name }}!</h2>
                
                <p class="message">
                    Great news! Your student registration has been reviewed and <strong>approved</strong> by the repository administrator. Your student account is now fully active.
                </p>

                <div class="info-card">
                    <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                        <tr>
                            <td style="padding: 6px 0; font-size: 13px; color: #64748b; font-weight: 500;">Account Name:</td>
                            <td style="padding: 6px 0; font-size: 13px; color: #0f172a; font-weight: 600; text-align: right;">{{ $user->name }}</td>
                        </tr>
                        @if($user->student_id_number)
                        <tr>
                            <td style="padding: 6px 0; font-size: 13px; color: #64748b; font-weight: 500; border-top: 1px dashed #e2e8f0;">Student ID:</td>
                            <td style="padding: 6px 0; font-size: 13px; color: #0f172a; font-weight: 600; text-align: right; border-top: 1px dashed #e2e8f0;">{{ $user->student_id_number }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td style="padding: 6px 0; font-size: 13px; color: #64748b; font-weight: 500; border-top: 1px dashed #e2e8f0;">Registered Email:</td>
                            <td style="padding: 6px 0; font-size: 13px; color: #0f172a; font-weight: 600; text-align: right; border-top: 1px dashed #e2e8f0;">{{ $user->email }}</td>
                        </tr>
                    </table>
                </div>

                <div class="cta-wrap">
                    <a href="{{ route('login') }}" class="cta-button" target="_blank">
                        Log In to Your Account
                    </a>
                    <div class="cta-subtext">You can now access your student dashboard and browse papers.</div>
                </div>

                <div class="divider"></div>

                <div class="tips">
                    <strong>What you can do now:</strong>
                    <ul style="margin: 8px 0 0; padding-left: 20px;">
                        <li>Browse and read approved capstone & research manuscripts.</li>
                        <li>Submit formal file download requests with institutional justification.</li>
                        <li>Submit your own research papers for faculty review and archiving.</li>
                    </ul>
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
