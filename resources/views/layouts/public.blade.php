<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'capstoneHub — Browse Capstone Projects')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Early Anti-FOUC Theme Initializer for Guest Users -->
    <script>
        (function() {
            function hexToRgb(hex) {
                hex = hex.replace('#', '');
                if (hex.length === 3) hex = hex.split('').map(function(c) { return c + c; }).join('');
                var num = parseInt(hex, 16);
                return { r: (num >> 16) & 255, g: (num >> 8) & 255, b: num & 255 };
            }
            function getLuminance(r, g, b) {
                var a = [r, g, b].map(function(v) {
                    v /= 255;
                    return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
                });
                return a[0] * 0.2126 + a[1] * 0.7152 + a[2] * 0.0722;
            }

            try {
                var raw = localStorage.getItem('capstone_guest_display_theme');
                var s = raw ? JSON.parse(raw) : null;
                var mode = (s && s.mode) ? s.mode : 'light';
                if (mode === 'dark' || mode === 'oled') mode = 'light';
                document.documentElement.setAttribute('data-guest-theme', mode);
                if (s) {
                    if (s.accent) {
                        document.documentElement.style.setProperty('--guest-accent', s.accent);
                        var rgb = hexToRgb(s.accent);
                        document.documentElement.style.setProperty('--guest-accent-glow', 'rgba(' + rgb.r + ',' + rgb.g + ',' + rgb.b + ',0.35)');
                        document.documentElement.style.setProperty('--guest-accent-rgb', rgb.r + ',' + rgb.g + ',' + rgb.b);
                        var lum = getLuminance(rgb.r, rgb.g, rgb.b);
                        document.documentElement.style.setProperty('--guest-accent-text', lum < 0.45 ? '#ffffff' : '#042f2e');
                    }
                    if (s.fontSize) {
                        document.documentElement.style.setProperty('--guest-font-scale', s.fontSize);
                        document.documentElement.style.fontSize = s.fontSize;
                        var multMap = { '0.94rem': '0.94', '1rem': '1.0', '1.08rem': '1.08', '1.18rem': '1.20' };
                        document.documentElement.style.setProperty('--guest-font-multiplier', multMap[s.fontSize] || '1.0');
                    }
                    if (s.mode === 'custom') {
                        if (s.customBg) {
                            document.documentElement.style.setProperty('--guest-bg-base', s.customBg);
                            document.documentElement.style.setProperty('--guest-page-gradient', 'none');
                        }
                        if (s.customText) {
                            document.documentElement.style.setProperty('--guest-text-main', s.customText);
                            document.documentElement.style.setProperty('--guest-text-headings', s.customText);
                        }
                    }
                }
            } catch(e) {}
        })();
    </script>

    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        
        :root {
            /* Guest Theme Defaults (Clean Academic White) */
            --guest-bg-base: #ffffff;
            --guest-page-gradient: none;
            --guest-bg-surface: #ffffff;
            --guest-bg-sidebar: #ffffff;
            --guest-bg-input: #f8fafc;
            --guest-text-main: #334155;
            --guest-text-muted: #64748b;
            --guest-text-headings: #0f172a;
            --guest-border: #e2e8f0;
            --guest-card-border-grad: linear-gradient(135deg, rgba(16, 185, 129, 0.4) 0%, rgba(56, 189, 248, 0.3) 100%);
            --guest-card-shadow: 0 4px 18px rgba(15, 23, 42, 0.06), 0 1px 3px rgba(15, 23, 42, 0.04);
            --guest-accent: #0c4233;
            --guest-accent-glow: rgba(12, 66, 51, 0.18);
            --guest-nav-bg: rgba(255, 255, 255, 0.96);
            --guest-nav-border: #e2e8f0;
            --guest-overlay-opacity: 0.02;
            --guest-font-scale: 1rem;

            /* Backward compatibility aliases */
            --bg-base: var(--guest-bg-base);
            --bg-card: var(--guest-bg-surface);
            --cyan-accent: var(--guest-accent);
            --cyan-glow: var(--guest-accent-glow);
            --coral-accent: #10b981;
            --emerald-accent: #0c4233;
            --text-main: var(--guest-text-main);
            --text-muted: var(--guest-text-muted);
        }

        /* ── THEME PRESET: CLEAN LIGHT (Default White) ── */
        html[data-guest-theme="light"] {
            --guest-bg-base: #ffffff;
            --guest-page-gradient: none;
            --guest-bg-surface: #ffffff;
            --guest-bg-sidebar: #ffffff;
            --guest-bg-input: #f8fafc;
            --guest-text-main: #334155;
            --guest-text-muted: #64748b;
            --guest-text-headings: #0f172a;
            --guest-border: #e2e8f0;
            --guest-card-border-grad: linear-gradient(135deg, rgba(16, 185, 129, 0.4) 0%, rgba(56, 189, 248, 0.3) 100%);
            --guest-card-shadow: 0 4px 18px rgba(15, 23, 42, 0.06), 0 1px 3px rgba(15, 23, 42, 0.04);
            --guest-accent: #0c4233;
            --guest-accent-glow: rgba(12, 66, 51, 0.18);
            --guest-nav-bg: rgba(255, 255, 255, 0.96);
            --guest-nav-border: #e2e8f0;
            --guest-overlay-opacity: 0.02;
        }

        /* ── THEME PRESET: WARM SEPIA ── */
        html[data-guest-theme="sepia"] {
            --guest-bg-base: #fbf7ee;
            --guest-page-gradient: 
                radial-gradient(circle at 50% 20%, rgba(217, 119, 6, 0.05) 0%, transparent 60%),
                radial-gradient(ellipse at 50% 50%, #fdfbf7 0%, #f4eee2 100%);
            --guest-bg-surface: #ffffff;
            --guest-bg-sidebar: #f7f2e6;
            --guest-bg-input: #fbf7ee;
            --guest-text-main: #443c33;
            --guest-text-muted: #786f64;
            --guest-text-headings: #262019;
            --guest-border: #ded2c0;
            --guest-card-border-grad: linear-gradient(135deg, rgba(217, 119, 6, 0.45) 0%, rgba(180, 83, 9, 0.3) 100%);
            --guest-card-shadow: 0 4px 18px rgba(68, 60, 51, 0.07);
            --guest-accent: #b45309;
            --guest-accent-glow: rgba(180, 83, 9, 0.2);
            --guest-nav-bg: rgba(251, 247, 238, 0.96);
            --guest-nav-border: rgba(222, 210, 192, 0.9);
            --guest-overlay-opacity: 0.06;
        }
        
        html {
            font-size: var(--guest-font-scale, 1rem) !important;
        }

        #guestThemeWidgetContainer,
        #guestThemeTriggerWrap,
        #guestThemeModalOverlay .guest-theme-dialog {
            font-size: 16px !important;
        }

        html, body {
            min-height: 100vh;
            width: 100%;
            background-color: var(--guest-bg-base) !important;
            background-image: var(--guest-page-gradient) !important;
            background-attachment: fixed !important;
            color: var(--guest-text-main);
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            font-size: var(--guest-font-scale, 1rem);
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
            transition: background-color 0.25s ease, color 0.25s ease;
        }

        /* ===== CYBER GRID OVERLAY (Same as Login & Register Page) ===== */
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

        .public-page-wrapper {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Navigation (Fixed Position) */
        .public-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            z-index: 500;
            background: var(--guest-nav-bg, rgba(255, 255, 255, 0.96));
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border-bottom: 1px solid var(--guest-nav-border, #e2e8f0);
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
        }

        .public-nav .nav-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0.8rem 2rem;
            min-height: 72px;
        }

        .public-nav .nav-brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            transition: transform 0.25s ease;
        }

        .public-nav .nav-brand:hover {
            transform: translateY(-2px);
        }

        .nav-logo-badge {
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

        .public-nav .nav-brand:hover .nav-logo-badge {
            transform: scale(1.08);
            border-color: transparent;
            box-shadow: none;
        }

        .nav-logo-badge img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.15));
            transition: filter 0.25s ease;
        }

        .public-nav .nav-brand:hover .nav-logo-badge img {
            filter: drop-shadow(0 3px 12px var(--guest-accent-glow, rgba(12, 66, 51, 0.25)));
        }

        .nav-brand-text {
            display: flex;
            flex-direction: column;
            gap: 1px;
        }

        .nav-brand-title {
            font-size: 1.18rem;
            font-weight: 800;
            color: var(--guest-text-headings, #0f172a);
            letter-spacing: -0.02em;
            line-height: 1.15;
        }

        .nav-brand-title .brand-hub {
            color: var(--guest-accent, #0c4233);
        }

        .nav-brand-sub {
            font-size: 0.62rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            color: var(--guest-text-muted, #64748b);
            font-family: 'JetBrains Mono', monospace;
            text-transform: uppercase;
        }

        /* Nav Links */
        .public-nav .nav-links {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .public-nav .nav-link-item {
            padding: 0.45rem 1.15rem;
            border-radius: 9999px;
            font-size: 0.88rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            color: var(--guest-text-main, #334155);
            white-space: nowrap;
        }

        .public-nav .nav-link-item:hover {
            color: var(--guest-text-headings, #0f172a);
            background: rgba(0, 0, 0, 0.05);
        }

        .public-nav .nav-link-item.active {
            color: #ffffff;
            background: var(--guest-accent, #0c4233);
            box-shadow: 0 2px 8px var(--guest-accent-glow, rgba(12, 66, 51, 0.25));
            font-weight: 700;
        }

        .public-nav .nav-link-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-family: inherit;
        }

        .nav-login-link {
            padding: 0.45rem 1.15rem;
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--guest-text-main, #334155);
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .nav-login-link:hover {
            color: var(--guest-text-headings, #0f172a);
        }

        .nav-btn-started {
            text-decoration: none;
            color: #ffffff;
            background: var(--guest-accent, #0c4233);
            border: 1px solid var(--guest-accent, #0c4233);
            padding: 0.48rem 1.35rem;
            border-radius: 9999px;
            font-size: 0.88rem;
            font-weight: 700;
            box-shadow: 0 2px 8px var(--guest-accent-glow, rgba(12, 66, 51, 0.25));
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .nav-btn-started:hover {
            background: #082d23;
            border-color: #082d23;
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(12, 66, 51, 0.35);
            transform: translateY(-1px);
        }

        .public-nav .mobile-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--guest-text-headings, #0f172a);
            padding: 0.5rem;
        }

        /* Main Container */
        main.public-main-content {
            flex: 1;
            width: 100%;
            padding-top: 76px;
        }

        /* Universal Modals */
        .modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 2000;
            background: rgba(7, 12, 19, 0.82);
            backdrop-filter: blur(12px);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .modal-overlay.active {
            display: flex;
            animation: modalFadeIn 0.25s ease forwards;
        }

        @keyframes modalFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-card {
            background: #0f1926;
            border: 1px solid rgba(0, 245, 212, 0.25);
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6), 0 0 30px rgba(0, 245, 212, 0.12);
            padding: 2.2rem;
            max-width: 620px;
            width: 100%;
            max-height: 85vh;
            overflow-y: auto;
            position: relative;
            color: #e2e8f0;
        }

        .modal-close-btn {
            position: absolute;
            top: 1.2rem;
            right: 1.4rem;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #94a3b8;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .modal-close-btn:hover {
            color: #ffffff;
            background: rgba(16, 185, 129, 0.2);
            border-color: #10b981;
        }

        .modal-title {
            font-size: 1.35rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 1.1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-title svg {
            color: #00f5d4;
        }

        .modal-body {
            font-size: 0.92rem;
            line-height: 1.7;
            color: #94a3b8;
        }

        .modal-body strong {
            color: #f8fafc;
        }

        .modal-body ul {
            padding-left: 1.2rem;
            margin: 0.8rem 0;
        }

        .modal-body li {
            margin-bottom: 0.4rem;
        }

        @media (max-width: 820px) {
            .public-nav .nav-links {
                display: none;
            }
            .public-nav .mobile-toggle {
                display: block;
            }
            .public-nav .nav-links.active {
                display: flex;
                position: absolute;
                top: 72px;
                left: 1rem;
                right: 1rem;
                background: var(--guest-bg-surface, #ffffff);
                backdrop-filter: blur(20px);
                flex-direction: column;
                padding: 1.5rem;
                border-radius: 16px;
                border: 1px solid var(--guest-border, #e2e8f0);
                align-items: stretch;
                gap: 0.75rem;
                box-shadow: 0 16px 40px rgba(15, 23, 42, 0.15);
            }
            .public-nav .nav-link-item,
            .nav-login-link,
            .nav-btn-started {
                text-align: center;
                justify-content: center;
                width: 100%;
            }
        }

        /* ===== GUEST THEME ADAPTER RULES ===== */
        .cyber-grid-overlay {
            opacity: var(--guest-overlay-opacity, 1);
            transition: opacity 0.25s ease;
        }

        .public-nav {
            background: var(--guest-nav-bg, rgba(11, 17, 25, 0.88)) !important;
            border-bottom: 1px solid var(--guest-nav-border, rgba(255, 255, 255, 0.08)) !important;
            transition: background 0.25s ease, border-color 0.25s ease;
        }

        html[data-guest-theme="light"] .public-nav,
        html[data-guest-theme="sepia"] .public-nav {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        }

        html[data-guest-theme="light"] .nav-brand-title,
        html[data-guest-theme="sepia"] .nav-brand-title,
        html[data-guest-theme="custom"] .nav-brand-title {
            color: var(--guest-text-headings) !important;
        }

        .nav-brand-title .brand-hub {
            color: var(--guest-accent, #00e676) !important;
        }

        html[data-guest-theme="light"] .nav-brand-sub,
        html[data-guest-theme="sepia"] .nav-brand-sub,
        html[data-guest-theme="custom"] .nav-brand-sub {
            color: var(--guest-text-muted) !important;
        }

        html[data-guest-theme="light"] .public-nav .nav-link-item,
        html[data-guest-theme="sepia"] .public-nav .nav-link-item,
        html[data-guest-theme="custom"] .public-nav .nav-link-item {
            color: var(--guest-text-main) !important;
        }

        html[data-guest-theme="light"] .public-nav .nav-link-item:hover,
        html[data-guest-theme="sepia"] .public-nav .nav-link-item:hover,
        html[data-guest-theme="custom"] .public-nav .nav-link-item:hover {
            color: var(--guest-text-headings) !important;
            background: rgba(0, 0, 0, 0.05) !important;
        }

        .public-nav .nav-link-item.active {
            color: #ffffff !important;
            background: var(--guest-accent, #00e676) !important;
            box-shadow: 0 0 16px var(--guest-accent-glow, rgba(0, 230, 118, 0.45)) !important;
        }

        html[data-guest-theme="light"] .nav-login-link,
        html[data-guest-theme="sepia"] .nav-login-link,
        html[data-guest-theme="custom"] .nav-login-link {
            color: var(--guest-text-headings) !important;
        }

        /* Modals Adaptations */
        html[data-guest-theme="light"] .modal-card,
        html[data-guest-theme="sepia"] .modal-card,
        html[data-guest-theme="custom"] .modal-card {
            background: var(--guest-bg-surface) !important;
            border-color: var(--guest-border) !important;
            color: var(--guest-text-main) !important;
            box-shadow: var(--guest-card-shadow) !important;
        }

        html[data-guest-theme="light"] .modal-title,
        html[data-guest-theme="sepia"] .modal-title,
        html[data-guest-theme="custom"] .modal-title {
            color: var(--guest-text-headings) !important;
        }

        html[data-guest-theme="light"] .modal-body,
        html[data-guest-theme="sepia"] .modal-body,
        html[data-guest-theme="custom"] .modal-body {
            color: var(--guest-text-muted) !important;
        }

        html[data-guest-theme="light"] .modal-body strong,
        html[data-guest-theme="sepia"] .modal-body strong,
        html[data-guest-theme="custom"] .modal-body strong {
            color: var(--guest-text-headings) !important;
        }

        /* Browse Page Adaptations */
        html[data-guest-theme="light"] .browse-page-container,
        html[data-guest-theme="sepia"] .browse-page-container,
        html[data-guest-theme="custom"] .browse-page-container {
            color: var(--guest-text-main) !important;
        }

        html[data-guest-theme="light"] .filter-sidebar,
        html[data-guest-theme="sepia"] .filter-sidebar,
        html[data-guest-theme="custom"] .filter-sidebar {
            background: linear-gradient(var(--guest-bg-sidebar), var(--guest-bg-sidebar)) padding-box,
                        var(--guest-card-border-grad) border-box !important;
            box-shadow: var(--guest-card-shadow) !important;
        }

        html[data-guest-theme="light"] .filter-header-title,
        html[data-guest-theme="sepia"] .filter-header-title,
        html[data-guest-theme="custom"] .filter-header-title {
            color: var(--guest-text-headings) !important;
        }

        html[data-guest-theme="light"] .filter-section-title,
        html[data-guest-theme="sepia"] .filter-section-title,
        html[data-guest-theme="custom"] .filter-section-title {
            color: var(--guest-text-muted) !important;
        }

        html[data-guest-theme="light"] .filter-item-label,
        html[data-guest-theme="sepia"] .filter-item-label,
        html[data-guest-theme="custom"] .filter-item-label {
            color: var(--guest-text-main) !important;
        }

        html[data-guest-theme="light"] .filter-item-label:hover,
        html[data-guest-theme="sepia"] .filter-item-label:hover,
        html[data-guest-theme="custom"] .filter-item-label:hover {
            color: var(--guest-text-headings) !important;
        }

        html[data-guest-theme="light"] .filter-search-input,
        html[data-guest-theme="sepia"] .filter-search-input,
        html[data-guest-theme="custom"] .filter-search-input {
            background: var(--guest-bg-input) !important;
            color: var(--guest-text-headings) !important;
            border-color: var(--guest-border) !important;
        }

        html[data-guest-theme="light"] .projects-title,
        html[data-guest-theme="sepia"] .projects-title,
        html[data-guest-theme="custom"] .projects-title {
            color: var(--guest-text-headings) !important;
        }

        html[data-guest-theme="light"] .projects-subtitle,
        html[data-guest-theme="sepia"] .projects-subtitle,
        html[data-guest-theme="custom"] .projects-subtitle {
            color: var(--guest-text-muted) !important;
        }

        html[data-guest-theme="light"] .capstone-card,
        html[data-guest-theme="sepia"] .capstone-card,
        html[data-guest-theme="custom"] .capstone-card {
            background: linear-gradient(var(--guest-bg-surface), var(--guest-bg-surface)) padding-box,
                        var(--guest-card-border-grad) border-box !important;
            box-shadow: var(--guest-card-shadow) !important;
        }

        html[data-guest-theme="light"] .card-title-link,
        html[data-guest-theme="sepia"] .card-title-link,
        html[data-guest-theme="custom"] .card-title-link {
            color: var(--guest-text-headings) !important;
        }

        html[data-guest-theme="light"] .card-author-name,
        html[data-guest-theme="sepia"] .card-author-name,
        html[data-guest-theme="custom"] .card-author-name {
            color: var(--guest-text-main) !important;
        }

        html[data-guest-theme="light"] .card-abstract-text,
        html[data-guest-theme="sepia"] .card-abstract-text,
        html[data-guest-theme="custom"] .card-abstract-text {
            color: var(--guest-text-muted) !important;
        }

        html[data-guest-theme="light"] .sort-dropdown,
        html[data-guest-theme="sepia"] .sort-dropdown,
        html[data-guest-theme="custom"] .sort-dropdown {
            background: var(--guest-bg-surface) !important;
            color: var(--guest-text-headings) !important;
            border-color: var(--guest-border) !important;
        }

        html[data-guest-theme="light"] .sort-dropdown option,
        html[data-guest-theme="sepia"] .sort-dropdown option {
            background: #ffffff !important;
            color: #0f172a !important;
        }

        .btn-view-project,
        .sim-public-btn {
            background: var(--guest-accent, #10b981) !important;
            box-shadow: 0 4px 18px var(--guest-accent-glow, rgba(16, 185, 129, 0.45)) !important;
        }

        /* Similarity Checker Page Adaptations */
        html[data-guest-theme="light"] .sim-public-card,
        html[data-guest-theme="sepia"] .sim-public-card,
        html[data-guest-theme="custom"] .sim-public-card {
            background: linear-gradient(var(--guest-bg-surface), var(--guest-bg-surface)) padding-box,
                        var(--guest-card-border-grad) border-box !important;
            box-shadow: var(--guest-card-shadow) !important;
        }

        html[data-guest-theme="light"] .sim-public-input,
        html[data-guest-theme="sepia"] .sim-public-input,
        html[data-guest-theme="custom"] .sim-public-input {
            background: var(--guest-bg-input) !important;
            color: var(--guest-text-headings) !important;
            border-color: var(--guest-border) !important;
        }

        html[data-guest-theme="light"] .sim-result-card-public,
        html[data-guest-theme="sepia"] .sim-result-card-public,
        html[data-guest-theme="custom"] .sim-result-card-public {
            background: var(--guest-bg-surface) !important;
            border-color: var(--guest-border) !important;
            box-shadow: var(--guest-card-shadow) !important;
        }

        html[data-guest-theme="light"] .sim-wrapper .text-white,
        html[data-guest-theme="sepia"] .sim-wrapper .text-white,
        html[data-guest-theme="custom"] .sim-wrapper .text-white {
            color: var(--guest-text-headings) !important;
        }

        html[data-guest-theme="light"] .sim-wrapper .text-slate-400,
        html[data-guest-theme="sepia"] .sim-wrapper .text-slate-400,
        html[data-guest-theme="custom"] .sim-wrapper .text-slate-400 {
            color: var(--guest-text-muted) !important;
        }

        html[data-guest-theme="light"] .sim-wrapper .text-slate-300,
        html[data-guest-theme="sepia"] .sim-wrapper .text-slate-300,
        html[data-guest-theme="custom"] .sim-wrapper .text-slate-300 {
            color: var(--guest-text-main) !important;
        }

        html[data-guest-theme="light"] .sim-wrapper .border-slate-800,
        html[data-guest-theme="sepia"] .sim-wrapper .border-slate-800,
        html[data-guest-theme="custom"] .sim-wrapper .border-slate-800 {
            border-color: var(--guest-border) !important;
        }

        html[data-guest-theme="light"] .sim-wrapper .bg-slate-900\/80,
        html[data-guest-theme="sepia"] .sim-wrapper .bg-slate-900\/80,
        html[data-guest-theme="custom"] .sim-wrapper .bg-slate-900\/80 {
            background-color: var(--guest-bg-input) !important;
        }

        html[data-guest-theme="light"] .sim-wrapper .border-slate-700\/80,
        html[data-guest-theme="sepia"] .sim-wrapper .border-slate-700\/80,
        html[data-guest-theme="custom"] .sim-wrapper .border-slate-700\/80 {
            border-color: var(--guest-border) !important;
        }

        html[data-guest-theme="light"] .sim-result-card-public strong.text-slate-200,
        html[data-guest-theme="sepia"] .sim-result-card-public strong.text-slate-200,
        html[data-guest-theme="custom"] .sim-result-card-public strong.text-slate-200 {
            color: var(--guest-text-headings) !important;
        }

        html[data-guest-theme="light"] .sim-wrapper .bg-slate-900\/90,
        html[data-guest-theme="sepia"] .sim-wrapper .bg-slate-900\/90,
        html[data-guest-theme="custom"] .sim-wrapper .bg-slate-900\/90 {
            background-color: var(--guest-bg-surface) !important;
            border-color: var(--guest-border) !important;
            color: var(--guest-text-main) !important;
        }

        html[data-guest-theme="light"] .custom-checkbox:not(:checked) {
            background: #ffffff !important;
            border: 2px solid #64748b !important;
        }

        html[data-guest-theme="light"] .custom-checkbox:checked {
            background: var(--guest-accent, #0c4233) !important;
            border-color: var(--guest-accent, #0c4233) !important;
        }

        html[data-guest-theme="sepia"] .custom-checkbox:not(:checked) {
            background: #fbf7ee !important;
            border: 2px solid #8c8273 !important;
        }

        html[data-guest-theme="sepia"] .custom-checkbox:checked {
            background: var(--guest-accent, #0c4233) !important;
            border-color: var(--guest-accent, #0c4233) !important;
        }

        /* Dynamic Reading Text Size for Research Titles & Abstracts */
        .card-title-link,
        .sd-research-title,
        .table-container-card td a,
        .sim-result-title {
            font-size: calc(1.05rem * var(--guest-font-multiplier, 1)) !important;
            line-height: 1.4 !important;
        }

        .abstract-preview,
        .sd-research-abstract,
        .sim-abstract-preview,
        .manuscript-abstract,
        .sim-result-abstract {
            font-size: calc(0.92rem * var(--guest-font-multiplier, 1)) !important;
            line-height: 1.65 !important;
        }

        ::selection {
            background: var(--guest-accent, #00f5d4) !important;
            color: var(--guest-accent-text, #042f2e) !important;
        }

        mark, .search-highlight, .highlight-term {
            background: var(--guest-accent-glow, rgba(0, 245, 212, 0.35)) !important;
            color: var(--guest-text-headings, #ffffff) !important;
            border-bottom: 2px solid var(--guest-accent, #00f5d4) !important;
        }
    </style>
</head>
<body>
    <div class="cyber-grid-overlay" aria-hidden="true"></div>

    <div class="public-page-wrapper">
        {{-- Navigation Bar --}}
        <nav class="public-nav">
            <div class="nav-inner">
                {{-- Brand --}}
                <a href="{{ url('/') }}" class="nav-brand">
                    <div class="nav-logo-badge">
                        <img src="{{ asset('images/scs.png') }}" alt="SCS Logo">
                    </div>
                    <div class="nav-brand-text">
                        <span class="nav-brand-title">capstone<span class="brand-hub">Hub</span></span>
                        <span class="nav-brand-sub">CAPSTONE REPOSITORY</span>
                    </div>
                </a>

                {{-- Mobile Hamburger --}}
                <button class="mobile-toggle" onclick="document.getElementById('navLinks').classList.toggle('active')" aria-label="Toggle Navigation">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <line x1="3" y1="12" x2="21" y2="12"/>
                        <line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>

                {{-- Navigation Links --}}
                <div class="nav-links" id="navLinks">
                    <a href="{{ route('repository.index') }}" class="nav-link-item {{ (request()->routeIs('repository.*') || request()->routeIs('browse')) ? 'active' : '' }}">
                        Browse
                    </a>
                    <a href="{{ route('similarity-checker') }}" class="nav-link-item {{ request()->routeIs('similarity-checker*') ? 'active' : '' }}">
                        Similarity
                    </a>

                    @if (Route::has('login'))
                        @auth
                            <!-- Desktop Dropdown -->
                            <div class="hidden md:block">
                                <x-dropdown align="right" width="48" contentClasses="py-1 bg-[#0f1926] border border-cyan-400/30 text-white rounded-xl shadow-2xl">
                                    <x-slot name="trigger">
                                        <button class="flex items-center gap-2 px-3 py-1.5 rounded-full hover:bg-white/10 transition cursor-pointer border border-cyan-400/35 bg-white/5 text-white focus:outline-none">
                                            <div class="w-6 h-6 rounded-full bg-cyan-400 text-[#042f2e] font-extrabold text-xs flex items-center justify-center">
                                                {{ substr(Auth::user()->name, 0, 1) }}
                                            </div>
                                            <span class="text-xs font-semibold text-slate-200">{{ Auth::user()->name }}</span>
                                            <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                    </x-slot>

                                    <x-slot name="content">
                                        <div class="px-4 py-2 border-b border-white/10">
                                            <div class="text-xs font-bold text-white">{{ Auth::user()->name }}</div>
                                            <div class="text-[10px] text-slate-400 truncate">{{ Auth::user()->email }}</div>
                                            <div class="text-[9px] text-cyan-400 uppercase tracking-wider font-semibold mt-0.5">
                                                {{ Auth::user()->role }}
                                            </div>
                                        </div>
                                        
                                        @if(Auth::user()->role === 'admin')
                                            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-xs text-slate-300 hover:text-white hover:bg-white/10 transition no-underline">
                                                📊 Dashboard
                                            </a>
                                        @else
                                            <a href="{{ route('student.dashboard') }}" class="block px-4 py-2 text-xs text-slate-300 hover:text-white hover:bg-white/10 transition no-underline">
                                                📊 Dashboard
                                            </a>
                                        @endif
                                        
                                        <div class="border-t border-white/10 my-1"></div>
                                        
                                        <form method="POST" action="{{ route('logout') }}" class="m-0" id="logout-form-public">
                                            @csrf
                                            <button type="submit" onclick="this.closest('form').submit();" class="w-full text-left px-4 py-2 text-xs text-red-400 hover:text-red-300 hover:bg-red-500/10 transition cursor-pointer border-0 bg-transparent flex items-center gap-1.5">
                                                🚪 Log Out
                                            </button>
                                        </form>
                                    </x-slot>
                                </x-dropdown>
                            </div>

                            <!-- Mobile Auth Links -->
                            <div class="md:hidden flex flex-col items-center gap-2 w-full pt-2 border-t border-white/10 mt-2">
                                <div class="text-center py-1">
                                    <div class="text-xs font-semibold text-white">{{ Auth::user()->name }}</div>
                                    <div class="text-[10px] text-slate-400">{{ Auth::user()->email }}</div>
                                </div>
                                <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('student.dashboard') }}" class="nav-btn-started w-full text-center">Dashboard</a>
                                <form method="POST" action="{{ route('logout') }}" class="w-full m-0">
                                    @csrf
                                    <button type="submit" class="w-full text-center bg-red-600/10 text-red-400 border border-red-500/20 py-2 rounded-full text-xs font-semibold hover:bg-red-600/20 transition cursor-pointer">
                                        Log Out
                                    </button>
                                </form>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="nav-login-link">Log In</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="nav-btn-started">
                                    Get Started
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </nav>

        {{-- Page Content --}}
        <main class="public-main-content">
            @yield('content')
        </main>
    </div>

    {{-- Universal Modal Overlays --}}
    <!-- About Modal -->
    <div class="modal-overlay" id="aboutModal" onclick="closeOnBackdrop(event, 'aboutModal')">
        <div class="modal-card">
            <button class="modal-close-btn" onclick="closePublicModal('aboutModal')">&times;</button>
            <h2 class="modal-title">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
                </svg>
                About capstoneHub
            </h2>
            <div class="modal-body">
                <p style="margin-bottom: 0.8rem;">
                    <strong>capstoneHub</strong> is the official digital capstone research repository system of the <strong>School of Computing Studies (SCS)</strong> at J.H. Cerilles State College (JHCSC), partnered with the <strong>Cyberian Club</strong>.
                </p>
                <p style="margin-bottom: 0.8rem;">
                    Our mission is to safeguard, categorize, and showcase the innovative technological achievements of students and faculty researchers in Computer Science and Information Technology.
                </p>
                <ul style="list-style-type: disc;">
                    <li><strong>School:</strong> School of Computing Studies (SCS)</li>
                    <li><strong>Partner:</strong> Cyberian Club (BSIT Student Organization)</li>
                    <li><strong>Institution:</strong> J.H. Cerilles State College (JHCSC)</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Terms Modal -->
    <div class="modal-overlay" id="termsModal" onclick="closeOnBackdrop(event, 'termsModal')">
        <div class="modal-card">
            <button class="modal-close-btn" onclick="closePublicModal('termsModal')">&times;</button>
            <h2 class="modal-title">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
                Terms of Use
            </h2>
            <div class="modal-body">
                <p style="margin-bottom: 0.8rem;">
                    By accessing <strong>capstoneHub</strong>, you agree to comply with academic integrity standards and intellectual property guidelines enforced by the School of Computing Studies.
                </p>
                <ul style="list-style-type: disc;">
                    <li>Archived capstone projects are for academic, educational, and research reference only.</li>
                    <li>Plagiarism, unauthorized commercial reproduction, or redistribution is strictly prohibited.</li>
                    <li>All research works remain the intellectual property of their respective student authors and advisers.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Privacy Modal -->
    <div class="modal-overlay" id="privacyModal" onclick="closeOnBackdrop(event, 'privacyModal')">
        <div class="modal-card">
            <button class="modal-close-btn" onclick="closePublicModal('privacyModal')">&times;</button>
            <h2 class="modal-title">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
                Privacy Policy
            </h2>
            <div class="modal-body">
                <p style="margin-bottom: 0.8rem;">
                    Your privacy and data security are central to capstoneHub.
                </p>
                <ul style="list-style-type: disc;">
                    <li>Personal information collected during registration is used solely for verification within JHCSC.</li>
                    <li>Uploaded manuscripts are processed using AI embeddings strictly for repository classification and similarity checking.</li>
                    <li>We do not sell, rent, or share personal data with external third parties.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Help Modal -->
    <div class="modal-overlay" id="helpModal" onclick="closeOnBackdrop(event, 'helpModal')">
        <div class="modal-card">
            <button class="modal-close-btn" onclick="closePublicModal('helpModal')">&times;</button>
            <h2 class="modal-title">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                Help & Support
            </h2>
            <div class="modal-body">
                <p style="margin-bottom: 0.8rem;">
                    Need assistance navigating the repository or submitting your capstone manuscript?
                </p>
                <ul style="list-style-type: disc;">
                    <li><strong>Browsing:</strong> Use the left-side category, author, and year filters to narrow down research manuscripts.</li>
                    <li><strong>Similarity Checking:</strong> Navigate to the <em>Similarity</em> tab to analyze novel project ideas.</li>
                    <li><strong>Contact:</strong> Inquire directly with the School of Computing Studies department or Cyberian Club student officers.</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        function openPublicModal(modalId) {
            const m = document.getElementById(modalId);
            if (m) m.classList.add('active');
        }
        function closePublicModal(modalId) {
            const m = document.getElementById(modalId);
            if (m) m.classList.remove('active');
        }
        function closeOnBackdrop(e, modalId) {
            if (e.target.id === modalId) closePublicModal(modalId);
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-overlay.active').forEach(m => m.classList.remove('active'));
            }
        });
    </script>

    {{-- Guest Theme & Display Customizer (Draggable FAB & Settings Modal) --}}
    @include('components.guest-theme-widget')
</body>
</html>
