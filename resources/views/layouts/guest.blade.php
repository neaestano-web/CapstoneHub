<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'JHCSC Capstone Repository') }}</title>

    <!-- Google Fonts: Plus Jakarta Sans for UI + JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --bg-base: #0b1119;
            --emerald-accent: #00e676;
            --emerald-hover: #00f5d4;
            --emerald-dark: #043823;
            --text-primary: #ffffff;
            --text-muted: #8fa2b6;
            --input-bg: rgba(22, 38, 54, 0.78);
            --input-border: rgba(255, 255, 255, 0.12);
        }

        html, body {
            min-height: 100vh;
            width: 100%;
            background-color: #0b1119;
            /* Luminous layered radial gradients matching the landing page */
            background-image: 
                radial-gradient(circle at 50% 24%, rgba(16, 185, 129, 0.20) 0%, rgba(56, 189, 248, 0.10) 35%, transparent 65%),
                radial-gradient(circle at 16% 48%, rgba(16, 185, 129, 0.18) 0%, transparent 45%),
                radial-gradient(circle at 84% 48%, rgba(0, 245, 212, 0.12) 0%, transparent 45%),
                radial-gradient(circle at 50% 92%, rgba(56, 189, 248, 0.10) 0%, transparent 52%),
                radial-gradient(ellipse at 50% 45%, #152538 0%, #0b1119 100%);
            background-attachment: fixed;
            color: var(--text-primary);
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            scrollbar-width: none !important; /* Firefox */
            -ms-overflow-style: none !important; /* IE and Edge */
        }

        /* Hide scrollbars cleanly across Chrome, Safari, Edge, and all Webkit */
        html::-webkit-scrollbar,
        body::-webkit-scrollbar,
        *::-webkit-scrollbar {
            display: none !important;
            width: 0px !important;
            height: 0px !important;
            background: transparent !important;
        }

        * {
            scrollbar-width: none !important;
            -ms-overflow-style: none !important;
        }

        /* ===== CYBER GRID OVERLAY (Same as Landing Page) ===== */
        .cyber-grid-overlay {
            position: fixed;
            inset: 0;
            background-image: 
                linear-gradient(to right, rgba(16, 185, 129, 0.07) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(16, 185, 129, 0.07) 1px, transparent 1px);
            background-size: 40px 40px;
            mask-image: radial-gradient(circle at 50% 45%, rgba(0, 0, 0, 1) 25%, rgba(0, 0, 0, 0.35) 60%, transparent 85%);
            -webkit-mask-image: radial-gradient(circle at 50% 45%, rgba(0, 0, 0, 1) 25%, rgba(0, 0, 0, 0.35) 60%, transparent 85%);
            pointer-events: none;
            z-index: 0;
        }

        .cyber-grid-overlay::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(52, 211, 153, 0.35) 1.5px, transparent 1.5px);
            background-size: 40px 40px;
            background-position: -1px -1px;
            pointer-events: none;
        }

        /* ===== TOP LEFT ONLY: SCS LOGO (Fixed Position, No other navigation) ===== */
        .auth-top-header {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            padding: 1.8rem 2.8rem;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            pointer-events: none;
        }

        .auth-top-header .auth-scs-brand {
            pointer-events: auto;
        }

        @media (max-width: 640px) {
            .auth-top-header {
                padding: 1rem 1.25rem;
                background: linear-gradient(180deg, rgba(11, 17, 25, 0.95) 0%, rgba(11, 17, 25, 0.7) 70%, transparent 100%);
                backdrop-filter: blur(8px);
                -webkit-backdrop-filter: blur(8px);
                width: 100%;
            }
        }

        .auth-scs-brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            transition: transform 0.25s ease;
        }

        .auth-scs-brand:hover {
            transform: translateY(-2px);
        }

        .scs-logo-frame {
            width: 44px;
            height: 44px;
            background: transparent;
            border: none;
            border-radius: 0;
            box-shadow: none;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            transition: transform 0.25s ease;
        }

        .auth-scs-brand:hover .scs-logo-frame {
            transform: scale(1.08);
            border-color: transparent;
            box-shadow: none;
        }

        .scs-logo-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.4));
            transition: filter 0.25s ease;
        }

        .auth-scs-brand:hover .scs-logo-img {
            filter: drop-shadow(0 3px 12px rgba(0, 230, 118, 0.55));
        }

        .scs-brand-details {
            display: flex;
            flex-direction: column;
            gap: 1px;
        }

        .scs-brand-title {
            font-size: 1.18rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.02em;
            line-height: 1.15;
        }

        .scs-brand-title .hub-text {
            color: #00e676;
        }

        .scs-brand-sub {
            font-size: 0.62rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            color: #94a3b8;
            font-family: 'JetBrains Mono', monospace;
        }

        /* ===== MAIN CENTER CONTAINER ===== */
        .auth-page-wrapper {
            position: relative;
            z-index: 10;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4.6rem 1.25rem 3.5rem;
            width: 100%;
        }

        .auth-center-card {
            width: 100%;
            max-width: 430px;
            display: flex;
            flex-direction: column;
            align-items: center;
            animation: fadeInScale 0.45s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .auth-center-card.auth-card-wide {
            max-width: 530px;
        }

        .auth-center-card.auth-card-wide .auth-emblem-badge {
            width: 52px;
            height: 52px;
            margin-bottom: 0.75rem;
            padding: 6px;
        }

        .auth-center-card.auth-card-wide .auth-heading {
            font-size: 1.95rem;
            margin-bottom: 0.2rem;
        }

        .auth-center-card.auth-card-wide .auth-subheading {
            margin-bottom: 1.2rem;
            font-size: 0.82rem;
        }

        .auth-center-card.auth-card-wide .auth-form-body {
            gap: 0.82rem;
        }

        .auth-center-card.auth-card-wide .auth-field {
            height: 46px;
            font-size: 0.88rem;
        }

        .auth-center-card.auth-card-wide .auth-switch-text {
            margin-top: 0.95rem;
        }

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: translateY(18px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* ===== TOP EMBLEM ICON ABOVE SIGN IN ===== */
        .auth-emblem-badge {
            width: 60px;
            height: 60px;
            border-radius: 20px;
            background: linear-gradient(135deg, rgba(0, 230, 118, 0.2) 0%, rgba(56, 189, 248, 0.15) 100%);
            border: 1.5px solid rgba(0, 230, 118, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4), 0 0 25px rgba(0, 230, 118, 0.25);
            margin-bottom: 1.2rem;
            position: relative;
        }

        .auth-emblem-badge img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        /* ===== TITLES & SUBTITLES (Matching Reference Image) ===== */
        .auth-heading {
            font-size: 2.25rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.025em;
            margin-bottom: 0.4rem;
            text-align: center;
            line-height: 1.2;
        }

        .auth-subheading {
            font-size: 0.88rem;
            color: var(--text-muted);
            text-align: center;
            margin-bottom: 2rem;
            font-weight: 400;
            line-height: 1.5;
            max-width: 380px;
        }

        /* ===== FORM WRAPPER & INPUTS ===== */
        .auth-form-body {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 1.05rem;
        }

        .auth-input-group {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
            width: 100%;
        }

        .auth-input-label {
            font-size: 0.76rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #cbd5e1;
        }

        .auth-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .auth-field {
            width: 100%;
            height: 50px;
            padding: 0 1.25rem;
            background: var(--input-bg);
            border: 1.5px solid var(--input-border);
            border-radius: 12px;
            color: #ffffff;
            font-size: 0.92rem;
            font-family: inherit;
            outline: none;
            transition: all 0.22s ease;
        }

        .auth-field::placeholder {
            color: #627992;
            font-size: 0.88rem;
        }

        .auth-field:focus {
            border-color: var(--emerald-accent);
            background: rgba(28, 48, 68, 0.95);
            box-shadow: 0 0 16px rgba(0, 230, 118, 0.28);
        }

        .auth-field.has-eye {
            padding-right: 3.1rem;
        }

        .auth-eye-btn {
            position: absolute;
            right: 0.9rem;
            background: transparent;
            border: none;
            color: #7b94ac;
            cursor: pointer;
            padding: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s;
        }

        .auth-eye-btn:hover {
            color: var(--emerald-accent);
        }

        .auth-field-error {
            font-size: 0.74rem;
            color: #f87171;
            font-weight: 600;
            padding-left: 0.4rem;
            margin-top: 0.15rem;
        }

        /* ===== REMEMBER ME & FORGOT PASSWORD ROW ===== */
        .auth-action-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            font-size: 0.82rem;
            margin-top: 0.1rem;
            margin-bottom: 0.35rem;
            width: 100%;
        }

        .auth-remember-wrap {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            color: #94a3b8;
            font-size: 0.82rem;
            font-weight: 500;
            user-select: none;
        }

        .auth-remember-wrap:hover {
            color: #e2e8f0;
        }

        .auth-checkbox {
            appearance: none;
            -webkit-appearance: none;
            width: 16px;
            height: 16px;
            border: 1.5px solid rgba(255, 255, 255, 0.25);
            border-radius: 4px;
            background: rgba(255, 255, 255, 0.06);
            cursor: pointer;
            outline: none;
            position: relative;
            transition: all 0.2s;
        }

        .auth-checkbox:checked {
            background: var(--emerald-accent);
            border-color: var(--emerald-accent);
        }

        .auth-checkbox:checked::after {
            content: '';
            position: absolute;
            left: 4.5px;
            top: 1.5px;
            width: 4.5px;
            height: 8.5px;
            border: solid #042f1d;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .auth-forgot-link {
            color: var(--emerald-accent);
            text-decoration: none;
            font-size: 0.82rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .auth-forgot-link:hover {
            color: var(--emerald-hover);
            text-decoration: underline;
        }

        /* ===== PRIMARY GREEN SUBMIT BUTTON (Matching Picture) ===== */
        .auth-btn-submit {
            width: 100%;
            height: 48px;
            background: var(--emerald-accent);
            color: #032b1a;
            border: none;
            border-radius: 9999px;
            font-size: 0.96rem;
            font-weight: 800;
            letter-spacing: -0.01em;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 8px 24px rgba(0, 230, 118, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 0.5rem;
        }

        .auth-btn-submit:hover {
            background: var(--emerald-hover);
            box-shadow: 0 10px 30px rgba(0, 245, 212, 0.5);
            transform: translateY(-1.5px);
            color: #032315;
        }

        .auth-btn-submit:active {
            transform: translateY(0);
        }

        /* ===== FOOTER SWITCH LINK ===== */
        .auth-switch-text {
            font-size: 0.84rem;
            color: var(--text-muted);
            text-align: center;
            margin-top: 1.4rem;
            width: 100%;
        }

        .auth-switch-text a {
            color: var(--emerald-accent);
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s;
        }

        .auth-switch-text a:hover {
            color: var(--emerald-hover);
            text-decoration: underline;
        }

        /* ===== BOTTOM LAYERED WAVES & PARTICLES (Exact match for picture!) ===== */
        .auth-bottom-waves {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 140px;
            pointer-events: none;
            z-index: 1;
            overflow: hidden;
        }

        .wave-svg {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* ===== AUTH ALERT BANNERS ===== */
        .auth-alert {
            width: 100%;
            margin-bottom: 1.15rem;
            padding: 0.85rem 1.1rem;
            font-size: 0.82rem;
            font-weight: 600;
            line-height: 1.45;
            border-radius: 14px;
            text-align: center;
            box-sizing: border-box;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .auth-alert-warning {
            color: #fef08a;
            background: rgba(113, 63, 18, 0.55);
            border: 1px solid rgba(234, 179, 8, 0.45);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25);
        }

        .auth-alert-success {
            color: #86efac;
            background: rgba(6, 78, 59, 0.55);
            border: 1px solid rgba(16, 185, 129, 0.45);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25);
        }

        .auth-alert-error {
            color: #fca5a5;
            background: rgba(127, 29, 29, 0.55);
            border: 1px solid rgba(239, 68, 68, 0.45);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25);
        }

        /* Floating particles matching picture circles and pluses */
        .auth-particle {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            z-index: 2;
        }
        .auth-p-1 { width: 6px; height: 6px; left: 6%; bottom: 60px; border: 1.5px solid rgba(255, 255, 255, 0.25); background: transparent; }
        .auth-p-2 { width: 4px; height: 4px; left: 14%; bottom: 95px; background: rgba(0, 230, 118, 0.4); }
        .auth-p-3 { width: 7px; height: 7px; left: 32%; bottom: 75px; border: 1.5px solid rgba(255, 255, 255, 0.25); background: transparent; }
        .auth-p-4 { width: 5px; height: 5px; left: 45%; bottom: 85px; background: rgba(56, 189, 248, 0.35); }
        .auth-p-5 { width: 6px; height: 6px; right: 34%; bottom: 70px; border: 1.5px solid rgba(255, 255, 255, 0.25); background: transparent; }
        .auth-p-6 { width: 4px; height: 4px; right: 22%; bottom: 100px; background: rgba(0, 230, 118, 0.4); }
        .auth-p-7 { width: 7px; height: 7px; right: 11%; bottom: 80px; border: 1.5px solid rgba(255, 255, 255, 0.2); background: transparent; }
        .auth-p-8 { width: 5px; height: 5px; right: 4%; bottom: 65px; background: rgba(255, 255, 255, 0.25); }
    </style>
</head>
<body>
    <!-- Cyber Grid Overlay matching landing page -->
    <div class="cyber-grid-overlay" aria-hidden="true"></div>

    <!-- Top Left: SCS Logo ONLY (No other navigation) -->
    <header class="auth-top-header">
        <a href="{{ url('/') }}" class="auth-scs-brand" title="School of Computing Studies — Capstone Repository">
            <div class="scs-logo-frame">
                <img src="{{ asset('images/scs.png') }}" alt="SCS Logo" class="scs-logo-img">
            </div>
            <div class="scs-brand-details">
                <span class="scs-brand-title">capstone<span class="hub-text">Hub</span></span>
                <span class="scs-brand-sub">CAPSTONE REPOSITORY</span>
            </div>
        </a>
    </header>

    <!-- Main Center Content Area -->
    <main class="auth-page-wrapper">
        {{ $slot }}
    </main>

    <!-- Bottom Layered Waves & Micro Particles (matching reference image) -->
    <div class="auth-bottom-waves" aria-hidden="true">
        <span class="auth-particle auth-p-1"></span>
        <span class="auth-particle auth-p-2"></span>
        <span class="auth-particle auth-p-3"></span>
        <span class="auth-particle auth-p-4"></span>
        <span class="auth-particle auth-p-5"></span>
        <span class="auth-particle auth-p-6"></span>
        <span class="auth-particle auth-p-7"></span>
        <span class="auth-particle auth-p-8"></span>

        <!-- Layered translucent wave ribbons from image -->
        <svg class="wave-svg" viewBox="0 0 1440 160" preserveAspectRatio="none" fill="none">
            <path d="M0,45 C320,110 560,10 880,70 C1140,115 1320,70 1440,50 L1440,160 L0,160 Z" fill="rgba(16, 38, 54, 0.75)"/>
            <path d="M0,75 C280,30 640,130 980,80 C1220,45 1340,90 1440,85 L1440,160 L0,160 Z" fill="rgba(24, 52, 74, 0.6)"/>
            <path d="M0,105 C260,80 520,140 840,95 C1120,60 1320,110 1440,100 L1440,160 L0,160 Z" fill="rgba(38, 76, 102, 0.45)"/>
            <path d="M0,135 C380,105 720,150 1060,115 C1260,95 1360,135 1440,125 L1440,160 L0,160 Z" fill="rgba(180, 210, 230, 0.14)"/>
        </svg>
    </div>
</body>
</html>
