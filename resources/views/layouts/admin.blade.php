<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') — JHCSC Capstone Repository</title>

    <!-- Pre-paint Script for Sidebar FOUC & Display Theme Engine -->
    <script>
        (function() {
            if (localStorage.getItem('admin_sidebar_collapsed') === 'true') {
                document.documentElement.classList.add('admin-sidebar-is-collapsed');
            }
            try {
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

                var raw = localStorage.getItem('capstone_guest_display_theme');
                if (raw) {
                    var s = JSON.parse(raw);
                    if (s.mode) {
                        document.documentElement.setAttribute('data-guest-theme', s.mode);
                        document.documentElement.setAttribute('data-student-theme', s.mode);
                        document.documentElement.setAttribute('data-admin-theme', s.mode);
                    }
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
                } else {
                    var saved = localStorage.getItem('student_theme_mode') || 'dark';
                    document.documentElement.setAttribute('data-guest-theme', saved);
                    document.documentElement.setAttribute('data-student-theme', saved);
                    document.documentElement.setAttribute('data-admin-theme', saved);
                }
            } catch(e) {}
        })();
    </script>

    <!-- Fonts & Chart.js -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        html {
            font-size: var(--guest-font-scale, 1rem) !important;
        }

        /* Prevent theme modal and floating widget from scaling awkwardly */
        #guestThemeWidgetContainer,
        #guestThemeTriggerWrap,
        #guestThemeModalOverlay .guest-theme-dialog {
            font-size: 16px !important;
        }
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--guest-bg-base, #0c4233) !important;
            background-image: var(--guest-page-gradient, none) !important;
            background-attachment: fixed !important;
            color: var(--guest-text-main, #2d2b56);
            min-height: 100vh;
            overflow-x: hidden;
            transition: background-color 0.25s ease, color 0.25s ease;
        }

        /* ═══════════════════════════════════════════════════════════════
           THEME ENGINE (CYBER DARK, LIGHT, SEPIA, OLED, CUSTOM)
        ═══════════════════════════════════════════════════════════════ */
        :root,
        html[data-guest-theme="dark"],
        html[data-admin-theme="dark"],
        html[data-student-theme="dark"] {
            --guest-bg-base: #0b1119;
            --guest-page-gradient: 
                radial-gradient(circle at 50% 24%, rgba(16, 185, 129, 0.20) 0%, rgba(56, 189, 248, 0.10) 35%, transparent 65%),
                radial-gradient(circle at 16% 48%, rgba(16, 185, 129, 0.18) 0%, transparent 45%),
                radial-gradient(circle at 84% 48%, rgba(0, 245, 212, 0.12) 0%, transparent 45%),
                radial-gradient(circle at 50% 92%, rgba(56, 189, 248, 0.10) 0%, transparent 52%),
                radial-gradient(ellipse at 50% 45%, #152538 0%, #0b1119 100%);
            --guest-bg-surface: rgba(14, 22, 33, 0.94);
            --guest-bg-sidebar: rgba(13, 21, 32, 0.92);
            --guest-bg-input: rgba(8, 14, 22, 0.85);
            --guest-text-main: #f8fafc;
            --guest-text-muted: #94a3b8;
            --guest-text-headings: #ffffff;
            --guest-border: rgba(255, 255, 255, 0.12);
            --guest-card-border-grad: linear-gradient(135deg, var(--guest-accent, #00f5d4) 0%, #06b6d4 35%, #8b5cf6 65%, #ef4444 100%);
            --guest-card-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
            --guest-accent: #00f5d4;
            --guest-accent-glow: rgba(0, 245, 212, 0.35);
        }

        /* CLEAN LIGHT PRESET */
        html[data-guest-theme="light"],
        html[data-admin-theme="light"],
        html[data-student-theme="light"] {
            --guest-bg-base: #f0fdf4;
            --guest-page-gradient: 
                radial-gradient(circle at 50% 18%, rgba(16, 185, 129, 0.08) 0%, rgba(56, 189, 248, 0.06) 35%, transparent 60%),
                radial-gradient(ellipse at 50% 45%, #f8fafc 0%, #e2e8f0 100%);
            --guest-bg-surface: #ffffff;
            --guest-bg-sidebar: #0c4233;
            --guest-bg-input: #f8fafc;
            --guest-text-main: #334155;
            --guest-text-muted: #64748b;
            --guest-text-headings: #0f172a;
            --guest-border: #cbd5e1;
            --guest-card-border-grad: linear-gradient(135deg, var(--guest-accent, #059669) 0%, rgba(6, 182, 212, 0.4) 40%, rgba(139, 92, 246, 0.35) 100%);
            --guest-card-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
            --guest-accent: #059669;
            --guest-accent-glow: rgba(5, 150, 105, 0.2);
        }

        /* WARM SEPIA PRESET */
        html[data-guest-theme="sepia"],
        html[data-admin-theme="sepia"],
        html[data-student-theme="sepia"] {
            --guest-bg-base: #fbf7ee;
            --guest-page-gradient: 
                radial-gradient(circle at 50% 20%, rgba(217, 119, 6, 0.05) 0%, transparent 60%),
                radial-gradient(ellipse at 50% 50%, #fdfbf7 0%, #f4eee2 100%);
            --guest-bg-surface: #ffffff;
            --guest-bg-sidebar: #1d1813;
            --guest-bg-input: #fbf7ee;
            --guest-text-main: #443c33;
            --guest-text-muted: #786f64;
            --guest-text-headings: #262019;
            --guest-border: #ded2c0;
            --guest-card-border-grad: linear-gradient(135deg, var(--guest-accent, #b45309) 0%, rgba(180, 83, 9, 0.3) 100%);
            --guest-card-shadow: 0 4px 18px rgba(68, 60, 51, 0.07);
            --guest-accent: #b45309;
            --guest-accent-glow: rgba(180, 83, 9, 0.2);
        }

        /* MIDNIGHT OLED PRESET */
        html[data-guest-theme="oled"],
        html[data-admin-theme="oled"],
        html[data-student-theme="oled"] {
            --guest-bg-base: #000000;
            --guest-page-gradient: none;
            --guest-bg-surface: #0a0a0a;
            --guest-bg-sidebar: #090909;
            --guest-bg-input: #141414;
            --guest-text-main: #e4e4e7;
            --guest-text-muted: #a1a1aa;
            --guest-text-headings: #ffffff;
            --guest-border: #27272a;
            --guest-card-border-grad: linear-gradient(135deg, var(--guest-accent, #00f5d4) 0%, #8b5cf6 100%);
            --guest-card-shadow: 0 6px 24px rgba(0, 0, 0, 0.95);
            --guest-accent: #00f5d4;
            --guest-accent-glow: rgba(0, 245, 212, 0.3);
        }

        /* CUSTOM CANVAS PRESET */
        html[data-guest-theme="custom"],
        html[data-admin-theme="custom"],
        html[data-student-theme="custom"] {
            --guest-page-gradient: none;
        }

        /* ── CANVAS & LAYOUT WRAPPERS ── */
        @media (min-width: 1024px) {
            .admin-sidebar-fixed {
                width: 240px;
                transition: width 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .admin-main-wrapper {
                margin-left: 240px;
                width: calc(100% - 240px);
                min-height: 100vh;
                padding: 0;
                transition: margin-left 0.35s cubic-bezier(0.4, 0, 0.2, 1), width 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .admin-main-canvas {
                background-color: var(--guest-bg-surface, #ffffff) !important;
                background-image: var(--guest-page-gradient, none) !important;
                background-attachment: fixed !important;
                color: var(--guest-text-main, #2d2b56) !important;
                border-radius: 0 !important;
                min-height: 100vh;
                box-shadow: none !important;
                padding: 36px 40px;
                transition: background-color 0.25s ease, color 0.25s ease;
            }

            html.admin-sidebar-is-collapsed .admin-sidebar-fixed,
            .admin-sidebar-fixed.is-collapsed {
                width: 80px;
            }

            html.admin-sidebar-is-collapsed .admin-main-wrapper,
            .admin-main-wrapper.is-collapsed {
                margin-left: 80px;
                width: calc(100% - 80px);
            }
        }

        @media (max-width: 1023px) {
            .admin-main-wrapper {
                width: 100%;
                margin-left: 0;
                padding: 0;
            }
            .admin-main-canvas {
                background-color: var(--guest-bg-surface, #ffffff) !important;
                background-image: var(--guest-page-gradient, none) !important;
                background-attachment: fixed !important;
                color: var(--guest-text-main, #2d2b56) !important;
                border-radius: 0 !important;
                min-height: 100vh;
                box-shadow: none !important;
                padding: 24px 20px;
                transition: background-color 0.25s ease, color 0.25s ease;
            }
        }

        /* ── DARK & OLED THEME ADAPTIVE RULES FOR ADMIN VIEWS ── */
        html[data-guest-theme="dark"] .admin-sidebar-fixed,
        html[data-guest-theme="oled"] .admin-sidebar-fixed,
        html[data-admin-theme="dark"] .admin-sidebar-fixed,
        html[data-admin-theme="oled"] .admin-sidebar-fixed {
            background: var(--guest-bg-sidebar, #0d1520) !important;
            border-right: 1px solid var(--guest-border, rgba(255, 255, 255, 0.12)) !important;
        }

        html[data-guest-theme="sepia"] .admin-sidebar-fixed,
        html[data-admin-theme="sepia"] .admin-sidebar-fixed {
            background: #1d1813 !important;
            border-right: 1px solid #3d3429 !important;
        }

        /* Typography */
        html[data-guest-theme="dark"] h1,
        html[data-guest-theme="dark"] h2,
        html[data-guest-theme="dark"] h3,
        html[data-guest-theme="dark"] h4,
        html[data-admin-theme="dark"] h1,
        html[data-admin-theme="dark"] h2,
        html[data-admin-theme="dark"] h3,
        html[data-admin-theme="dark"] h4,
        html[data-guest-theme="oled"] h1,
        html[data-guest-theme="oled"] h2,
        html[data-guest-theme="oled"] h3,
        html[data-guest-theme="oled"] h4,
        html[data-admin-theme="oled"] h1,
        html[data-admin-theme="oled"] h2,
        html[data-admin-theme="oled"] h3,
        html[data-admin-theme="oled"] h4,
        html[data-guest-theme="dark"] .text-\[\#2d2b56\],
        html[data-guest-theme="oled"] .text-\[\#2d2b56\],
        html[data-admin-theme="dark"] .text-\[\#2d2b56\],
        html[data-admin-theme="oled"] .text-\[\#2d2b56\] {
            color: #ffffff !important;
        }

        html[data-guest-theme="dark"] .text-\[\#0c4233\],
        html[data-guest-theme="oled"] .text-\[\#0c4233\],
        html[data-admin-theme="dark"] .text-\[\#0c4233\],
        html[data-admin-theme="oled"] .text-\[\#0c4233\] {
            color: var(--guest-accent, #00f5d4) !important;
        }

        html[data-guest-theme="dark"] .text-slate-500,
        html[data-guest-theme="dark"] .text-slate-400,
        html[data-guest-theme="dark"] .text-slate-600,
        html[data-guest-theme="oled"] .text-slate-500,
        html[data-guest-theme="oled"] .text-slate-400,
        html[data-guest-theme="oled"] .text-slate-600,
        html[data-admin-theme="dark"] .text-slate-500,
        html[data-admin-theme="dark"] .text-slate-400,
        html[data-admin-theme="dark"] .text-slate-600,
        html[data-admin-theme="oled"] .text-slate-500,
        html[data-admin-theme="oled"] .text-slate-400,
        html[data-admin-theme="oled"] .text-slate-600 {
            color: var(--guest-text-muted, #94a3b8) !important;
        }

        /* Admin Cards: Stat Cards, Tables, Detail Cards, Upload Forms */
        html[data-guest-theme="dark"] .stat-card-admin,
        html[data-guest-theme="dark"] .section-card-admin,
        html[data-guest-theme="dark"] .table-container-card,
        html[data-guest-theme="dark"] .card-detail-section,
        html[data-guest-theme="dark"] .upload-form-card,
        html[data-guest-theme="oled"] .stat-card-admin,
        html[data-guest-theme="oled"] .section-card-admin,
        html[data-guest-theme="oled"] .table-container-card,
        html[data-guest-theme="oled"] .card-detail-section,
        html[data-guest-theme="oled"] .upload-form-card,
        html[data-admin-theme="dark"] .stat-card-admin,
        html[data-admin-theme="dark"] .section-card-admin,
        html[data-admin-theme="dark"] .table-container-card,
        html[data-admin-theme="dark"] .card-detail-section,
        html[data-admin-theme="dark"] .upload-form-card,
        html[data-admin-theme="oled"] .stat-card-admin,
        html[data-admin-theme="oled"] .section-card-admin,
        html[data-admin-theme="oled"] .table-container-card,
        html[data-admin-theme="oled"] .card-detail-section,
        html[data-admin-theme="oled"] .upload-form-card {
            background: linear-gradient(var(--guest-bg-surface), var(--guest-bg-surface)) padding-box,
                        var(--guest-card-border-grad) border-box !important;
            border: 1.5px solid transparent !important;
            color: #f8fafc !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.45), 0 0 18px rgba(0, 245, 212, 0.08) !important;
            backdrop-filter: blur(16px) !important;
            -webkit-backdrop-filter: blur(16px) !important;
        }

        /* Inputs, Search Bars, Textareas, Selects */
        html[data-guest-theme="dark"] .form-input-custom,
        html[data-guest-theme="dark"] input[type="text"]:not(.native-color-picker),
        html[data-guest-theme="dark"] input[type="number"],
        html[data-guest-theme="dark"] input[type="search"],
        html[data-guest-theme="dark"] textarea,
        html[data-guest-theme="dark"] select,
        html[data-guest-theme="oled"] .form-input-custom,
        html[data-guest-theme="oled"] input[type="text"]:not(.native-color-picker),
        html[data-guest-theme="oled"] input[type="number"],
        html[data-guest-theme="oled"] input[type="search"],
        html[data-guest-theme="oled"] textarea,
        html[data-guest-theme="oled"] select,
        html[data-admin-theme="dark"] .form-input-custom,
        html[data-admin-theme="dark"] input[type="text"]:not(.native-color-picker),
        html[data-admin-theme="dark"] input[type="number"],
        html[data-admin-theme="dark"] input[type="search"],
        html[data-admin-theme="dark"] textarea,
        html[data-admin-theme="dark"] select,
        html[data-admin-theme="oled"] .form-input-custom,
        html[data-admin-theme="oled"] input[type="text"]:not(.native-color-picker),
        html[data-admin-theme="oled"] input[type="number"],
        html[data-admin-theme="oled"] input[type="search"],
        html[data-admin-theme="oled"] textarea,
        html[data-admin-theme="oled"] select {
            background: rgba(8, 14, 22, 0.85) !important;
            border-color: rgba(255, 255, 255, 0.18) !important;
            color: #ffffff !important;
        }

        html[data-guest-theme="dark"] .form-input-custom:focus,
        html[data-guest-theme="dark"] input:focus,
        html[data-guest-theme="dark"] textarea:focus,
        html[data-guest-theme="dark"] select:focus,
        html[data-guest-theme="oled"] .form-input-custom:focus,
        html[data-guest-theme="oled"] input:focus,
        html[data-guest-theme="oled"] textarea:focus,
        html[data-guest-theme="oled"] select:focus,
        html[data-admin-theme="dark"] .form-input-custom:focus,
        html[data-admin-theme="dark"] input:focus,
        html[data-admin-theme="dark"] textarea:focus,
        html[data-admin-theme="dark"] select:focus,
        html[data-admin-theme="oled"] .form-input-custom:focus,
        html[data-admin-theme="oled"] input:focus,
        html[data-admin-theme="oled"] textarea:focus,
        html[data-admin-theme="oled"] select:focus {
            background: rgba(12, 20, 30, 0.95) !important;
            border-color: var(--guest-accent, #00f5d4) !important;
            box-shadow: 0 0 0 4px var(--guest-accent-glow, rgba(0, 245, 212, 0.22)) !important;
        }

        /* Checkboxes & Cards */
        html[data-guest-theme="dark"] .check-card,
        html[data-guest-theme="oled"] .check-card,
        html[data-admin-theme="dark"] .check-card,
        html[data-admin-theme="oled"] .check-card {
            background: rgba(8, 14, 22, 0.85) !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
            color: #e2e8f0 !important;
        }

        html[data-guest-theme="dark"] .check-card:hover,
        html[data-guest-theme="oled"] .check-card:hover,
        html[data-admin-theme="dark"] .check-card:hover,
        html[data-admin-theme="oled"] .check-card:hover {
            border-color: var(--guest-accent, #00f5d4) !important;
            background: rgba(0, 245, 212, 0.08) !important;
        }

        html[data-guest-theme="dark"] .check-card.checked,
        html[data-guest-theme="dark"] .check-card.is-checked,
        html[data-guest-theme="oled"] .check-card.checked,
        html[data-guest-theme="oled"] .check-card.is-checked,
        html[data-admin-theme="dark"] .check-card.checked,
        html[data-admin-theme="dark"] .check-card.is-checked,
        html[data-admin-theme="oled"] .check-card.checked,
        html[data-admin-theme="oled"] .check-card.is-checked {
            border-color: var(--guest-accent, #00f5d4) !important;
            background: var(--guest-accent-glow, rgba(0, 245, 212, 0.14)) !important;
            color: #ffffff !important;
        }

        /* Tables & Lists */
        html[data-guest-theme="dark"] table th,
        html[data-guest-theme="oled"] table th,
        html[data-admin-theme="dark"] table th,
        html[data-admin-theme="oled"] table th {
            background: rgba(8, 14, 22, 0.95) !important;
            color: #94a3b8 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }

        html[data-guest-theme="dark"] table td,
        html[data-guest-theme="oled"] table td,
        html[data-admin-theme="dark"] table td,
        html[data-admin-theme="oled"] table td {
            border-color: rgba(255, 255, 255, 0.08) !important;
            color: #f8fafc !important;
        }

        html[data-guest-theme="dark"] table tr:hover td,
        html[data-guest-theme="oled"] table tr:hover td,
        html[data-admin-theme="dark"] table tr:hover td,
        html[data-admin-theme="oled"] table tr:hover td,
        html[data-guest-theme="dark"] .hover\:bg-slate-50\/70:hover,
        html[data-guest-theme="oled"] .hover\:bg-slate-50\/70:hover,
        html[data-admin-theme="dark"] .hover\:bg-slate-50\/70:hover,
        html[data-admin-theme="oled"] .hover\:bg-slate-50\/70:hover {
            background: rgba(255, 255, 255, 0.04) !important;
        }

        /* Modals in Admin */
        html[data-guest-theme="dark"] .modal-backdrop-custom > div,
        html[data-guest-theme="oled"] .modal-backdrop-custom > div,
        html[data-admin-theme="dark"] .modal-backdrop-custom > div,
        html[data-admin-theme="oled"] .modal-backdrop-custom > div {
            background: var(--guest-bg-surface, #0e1621) !important;
            border-color: var(--guest-border, rgba(255, 255, 255, 0.15)) !important;
            color: #f8fafc !important;
        }

        /* Filter Pills & Buttons */
        html[data-guest-theme="dark"] .filter-pill-inactive,
        html[data-guest-theme="oled"] .filter-pill-inactive,
        html[data-admin-theme="dark"] .filter-pill-inactive,
        html[data-admin-theme="oled"] .filter-pill-inactive {
            background: rgba(255, 255, 255, 0.08) !important;
            color: var(--guest-text-muted, #94a3b8) !important;
        }

        html[data-guest-theme="dark"] .filter-pill-inactive:hover,
        html[data-guest-theme="oled"] .filter-pill-inactive:hover,
        html[data-admin-theme="dark"] .filter-pill-inactive:hover,
        html[data-admin-theme="oled"] .filter-pill-inactive:hover {
            background: rgba(255, 255, 255, 0.16) !important;
            color: #ffffff !important;
        }

        html[data-guest-theme="dark"] .filter-pill-active,
        html[data-guest-theme="oled"] .filter-pill-active,
        html[data-admin-theme="dark"] .filter-pill-active,
        html[data-admin-theme="oled"] .filter-pill-active {
            background: var(--guest-accent, #00f5d4) !important;
            color: var(--guest-accent-text, #042f2e) !important;
            box-shadow: 0 4px 14px var(--guest-accent-glow) !important;
        }

        html[data-guest-theme="dark"] .btn-upload-primary,
        html[data-guest-theme="oled"] .btn-upload-primary,
        html[data-admin-theme="dark"] .btn-upload-primary,
        html[data-admin-theme="oled"] .btn-upload-primary {
            background: var(--guest-accent, #00f5d4) !important;
            color: var(--guest-accent-text, #042f2e) !important;
            box-shadow: 0 4px 14px var(--guest-accent-glow) !important;
        }

        html[data-guest-theme="dark"] .bg-slate-50,
        html[data-guest-theme="dark"] .bg-slate-50\/80,
        html[data-guest-theme="dark"] .bg-slate-50\/50,
        html[data-guest-theme="oled"] .bg-slate-50,
        html[data-guest-theme="oled"] .bg-slate-50\/80,
        html[data-guest-theme="oled"] .bg-slate-50\/50,
        html[data-admin-theme="dark"] .bg-slate-50,
        html[data-admin-theme="dark"] .bg-slate-50\/80,
        html[data-admin-theme="dark"] .bg-slate-50\/50,
        html[data-admin-theme="oled"] .bg-slate-50,
        html[data-admin-theme="oled"] .bg-slate-50\/80,
        html[data-admin-theme="oled"] .bg-slate-50\/50 {
            background: rgba(8, 14, 22, 0.95) !important;
        }

        html[data-guest-theme="dark"] .border-slate-100,
        html[data-guest-theme="dark"] .border-slate-200,
        html[data-guest-theme="dark"] .border-slate-200\/80,
        html[data-guest-theme="oled"] .border-slate-100,
        html[data-guest-theme="oled"] .border-slate-200,
        html[data-guest-theme="oled"] .border-slate-200\/80,
        html[data-admin-theme="dark"] .border-slate-100,
        html[data-admin-theme="dark"] .border-slate-200,
        html[data-admin-theme="dark"] .border-slate-200\/80,
        html[data-admin-theme="oled"] .border-slate-100,
        html[data-admin-theme="oled"] .border-slate-200,
        html[data-admin-theme="oled"] .border-slate-200\/80 {
            border-color: rgba(255, 255, 255, 0.1) !important;
        }

        /* Reading Text Size Dynamic Scaling (Only for Article/Title links, never action buttons) */
        .table-container-card td a:not(.rounded-full):not([class*="rounded"]):not([class*="btn"]):not([class*="px-"]),
        .card-title-link {
            font-size: calc(0.95rem * var(--guest-font-multiplier, 1)) !important;
            line-height: 1.4 !important;
        }

        /* Action Buttons & Badges inside Tables must stay compact & crisp */
        .table-container-card td a.rounded-full,
        .table-container-card td a[class*="rounded-full"],
        .table-container-card td button.rounded-full,
        .table-container-card td button[class*="rounded-full"],
        .table-container-card td .flex.items-center.justify-end a,
        .table-container-card td .flex.items-center.justify-end button {
            font-size: 12px !important;
            line-height: 1.25rem !important;
            white-space: nowrap !important;
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen" 
         x-data="{ 
             collapsed: document.documentElement.classList.contains('admin-sidebar-is-collapsed'), 
             mobileOpen: false 
         }"
         x-init="$watch('collapsed', value => {
             localStorage.setItem('admin_sidebar_collapsed', value);
             if (value) {
                 document.documentElement.classList.add('admin-sidebar-is-collapsed');
             } else {
                 document.documentElement.classList.remove('admin-sidebar-is-collapsed');
             }
         })">
        
        {{-- ADMIN SIDEBAR NAVIGATION --}}
        @include('layouts.admin-navigation')

        {{-- MAIN CONTENT WRAPPER --}}
        <div class="admin-main-wrapper" :class="collapsed ? 'is-collapsed' : ''">
            <main class="admin-main-canvas">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Floating & Draggable Theme & Display Customizer --}}
    @include('components.guest-theme-widget')
</body>
</html>
