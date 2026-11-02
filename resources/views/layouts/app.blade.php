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
                    if (raw) {
                        var s = JSON.parse(raw);
                        if (s.mode) {
                            document.documentElement.setAttribute('data-guest-theme', s.mode);
                            document.documentElement.setAttribute('data-student-theme', s.mode);
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
                background-color: var(--guest-bg-base, #0c4233) !important;
                background-image: var(--guest-page-gradient, none) !important;
                background-attachment: fixed !important;
                color: var(--guest-text-main, #1e293b);
                min-height: 100vh;
                margin: 0;
                overflow-x: hidden;
            }

            .student-main-canvas {
                background-color: var(--guest-bg-base, #ffffff) !important;
                background-image: var(--guest-page-gradient, none) !important;
                background-attachment: fixed !important;
                color: var(--guest-text-main, #1e293b) !important;
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
                    background-color: var(--guest-bg-base, #ffffff) !important;
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
               THEME ENGINE (SAME AS GUEST) — CYBER DARK, LIGHT, SEPIA, OLED, CUSTOM
            ═══════════════════════════════════════════════════════════════ */
            :root,
            html[data-guest-theme="dark"],
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
                --guest-card-border-grad: linear-gradient(135deg, var(--guest-accent, #00f5d4) 0%, #06b6d4 35%, #10b981 70%, #34d399 100%);
                --guest-card-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
                --guest-accent: #00f5d4;
                --guest-accent-glow: rgba(0, 245, 212, 0.35);
            }

            /* ── CLEAN LIGHT PRESET ── */
            html[data-guest-theme="light"],
            html[data-student-theme="light"] {
                --guest-bg-base: #ffffff;
                --guest-page-gradient: 
                    radial-gradient(circle at 50% 18%, rgba(16, 185, 129, 0.06) 0%, rgba(56, 189, 248, 0.05) 35%, transparent 60%),
                    radial-gradient(ellipse at 50% 45%, #ffffff 0%, #e2e8f0 100%);
                --guest-bg-surface: #ffffff;
                --guest-bg-sidebar: #ffffff;
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

            /* ── MIDNIGHT OLED PRESET ── */
            html[data-guest-theme="oled"],
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

            /* ── CUSTOM CANVAS PRESET ── */
            html[data-guest-theme="custom"],
            html[data-student-theme="custom"] {
                --guest-page-gradient: none;
            }

            /* Dark Typography Across Student Views */
            html[data-guest-theme="dark"] h1,
            html[data-guest-theme="dark"] h2,
            html[data-guest-theme="dark"] h3,
            html[data-student-theme="dark"] h1,
            html[data-student-theme="dark"] h2,
            html[data-student-theme="dark"] h3,
            html[data-guest-theme="dark"] .text-\[\#2d2b56\],
            html[data-student-theme="dark"] .text-\[\#2d2b56\] {
                color: #ffffff !important;
            }

            html[data-guest-theme="dark"] .text-\[\#0c4233\],
            html[data-student-theme="dark"] .text-\[\#0c4233\] {
                color: var(--guest-accent, #00f5d4) !important;
            }

            html[data-guest-theme="dark"] .text-slate-500,
            html[data-guest-theme="dark"] .text-gray-500,
            html[data-guest-theme="dark"] .text-slate-600,
            html[data-student-theme="dark"] .text-slate-500,
            html[data-student-theme="dark"] .text-gray-500,
            html[data-student-theme="dark"] .text-slate-600 {
                color: #94a3b8 !important;
            }

            html[data-guest-theme="dark"] .border-b,
            html[data-guest-theme="dark"] .border-slate-100,
            html[data-guest-theme="dark"] .border-slate-200,
            html[data-guest-theme="dark"] .border-slate-200\/80,
            html[data-student-theme="dark"] .border-b,
            html[data-student-theme="dark"] .border-slate-100,
            html[data-student-theme="dark"] .border-slate-200,
            html[data-student-theme="dark"] .border-slate-200\/80 {
                border-color: rgba(255, 255, 255, 0.1) !important;
            }

            /* Cards & Panels with Glowing Cyber Borders */
            html[data-guest-theme="dark"] .sim-auth-card,
            html[data-guest-theme="dark"] .table-container-card,
            html[data-guest-theme="dark"] .form-card,
            html[data-guest-theme="dark"] .section-card,
            html[data-student-theme="dark"] .sim-auth-card,
            html[data-student-theme="dark"] .table-container-card,
            html[data-student-theme="dark"] .form-card,
            html[data-student-theme="dark"] .section-card {
                background: linear-gradient(var(--guest-bg-surface), var(--guest-bg-surface)) padding-box,
                            var(--guest-card-border-grad) border-box !important;
                border: 1.5px solid transparent !important;
                color: #f8fafc !important;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.45), 0 0 18px rgba(0, 245, 212, 0.12) !important;
                backdrop-filter: blur(16px) !important;
                -webkit-backdrop-filter: blur(16px) !important;
            }

            /* Input Fields Across All Views */
            html[data-student-theme="dark"] .sim-auth-input,
            html[data-student-theme="dark"] .form-input,
            html[data-student-theme="dark"] input[type="text"]:not(.native-color-picker),
            html[data-student-theme="dark"] input[type="search"],
            html[data-student-theme="dark"] textarea,
            html[data-student-theme="dark"] select {
                background: rgba(8, 14, 22, 0.85) !important;
                border-color: rgba(255, 255, 255, 0.18) !important;
                color: #ffffff !important;
            }

            html[data-student-theme="dark"] .sim-auth-input:focus,
            html[data-student-theme="dark"] .form-input:focus,
            html[data-student-theme="dark"] textarea:focus,
            html[data-student-theme="dark"] select:focus {
                background: rgba(12, 20, 30, 0.95) !important;
                border-color: var(--guest-accent, #00f5d4) !important;
                box-shadow: 0 0 0 4px var(--guest-accent-glow, rgba(0, 245, 212, 0.22)) !important;
            }

            /* Labels & Checkboxes */
            html[data-student-theme="dark"] .form-label {
                color: #e2e8f0 !important;
            }

            html[data-student-theme="dark"] .check-card {
                background: rgba(8, 14, 22, 0.85) !important;
                border-color: rgba(255, 255, 255, 0.12) !important;
                color: #e2e8f0 !important;
            }

            html[data-student-theme="dark"] .check-card:hover {
                border-color: var(--guest-accent, #00f5d4) !important;
                background: rgba(0, 245, 212, 0.08) !important;
            }

            html[data-student-theme="dark"] .check-card.checked,
            html[data-student-theme="dark"] .check-card.is-checked {
                border-color: var(--guest-accent, #00f5d4) !important;
                background: var(--guest-accent-glow, rgba(0, 245, 212, 0.14)) !important;
                color: #ffffff !important;
            }

            html[data-student-theme="dark"] .classification-panel {
                background: rgba(88, 64, 187, 0.16) !important;
                border-color: rgba(139, 92, 246, 0.35) !important;
            }

            html[data-student-theme="dark"] .classification-panel-title {
                color: #c084fc !important;
            }

            /* Tables (Submissions, Lists) */
            html[data-student-theme="dark"] table th {
                background: rgba(8, 14, 22, 0.95) !important;
                color: #94a3b8 !important;
                border-color: rgba(255, 255, 255, 0.08) !important;
            }

            html[data-student-theme="dark"] table td {
                border-color: rgba(255, 255, 255, 0.08) !important;
                color: #f8fafc !important;
            }

            html[data-student-theme="dark"] table tr:hover td {
                background: rgba(255, 255, 255, 0.03) !important;
            }

            /* Buttons & Actions */
            html[data-student-theme="dark"] .btn-cancel {
                background: rgba(255, 255, 255, 0.06) !important;
                color: #cbd5e1 !important;
                border-color: rgba(255, 255, 255, 0.14) !important;
            }

            html[data-student-theme="dark"] .btn-cancel:hover {
                background: rgba(255, 255, 255, 0.12) !important;
                color: #ffffff !important;
            }

            html[data-student-theme="dark"] .back-btn-pill {
                background: rgba(139, 92, 246, 0.15) !important;
                color: #c084fc !important;
                border-color: rgba(139, 92, 246, 0.3) !important;
            }

            /* Browse Repository Adapters in Student View */
            html[data-student-theme="dark"] .browse-page-container {
                color: var(--guest-text-main) !important;
            }

            html[data-student-theme="dark"] .filter-sidebar {
                background: linear-gradient(var(--guest-bg-sidebar), var(--guest-bg-sidebar)) padding-box,
                            var(--guest-card-border-grad) border-box !important;
                box-shadow: var(--guest-card-shadow) !important;
            }

            html[data-student-theme="dark"] .filter-header-title {
                color: var(--guest-text-headings) !important;
            }

            html[data-student-theme="dark"] .filter-section-title {
                color: var(--guest-text-muted) !important;
            }

            html[data-student-theme="dark"] .filter-item-label {
                color: var(--guest-text-main) !important;
            }

            html[data-student-theme="dark"] .filter-search-input {
                background: var(--guest-bg-input) !important;
                color: var(--guest-text-headings) !important;
                border-color: var(--guest-border) !important;
            }

            html[data-student-theme="dark"] .capstone-card {
                background: linear-gradient(var(--guest-bg-surface), var(--guest-bg-surface)) padding-box,
                            var(--guest-card-border-grad) border-box !important;
                box-shadow: var(--guest-card-shadow) !important;
            }

            html[data-student-theme="dark"] .card-title-link {
                color: var(--guest-text-headings) !important;
            }

            /* Additional Table, Card & Alert Enhancements */
            html[data-student-theme="dark"] .bg-slate-50,
            html[data-student-theme="dark"] .bg-slate-50\/80 {
                background: rgba(8, 14, 22, 0.95) !important;
            }
            html[data-student-theme="dark"] tr:hover {
                background: rgba(255, 255, 255, 0.04) !important;
            }
            html[data-student-theme="dark"] .sim-wrapper .bg-white {
                background: rgba(14, 23, 34, 0.94) !important;
                border-color: rgba(255, 255, 255, 0.12) !important;
                color: #f8fafc !important;
            }
            html[data-student-theme="dark"] .bg-purple-50 {
                background: rgba(139, 92, 246, 0.16) !important;
                border-color: rgba(139, 92, 246, 0.3) !important;
                color: #c084fc !important;
            }
            html[data-student-theme="dark"] .bg-amber-50\/80,
            html[data-student-theme="dark"] .bg-amber-50\/90 {
                background: rgba(217, 119, 6, 0.15) !important;
                border-color: rgba(217, 119, 6, 0.35) !important;
                color: #fde68a !important;
            }
            html[data-student-theme="dark"] .bg-amber-100 {
                background: rgba(245, 158, 11, 0.18) !important;
                color: #fbbf24 !important;
            }
            html[data-student-theme="dark"] .bg-emerald-100 {
                background: rgba(16, 185, 129, 0.18) !important;
                color: #34d399 !important;
            }
            html[data-student-theme="dark"] .text-amber-900,
            html[data-student-theme="dark"] .text-amber-950 {
                color: #fde68a !important;
            }
            html[data-student-theme="dark"] .bg-slate-100 {
                background: rgba(255, 255, 255, 0.08) !important;
                color: #ffffff !important;
            }
            html[data-student-theme="dark"] .text-slate-700,
            html[data-student-theme="dark"] .text-slate-800 {
                color: #f8fafc !important;
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

            /* ── MIDNIGHT OLED PRESET FOR STUDENT ── */
            html[data-student-theme="oled"] .student-main-canvas {
                background: #000000 !important;
                color: #ffffff !important;
            }
            html[data-student-theme="oled"] .form-card,
            html[data-student-theme="oled"] .section-card,
            html[data-student-theme="oled"] .table-container-card,
            html[data-student-theme="oled"] .sim-auth-card {
                background: #0a0a0a !important;
                border-color: #27272a !important;
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
