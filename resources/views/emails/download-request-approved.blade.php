<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Request Approved</title>
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
            border-bottom: 3px solid #2563eb;
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
            background-color: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
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
        .research-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #2563eb;
            border-radius: 10px;
            padding: 18px 20px;
            margin-bottom: 26px;
        }
        .research-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.4;
            margin: 0 0 10px;
        }
        .meta-row {
            font-size: 13px;
            color: #64748b;
            margin: 4px 0;
        }
        .meta-row strong {
            color: #334155;
        }
        .cta-wrap {
            text-align: center;
            margin: 30px 0 22px;
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
        .secondary-link-wrap {
            margin-top: 14px;
            font-size: 13px;
        }
        .secondary-link {
            color: #2563eb;
            text-decoration: underline;
            font-weight: 500;
        }
        .divider {
            height: 1px;
            background-color: #e2e8f0;
            margin: 26px 0;
        }
        .notice-box {
            background-color: #fffbeb;
            border: 1px solid #fef3c7;
            border-radius: 8px;
            padding: 14px 16px;
            font-size: 13px;
            color: #92400e;
            line-height: 1.5;
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
                    <span class="status-badge">&#10003; Download Request Approved</span>
                </div>

                <h2 class="greeting">Hello, {{ $downloadRequest->user->first_name ?? $downloadRequest->user->name }}!</h2>
                
                <p class="message">
                    Your request to download the full manuscript for the research paper below has been reviewed and <strong>approved</strong> by the administrator.
                </p>

                <div class="research-card">
                    <h3 class="research-title">{{ $downloadRequest->research->title ?? 'Research Manuscript' }}</h3>
                    
                    @if(!empty($downloadRequest->research->authors))
                    <div class="meta-row">
                        <strong>Author(s):</strong> {{ $downloadRequest->research->authors }}
                    </div>
                    @endif

                    @if(!empty($downloadRequest->research->year))
                    <div class="meta-row">
                        <strong>Year:</strong> {{ $downloadRequest->research->year }}
                    </div>
                    @endif

                    @if(!empty($downloadRequest->reason))
                    <div class="meta-row" style="margin-top: 8px; padding-top: 8px; border-top: 1px dashed #e2e8f0;">
                        <strong>Your Purpose:</strong> <em>"{{ $downloadRequest->reason }}"</em>
                    </div>
                    @endif
                </div>

                <div class="cta-wrap">
                    <a href="{{ route('repository.download', $downloadRequest->research_id) }}" class="cta-button" target="_blank">
                        Download Full Manuscript (PDF)
                    </a>
                    <div class="secondary-link-wrap">
                        or <a href="{{ route('repository.show', $downloadRequest->research_id) }}" class="secondary-link" target="_blank">view the research details page</a>
                    </div>
                </div>

                <div class="notice-box">
                    <strong>Important Note:</strong> Please ensure you are logged into your student account on the repository before clicking the download link. Downloaded academic papers are for institutional study and reference purposes only.
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
