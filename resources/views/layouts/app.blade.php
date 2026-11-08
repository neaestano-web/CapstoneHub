<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CapstoneHub') }}</title>

        <!-- Pre-paint Script & CSS for Sidebar FOUC & Guest Theme Engine -->
        <script>
            // Force reload if restored from browser back-forward cache (BFCache) after logout
            window.addEventListener('pageshow', function (event) {
                if (event.persisted) {
                    window.location.reload();
                }
            });

            (function() {
                if (localStorage.getItem('student_sidebar_collapsed') === 'true') {
                    document.documentElement.classList.add('sidebar-is-collapsed');
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
                    var studentSaved = localStorage.getItem('student_theme_mode');
                    var mode = (s && s.mode) ? s.mode : (studentSaved ? studentSaved : 'light');
                    if (mode === 'dark' || mode === 'oled') mode = 'light';
                    document.documentElement.setAttribute('data-guest-theme', mode);
                    document.documentElement.setAttribute('data-student-theme', mode);

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
                background-color: var(--guest-bg-base, #ffffff) !important;
                background-image: var(--guest-page-gradient, none) !important;
                background-attachment: fixed !important;
                color: var(--guest-text-main, #334155);
                min-height: 100vh;
                margin: 0;
                overflow-x: hidden;
            }

            .student-main-canvas {
                background-color: var(--guest-bg-surface, #ffffff) !important;
                background-image: var(--guest-page-gradient, none) !important;
                background-attachment: fixed !important;
                color: var(--guest-text-main, #334155) !important;
                border-radius: 0;
                min-height: 100vh;
                padding: 24px 20px;
                transition: background-color 0.25s ease, color 0.25s ease;
            }

            @media (min-width: 1024px) {
                .student-sidebar-fixed {
                    width: 240px;
                    transition: width 0.35s cubic-bezier(0.4, 0, 0.2, 1);
                }

                .student-main-wrapper {
                    margin-left: 240px;
                    width: calc(100% - 240px);
                    min-height: 100vh;
                    padding: 0;
                    transition: margin-left 0.35s cubic-bezier(0.4, 0, 0.2, 1), width 0.35s cubic-bezier(0.4, 0, 0.2, 1);
                }

                .student-main-canvas {
                    background-color: var(--guest-bg-surface, #ffffff) !important;
                    background-image: var(--guest-page-gradient, none) !important;
                    background-attachment: fixed !important;
                    border-radius: 0;
                    min-height: 100vh;
                    box-shadow: none;
                    padding: 36px 40px;
                }

                html.sidebar-is-collapsed .student-sidebar-fixed,
                .student-sidebar-fixed.is-collapsed {
                    width: 80px;
                }

                html.sidebar-is-collapsed .student-main-wrapper,
                .student-main-wrapper.is-collapsed {
                    margin-left: 80px;
                    width: calc(100% - 80px);
                }
            }

            /* ═══════════════════════════════════════════════════════════════
               THEME ENGINE (CLEAN LIGHT, SEPIA, CUSTOM)
            ═══════════════════════════════════════════════════════════════ */
            :root {
                /* Defaults: Clean Academic White */
                --guest-bg-base: #ffffff;
                --guest-page-gradient: none;
                --guest-bg-surface: #ffffff;
                --guest-bg-sidebar: #ffffff;
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

            /* ── CLEAN LIGHT PRESET (Default) ── */
            html[data-guest-theme="light"],
            html[data-student-theme="light"] {
                --guest-bg-base: #ffffff;
                --guest-page-gradient: none;
                --guest-bg-surface: #ffffff;
                --guest-bg-sidebar: #ffffff;
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

            /* ── WARM SEPIA PRESET ── */
            html[data-guest-theme="sepia"],
            html[data-student-theme="sepia"] {
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
                --guest-card-border-grad: linear-gradient(135deg, var(--guest-accent, #b45309) 0%, rgba(180, 83, 9, 0.3) 100%);
                --guest-card-shadow: 0 4px 18px rgba(68, 60, 51, 0.07);
                --guest-accent: #b45309;
                --guest-accent-glow: rgba(180, 83, 9, 0.2);
            }

            /* ── CUSTOM CANVAS PRESET ── */
            html[data-guest-theme="custom"],
            html[data-student-theme="custom"] {
                --guest-page-gradient: none;
            }

            /* ── WARM SEPIA PRESET FOR STUDENT ── */
            html[data-student-theme="sepia"] .student-main-canvas {
                background: #fbf7ee !important;
                color: #262019 !important;
            }
            html[data-student-theme="sepia"] .form-card,
            html[data-student-theme="sepia"] .section-card,
            html[data-student-theme="sepia"] .table-container-card,
            html[data-student-theme="sepia"] .sim-auth-card {
                background: #ffffff !important;
                border-color: #ded2c0 !important;
            }

            /* ── CUSTOM CANVAS PRESET (SAME AS GUEST) ── */
            html[data-guest-theme="custom"] .student-main-canvas,
            html[data-student-theme="custom"] .student-main-canvas {
                background: var(--guest-bg-base) !important;
                color: var(--guest-text-main) !important;
            }
            html[data-guest-theme="custom"] .form-card,
            html[data-guest-theme="custom"] .section-card,
            html[data-guest-theme="custom"] .table-container-card,
            html[data-guest-theme="custom"] .sim-auth-card,
            html[data-student-theme="custom"] .form-card,
            html[data-student-theme="custom"] .section-card,
            html[data-student-theme="custom"] .table-container-card,
            html[data-student-theme="custom"] .sim-auth-card {
                background: var(--guest-bg-surface) !important;
                border-color: var(--guest-border) !important;
                color: var(--guest-text-main) !important;
            }
            html[data-guest-theme="custom"] .sim-auth-input,
            html[data-guest-theme="custom"] .form-input,
            html[data-student-theme="custom"] .sim-auth-input,
            html[data-student-theme="custom"] .form-input {
                background: var(--guest-bg-input) !important;
                border-color: var(--guest-border) !important;
                color: var(--guest-text-headings) !important;
            }

            /* ═══════════════════════════════════════════════════════════════
               BRAND ACCENT HIGHLIGHT SYSTEM (CYAN, EMERALD, SKY, VIOLET, CORAL, AMBER)
               Colors for buttons, badges, glowing borders, and search highlights
            ═══════════════════════════════════════════════════════════════ */

            /* 1. BUTTONS: Primary Action Buttons Bound to Brand Accent */
            .sd-btn-primary,
            .btn-upload-primary,
            .sim-auth-btn,
            .btn-submit,
            .btn-edit-action,
            .sim-public-btn,
            .btn-view-project,
            .btn-footer-done {
                background: var(--guest-accent, #00f5d4) !important;
                color: var(--guest-accent-text, #042f2e) !important;
                box-shadow: 0 4px 18px var(--guest-accent-glow, rgba(0, 245, 212, 0.35)) !important;
                border: 1px solid var(--guest-accent, #00f5d4) !important;
                transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1) !important;
                text-shadow: none !important;
            }

            .sd-btn-primary:hover,
            .btn-upload-primary:hover,
            .sim-auth-btn:hover,
            .btn-submit:hover,
            .btn-edit-action:hover,
            .sim-public-btn:hover,
            .btn-view-project:hover,
            .btn-footer-done:hover {
                filter: brightness(1.12) !important;
                transform: translateY(-2px) !important;
                box-shadow: 0 8px 24px var(--guest-accent-glow, rgba(0, 245, 212, 0.55)) !important;
                color: var(--guest-accent-text, #042f2e) !important;
            }

            .sd-btn-subtle:hover,
            .btn-cancel:hover {
                border-color: var(--guest-accent, #00f5d4) !important;
                color: var(--guest-accent, #00f5d4) !important;
            }

            /* 2. BADGES & PILLS: Portals, Tags, and Field Indicators */
            .sd-badge-portal,
            .nav-tag-pill,
            .sd-rank-top,
            .sim-field-tag-optional,
            .sim-counter-pill {
                background: var(--guest-accent-glow, rgba(0, 245, 212, 0.16)) !important;
                color: var(--guest-accent, #00f5d4) !important;
                border: 1px solid var(--guest-accent, #00f5d4) !important;
            }

            .sim-field-badge {
                background: var(--guest-accent, #00f5d4) !important;
                color: var(--guest-accent-text, #042f2e) !important;
                box-shadow: 0 2px 8px var(--guest-accent-glow, rgba(0, 245, 212, 0.35)) !important;
            }

            .sd-accent-text,
            .text-accent-brand,
            .student-portal-accent {
                color: var(--guest-accent, #00f5d4) !important;
            }

            /* 3. GLOWING BORDERS & FOCUS HIGHLIGHTS */
            .form-input:focus,
            .sim-auth-input:focus,
            .sim-public-input-field:focus,
            .sim-public-textarea-field:focus,
            .sim-input-shell:focus-within,
            .sim-textarea-shell:focus-within,
            input[type="text"]:focus,
            input[type="search"]:focus,
            textarea:focus,
            select:focus {
                border-color: var(--guest-accent, #00f5d4) !important;
                box-shadow: 0 0 0 4px var(--guest-accent-glow, rgba(0, 245, 212, 0.22)) !important;
            }

            .form-card .accent-bar {
                background: linear-gradient(90deg, var(--guest-accent, #00f5d4) 0%, #06b6d4 50%, #8b5cf6 100%) !important;
            }

            .sd-theme-toggle:hover {
                border-color: var(--guest-accent, #00f5d4) !important;
                box-shadow: 0 4px 16px var(--guest-accent-glow, rgba(0, 245, 212, 0.35)) !important;
            }

            /* 4. SEARCH HIGHLIGHTS & SELECTION */
            ::selection {
                background: var(--guest-accent, #00f5d4) !important;
                color: var(--guest-accent-text, #042f2e) !important;
            }

            mark,
            .search-highlight,
            .highlight-term {
                background: var(--guest-accent-glow, rgba(0, 245, 212, 0.35)) !important;
                color: var(--guest-text-headings, #ffffff) !important;
                border-bottom: 2px solid var(--guest-accent, #00f5d4) !important;
                border-radius: 3px;
                padding: 1px 4px;
            }

            /* 5. READING TEXT SIZE DYNAMIC SCALING (TITLES & ABSTRACTS) */
            .card-title-link,
            .sd-research-title,
            .table-container-card td a:not(.rounded-full):not([class*="rounded"]):not([class*="btn"]):not([class*="px-"]),
            .submission-title,
            .sim-result-title {
                font-size: calc(1.05rem * var(--guest-font-multiplier, 1)) !important;
                line-height: 1.4 !important;
            }

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

            .abstract-preview,
            .sd-research-abstract,
            .sim-abstract-preview,
            .manuscript-abstract,
            .sim-result-abstract {
                font-size: calc(0.92rem * var(--guest-font-multiplier, 1)) !important;
                line-height: 1.65 !important;
            }
        </style>

        <!-- Fonts & Chart.js -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen" 
             x-data="{ 
                 collapsed: document.documentElement.classList.contains('sidebar-is-collapsed'), 
                 mobileOpen: false 
             }"
             x-init="$watch('collapsed', value => {
                 localStorage.setItem('student_sidebar_collapsed', value);
                 if (value) {
                     document.documentElement.classList.add('sidebar-is-collapsed');
                 } else {
                     document.documentElement.classList.remove('sidebar-is-collapsed');
                 }
             })">
            @if(auth()->check() && auth()->user()->role === 'admin')
                @include('layouts.admin-navigation')
            @elseif(auth()->check())
                @include('layouts.student-navigation')
            @endif

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content inside Giant White Container -->
            <div class="{{ auth()->check() ? 'student-main-wrapper' : 'max-w-7xl mx-auto px-4 py-8' }}"
                 :class="(auth()->check() && collapsed) ? 'is-collapsed' : ''">
                <main class="{{ auth()->check() ? 'student-main-canvas' : '' }}">
                    @yield('content')
                </main>
            </div>
        </div>

        {{-- Floating & Draggable Theme & Display Customizer (Same as Guest) --}}
        @if(auth()->check() && auth()->user()->role === 'student')
            @include('components.guest-theme-widget')
        @endif
    </body>
</html>
