<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') — JHCSC Capstone Repository</title>

    <!-- Pre-paint Script for Sidebar FOUC & Display Theme Engine -->
    <script>
        // Force reload if restored from browser back-forward cache (BFCache) after logout
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                window.location.reload();
            }
        });

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
                var s = raw ? JSON.parse(raw) : null;
                var adminSaved = localStorage.getItem('student_theme_mode');
                var mode = (s && s.mode) ? s.mode : (adminSaved ? adminSaved : 'light');
                if (mode === 'dark' || mode === 'oled') mode = 'light';
                document.documentElement.setAttribute('data-guest-theme', mode);
                document.documentElement.setAttribute('data-student-theme', mode);
                document.documentElement.setAttribute('data-admin-theme', mode);

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
            background-color: var(--guest-bg-base, #ffffff) !important;
            background-image: var(--guest-page-gradient, none) !important;
            background-attachment: fixed !important;
            color: var(--guest-text-main, #334155);
            min-height: 100vh;
            overflow-x: hidden;
            transition: background-color 0.25s ease, color 0.25s ease;
        }

        /* ═══════════════════════════════════════════════════════════════
           THEME ENGINE (CLEAN LIGHT, SEPIA, CUSTOM)
        ═══════════════════════════════════════════════════════════════ */
        :root {
            /* Defaults: Clean Academic White */
            --guest-bg-base: #ffffff;
            --guest-page-gradient: none;
            --guest-bg-surface: #ffffff;
            --guest-bg-sidebar: #0c4233;
            --guest-bg-input: #f8fafc;
            --guest-text-main: #334155;
            --guest-text-muted: #64748b;
            --guest-text-headings: #0f172a;
            --guest-border: #e2e8f0;
            --guest-card-border-grad: linear-gradient(135deg, var(--guest-accent, #0c4233) 0%, rgba(6, 182, 212, 0.4) 40%, rgba(139, 92, 246, 0.35) 100%);
            --guest-card-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
            --guest-accent: #0c4233;
            --guest-accent-glow: rgba(12, 66, 51, 0.18);
        }

        /* CLEAN LIGHT PRESET (Default) */
        html[data-guest-theme="light"],
        html[data-admin-theme="light"],
        html[data-student-theme="light"] {
            --guest-bg-base: #ffffff;
            --guest-page-gradient: none;
            --guest-bg-surface: #ffffff;
            --guest-bg-sidebar: #0c4233;
            --guest-bg-input: #f8fafc;
            --guest-text-main: #334155;
            --guest-text-muted: #64748b;
            --guest-text-headings: #0f172a;
            --guest-border: #e2e8f0;
            --guest-card-border-grad: linear-gradient(135deg, var(--guest-accent, #0c4233) 0%, rgba(6, 182, 212, 0.4) 40%, rgba(139, 92, 246, 0.35) 100%);
            --guest-card-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
            --guest-accent: #0c4233;
            --guest-accent-glow: rgba(12, 66, 51, 0.18);
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

        html[data-guest-theme="sepia"] .admin-sidebar-fixed,
        html[data-admin-theme="sepia"] .admin-sidebar-fixed {
            background: #1d1813 !important;
            border-right: 1px solid #3d3429 !important;
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
