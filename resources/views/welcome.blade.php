<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>capstoneHub — Academic Capstone Repository</title>
    
    <meta name="description" content="capstoneHub — Digital Capstone Repository for School of Computing Studies, J.H. Cerilles State College.">
    <meta name="keywords" content="capstoneHub, JHCSC, Capstone Repository, Cyberian Club, School of Computing Studies, Research">
    
    <!-- Google Fonts: Plus Jakarta Sans for UI + JetBrains Mono for IT & Tech elements -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --bg-base: #0c131c;
            --bg-surface: #141f2e;
            --border-subtle: rgba(255, 255, 255, 0.12);
            --border-hover: rgba(255, 255, 255, 0.25);
            --emerald-accent: #10b981;
            --emerald-soft: rgba(16, 185, 129, 0.16);
            --emerald-border: rgba(16, 185, 129, 0.45);
            --cyan-accent: #38bdf8;
            --text-primary: #f8fafc;
            --text-secondary: #cbd5e1;
            --text-muted: #94a3b8;
            --font-mono: 'JetBrains Mono', ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }

        html, body {
            min-height: 100vh;
            width: 100%;
            background-color: #0b1119;
            /* Luminous layered radial gradients to bring ambient light & tech depth */
            background-image: 
                radial-gradient(circle at 50% 28%, rgba(16, 185, 129, 0.22) 0%, rgba(56, 189, 248, 0.12) 35%, transparent 68%),
                radial-gradient(circle at 16% 48%, rgba(16, 185, 129, 0.22) 0%, transparent 45%),
                radial-gradient(circle at 84% 48%, rgba(0, 245, 212, 0.18) 0%, transparent 45%),
                radial-gradient(circle at 50% 92%, rgba(56, 189, 248, 0.12) 0%, transparent 52%),
                radial-gradient(ellipse at 50% 45%, #182638 0%, #0b1119 100%);
            background-attachment: fixed;
            color: var(--text-primary);
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* ===== IT / CYBER GRID & NETWORK BACKGROUND ===== */
        .tech-bg-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
            opacity: 0.9;
        }

        .cyber-grid-overlay {
            position: fixed;
            inset: 0;
            background-image: 
                linear-gradient(to right, rgba(16, 185, 129, 0.08) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(16, 185, 129, 0.08) 1px, transparent 1px);
            background-size: 40px 40px;
            mask-image: radial-gradient(circle at 50% 45%, rgba(0, 0, 0, 1) 25%, rgba(0, 0, 0, 0.45) 60%, transparent 85%);
            -webkit-mask-image: radial-gradient(circle at 50% 45%, rgba(0, 0, 0, 1) 25%, rgba(0, 0, 0, 0.45) 60%, transparent 85%);
            pointer-events: none;
            z-index: 0;
        }

        .cyber-grid-overlay::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(52, 211, 153, 0.4) 1.5px, transparent 1.5px);
            background-size: 40px 40px;
            background-position: -1px -1px;
            pointer-events: none;
        }

        /* ===== MAIN PAGE CONTAINER ===== */
        .page-wrapper {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 1.6rem 3rem;
            max-width: 1440px;
            margin: 0 auto;
        }

        /* ===== TOP NAVIGATION BAR ===== */
        header.top-nav {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 2.2rem;
            padding: 0.5rem 0;
            width: 100%;
        }

        .nav-link {
            text-decoration: none;
            color: var(--text-secondary);
            font-size: 0.92rem;
            font-weight: 500;
            transition: color 0.2s ease;
            white-space: nowrap;
        }

        .nav-link:hover {
            color: #ffffff;
        }

        .nav-link-browse {
            color: var(--text-secondary);
            text-shadow: 0 0 10px rgba(0, 245, 212, 0.4);
            font-weight: 600;
        }

        .btn-get-started {
            text-decoration: none;
            color: #6ee7b7;
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.55);
            padding: 0.52rem 1.45rem;
            border-radius: 9999px;
            font-size: 0.9rem;
            font-weight: 600;
            box-shadow: 0 0 16px rgba(16, 185, 129, 0.24);
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .btn-get-started:hover {
            background: rgba(16, 185, 129, 0.22);
            border-color: rgba(16, 185, 129, 0.85);
            color: #ffffff;
            box-shadow: 0 0 24px rgba(16, 185, 129, 0.45);
            transform: translateY(-1px);
        }

        /* ===== HERO STAGE (3 COLUMNS: LEFT SEAL, CENTER CONTENT, RIGHT SEAL) ===== */
        main.hero-stage {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 2.5rem;
            width: 100%;
            margin: auto 0;
            padding: 2rem 0;
        }

        /* Seals (JHCSC & SCS) */
        .seal-column {
            flex: 0 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ===== SCI-FI METALLIC REACTOR BEZEL FRAMES ===== */
        .seal-reactor-container {
            position: relative;
            width: 275px;
            height: 275px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.35s ease;
        }

        .seal-reactor-container:hover {
            transform: scale(1.035);
        }

        /* Concentric Sci-Fi HUD Orbit */
        .reactor-hud-orbit {
            position: absolute;
            inset: -16px;
            width: calc(100% + 32px);
            height: calc(100% + 32px);
            pointer-events: none;
            z-index: 1;
            animation: hudRotate 38s linear infinite;
        }

        .seal-reactor-right .reactor-hud-orbit {
            animation: hudRotateRev 42s linear infinite;
        }

        @keyframes hudRotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes hudRotateRev {
            from { transform: rotate(0deg); }
            to { transform: rotate(-360deg); }
        }

        /* The Heavy Metallic Bezel */
        .metallic-bezel {
            position: relative;
            width: 246px;
            height: 246px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
        }

        /* JHCSC Emerald / Gold Cyber Bezel */
        .jhcsc-metallic-bezel {
            background: 
                radial-gradient(circle at 35% 30%, #1e4d38 0%, #113324 40%, #091c13 72%, #1b4532 94%, #0b1c14 100%);
            box-shadow: 
                0 0 0 1.5px rgba(52, 211, 153, 0.45),
                0 0 0 3px #08140e,
                0 0 0 5.5px #166534,
                0 0 0 7.5px #07130c,
                0 0 32px rgba(16, 185, 129, 0.65),
                0 0 65px rgba(16, 185, 129, 0.35),
                inset 0 0 14px rgba(0, 0, 0, 0.95),
                inset 0 2px 4px rgba(255, 255, 255, 0.55),
                inset 0 -2px 5px rgba(0, 0, 0, 0.9),
                0 18px 42px rgba(0, 0, 0, 0.65);
        }

        /* SCS Chrome / Titanium Cyber Bezel */
        .scs-metallic-bezel {
            background: 
                radial-gradient(circle at 35% 30%, #52616f 0%, #1e2630 40%, #0e151d 72%, #334155 94%, #151e28 100%);
            box-shadow: 
                0 0 0 1.5px rgba(255, 255, 255, 0.4),
                0 0 0 3px #0a1017,
                0 0 0 5.5px #475569,
                0 0 0 7.5px #0b1118,
                0 0 30px rgba(0, 245, 212, 0.65),
                0 0 65px rgba(0, 245, 212, 0.35),
                inset 0 0 14px rgba(0, 0, 0, 0.95),
                inset 0 2px 4px rgba(255, 255, 255, 0.55),
                inset 0 -2px 5px rgba(0, 0, 0, 0.9),
                0 18px 42px rgba(0, 0, 0, 0.65);
        }

        /* Sci-Fi Metallic Notch Clips */
        .bezel-notch {
            position: absolute;
            background: linear-gradient(135deg, #94a3b8, #334155);
            border-radius: 2px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.8), inset 0 1px 1px rgba(255, 255, 255, 0.7);
            z-index: 4;
        }

        .jhcsc-metallic-bezel .bezel-notch {
            background: linear-gradient(135deg, #a7f3d0, #065f46);
            box-shadow: 0 0 4px rgba(16, 185, 129, 0.6), inset 0 1px 1px #ffffff;
        }

        .scs-metallic-bezel .bezel-notch {
            background: linear-gradient(135deg, #e2e8f0, #475569);
            box-shadow: 0 0 4px rgba(0, 245, 212, 0.6), inset 0 1px 1px #ffffff;
        }

        .notch-t {
            top: -4px;
            left: 50%;
            transform: translateX(-50%);
            width: 14px;
            height: 7px;
        }

        .notch-b {
            bottom: -4px;
            left: 50%;
            transform: translateX(-50%);
            width: 14px;
            height: 7px;
        }

        .notch-l {
            left: -4px;
            top: 50%;
            transform: translateY(-50%);
            width: 7px;
            height: 14px;
        }

        .notch-r {
            right: -4px;
            top: 50%;
            transform: translateY(-50%);
            width: 7px;
            height: 14px;
        }

        /* Inner Seal Circle Frame */
        .seal-inner-frame {
            position: relative;
            width: 212px;
            height: 212px;
            border-radius: 50%;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #000000;
            z-index: 3;
        }

        .jhcsc-metallic-bezel .seal-inner-frame {
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.85), 0 0 16px rgba(16, 185, 129, 0.6);
        }

        .scs-metallic-bezel .seal-inner-frame {
            box-shadow: 0 0 0 2px rgba(0, 245, 212, 0.85), 0 0 16px rgba(0, 245, 212, 0.6);
        }

        .seal-inner-frame img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 50%;
            display: block;
        }

        /* Center Column */
        .center-column {
            flex: 1;
            max-width: 660px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            position: relative;
        }

        /* Ambient glow aura behind center search area */
        .center-column::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -45%);
            width: 540px;
            height: 300px;
            background: radial-gradient(ellipse, rgba(16, 185, 129, 0.16) 0%, rgba(56, 189, 248, 0.08) 45%, transparent 75%);
            pointer-events: none;
            z-index: -1;
            filter: blur(32px);
        }

        /* IT System Status Badge */
        .tech-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 5px 16px;
            border-radius: 9999px;
            background: rgba(16, 185, 129, 0.08);
            border: 1px solid rgba(16, 185, 129, 0.32);
            font-family: var(--font-mono);
            font-size: 0.72rem;
            letter-spacing: 0.06em;
            color: #34d399;
            margin-bottom: 1.1rem;
            box-shadow: 0 0 16px rgba(16, 185, 129, 0.15);
            backdrop-filter: blur(8px);
            user-select: none;
            transition: all 0.25s ease;
        }

        .tech-status-badge:hover {
            border-color: rgba(16, 185, 129, 0.6);
            background: rgba(16, 185, 129, 0.14);
            box-shadow: 0 0 24px rgba(16, 185, 129, 0.28);
        }

        .status-pulse-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 10px #10b981, 0 0 4px #34d399;
            animation: techPulse 2.2s infinite ease-in-out;
        }

        @keyframes techPulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.35; transform: scale(0.8); }
        }

        .tech-badge-code {
            font-weight: 600;
        }

        .tech-badge-sep {
            color: rgba(16, 185, 129, 0.45);
        }

        .tech-badge-inst {
            color: #93c5fd;
            font-weight: 500;
        }

        /* Logo Branding */
        .brand-header {
            margin-bottom: 1.8rem;
            position: relative;
        }

        .brand-title-wrap {
            display: inline-flex;
            align-items: center;
            position: relative;
            line-height: 1;
        }

        .brand-title {
            font-size: 3.6rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: #ffffff;
            display: flex;
            align-items: center;
        }

        .brand-hub {
            color: #f1f5f9;
        }

        /* Graduation Cap Icon - Radiant Neon Cyan */
        .grad-cap-icon {
            display: inline-flex;
            margin-left: 8px;
            margin-top: -14px;
            color: #00f5d4;
            filter: drop-shadow(0 0 6px #00f5d4) drop-shadow(0 0 16px rgba(0, 245, 212, 0.7));
            transition: transform 0.25s ease, filter 0.25s ease;
        }

        .brand-title-wrap:hover .grad-cap-icon {
            transform: translateY(-3px) scale(1.05);
            filter: drop-shadow(0 0 10px #00f5d4) drop-shadow(0 0 24px rgba(0, 245, 212, 0.9));
        }

        .brand-subtitle {
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.32em;
            color: var(--text-muted);
            text-transform: uppercase;
            margin-top: 0.65rem;
        }

        /* Search Input Form Container */
        .search-container {
            width: 100%;
            max-width: 620px;
            margin: 0 auto;
        }

        /* Dual-Gradient Emerald to Cyan Glowing Search Bar */
        .search-pill-bar {
            background: linear-gradient(rgba(14, 22, 33, 0.9), rgba(14, 22, 33, 0.9)) padding-box,
                        linear-gradient(90deg, #10b981 0%, #34d399 35%, #38bdf8 70%, #00f5d4 100%) border-box;
            border: 1.5px solid transparent;
            border-radius: 9999px;
            padding: 8px 14px 8px 22px;
            display: flex;
            align-items: center;
            gap: 12px;
            backdrop-filter: blur(18px);
            box-shadow: 
                0 12px 34px rgba(0, 0, 0, 0.45),
                -18px 0 32px -6px rgba(16, 185, 129, 0.32),
                18px 0 32px -6px rgba(0, 245, 212, 0.32);
            transition: all 0.3s ease;
        }

        .search-pill-bar:hover {
            box-shadow: 
                0 14px 40px rgba(0, 0, 0, 0.5),
                -22px 0 38px -4px rgba(16, 185, 129, 0.45),
                22px 0 38px -4px rgba(0, 245, 212, 0.45);
        }

        .search-pill-bar:focus-within {
            box-shadow: 
                0 0 0 3px rgba(0, 245, 212, 0.25),
                0 16px 45px rgba(0, 0, 0, 0.55),
                -24px 0 42px -2px rgba(16, 185, 129, 0.5),
                24px 0 42px -2px rgba(0, 245, 212, 0.5);
        }

        .search-terminal-prompt {
            font-family: var(--font-mono);
            color: #00f5d4;
            font-weight: 700;
            font-size: 1.05rem;
            user-select: none;
            display: flex;
            align-items: center;
            text-shadow: 0 0 10px rgba(0, 245, 212, 0.8), 0 0 18px rgba(0, 245, 212, 0.45);
        }

        .search-input {
            flex: 1;
            background: transparent;
            border: none;
            outline: none;
            color: #ffffff;
            font-size: 0.96rem;
            font-family: inherit;
            padding: 6px 0;
        }

        .search-input::placeholder {
            color: var(--text-muted);
            font-size: 0.92rem;
        }

        .shortcut-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.16);
            color: #cbd5e1;
            font-family: var(--font-mono);
            font-size: 0.74rem;
            font-weight: 600;
            padding: 4px 9px;
            border-radius: 6px;
            letter-spacing: 0.5px;
            user-select: none;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.25);
            transition: all 0.2s ease;
        }

        .shortcut-badge:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.16);
            border-color: rgba(255, 255, 255, 0.3);
        }

        .search-icon-btn {
            background: none;
            border: none;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 6px;
            cursor: pointer;
            border-radius: 50%;
            transition: color 0.2s ease, transform 0.2s ease;
        }

        .search-pill-bar:focus-within .search-icon-btn,
        .search-icon-btn:hover {
            color: #00f5d4;
            transform: scale(1.08);
        }

        /* ===== SUB-HERO: DISCIPLINE PILLS & ARCHIVED COUNT ===== */
        .bottom-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-top: 1.2rem;
            text-align: center;
        }

        .discipline-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            flex-wrap: wrap;
            font-size: 0.86rem;
            color: var(--text-secondary);
        }

        .discipline-tag {
            text-decoration: none;
            color: #e2e8f0;
            background: rgba(14, 23, 34, 0.75);
            padding: 5px 15px;
            border-radius: 9999px;
            font-size: 0.83rem;
            font-weight: 600;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(0, 245, 212, 0.45);
            box-shadow: 0 0 12px rgba(0, 245, 212, 0.15);
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }

        .discipline-tag:hover {
            border-color: #00f5d4;
            box-shadow: 0 0 18px rgba(0, 245, 212, 0.5);
            color: #ffffff;
            transform: translateY(-2px);
        }

        .discipline-tag:nth-child(2n) {
            border-color: rgba(56, 189, 248, 0.5);
            box-shadow: 0 0 12px rgba(56, 189, 248, 0.18);
        }
        .discipline-tag:nth-child(2n):hover {
            border-color: #38bdf8;
            box-shadow: 0 0 20px rgba(56, 189, 248, 0.5);
            color: #ffffff;
        }

        .discipline-tag:nth-child(3n) {
            border-color: rgba(139, 92, 246, 0.5);
            box-shadow: 0 0 12px rgba(139, 92, 246, 0.18);
        }
        .discipline-tag:nth-child(3n):hover {
            border-color: #a78bfa;
            box-shadow: 0 0 20px rgba(139, 92, 246, 0.5);
            color: #ffffff;
        }

        .discipline-tag:nth-child(4n) {
            border-color: rgba(16, 185, 129, 0.5);
            box-shadow: 0 0 12px rgba(16, 185, 129, 0.18);
        }
        .discipline-tag:nth-child(4n):hover {
            border-color: #34d399;
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.5);
            color: #ffffff;
        }

        .discipline-count-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.72rem;
            font-family: var(--font-mono);
            font-weight: 700;
            padding: 1px 7px;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.1);
            color: #cbd5e1;
        }

        .stats-text {
            font-size: 0.88rem;
            color: var(--text-muted);
            letter-spacing: 0.2px;
        }

        .stats-count-accent {
            color: #00f5d4;
            font-weight: 800;
            font-family: var(--font-mono);
            text-shadow: 0 0 12px rgba(0, 245, 212, 0.45);
        }

        /* ===== FOOTER LINKS ===== */
        footer.site-footer {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 2rem;
            padding: 1.2rem 0 0.5rem;
        }

        .footer-link {
            text-decoration: none;
            color: var(--text-muted);
            font-size: 0.84rem;
            transition: color 0.2s ease;
            cursor: pointer;
            background: none;
            border: none;
            font-family: inherit;
        }

        .footer-link:hover {
            color: var(--text-secondary);
        }

        /* ===== MODAL DIALOGS (About, Terms, Privacy, Help) ===== */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(7, 10, 14, 0.75);
            backdrop-filter: blur(8px);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .modal-overlay.open {
            display: flex;
            opacity: 1;
        }

        .modal-card {
            background: #11161f;
            border: 1px solid var(--border-hover);
            border-radius: 14px;
            width: 100%;
            max-width: 520px;
            padding: 2.2rem;
            position: relative;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.65);
            color: #e2e8f0;
            transform: scale(0.97);
            transition: transform 0.2s ease;
        }

        .modal-overlay.open .modal-card {
            transform: scale(1);
        }

        .modal-close-btn {
            position: absolute;
            top: 1.2rem;
            right: 1.2rem;
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 1.3rem;
            cursor: pointer;
            transition: color 0.2s;
        }

        .modal-close-btn:hover {
            color: #ffffff;
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--emerald-accent);
            margin-bottom: 0.85rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-body {
            font-size: 0.92rem;
            line-height: 1.7;
            color: #cbd5e1;
        }

        /* ===== RESPONSIVE DESIGN ===== */
        @media (max-width: 1180px) {
            .seal-reactor-container {
                width: 220px;
                height: 220px;
            }
            .metallic-bezel {
                width: 195px;
                height: 195px;
            }
            .seal-inner-frame {
                width: 170px;
                height: 170px;
            }
            .brand-title {
                font-size: 3rem;
            }
            .page-wrapper {
                padding: 1.2rem 1.8rem;
            }
        }

        @media (max-width: 900px) {
            main.hero-stage {
                gap: 1.5rem;
                padding: 1.5rem 0;
            }
            .seal-reactor-container {
                width: 160px;
                height: 160px;
            }
            .metallic-bezel {
                width: 142px;
                height: 142px;
            }
            .seal-inner-frame {
                width: 122px;
                height: 122px;
            }
            .brand-title {
                font-size: 2.8rem;
            }
            .search-container {
                max-width: 100%;
            }
        }

        @media (max-width: 768px) {
            .page-wrapper {
                padding: 1rem 1.2rem 0.6rem;
                min-height: 100vh;
                min-height: 100dvh;
            }

            header.top-nav {
                gap: 1.1rem;
                justify-content: center;
                padding: 0.35rem 0;
            }

            .nav-link {
                font-size: 0.88rem;
            }

            .btn-get-started {
                font-size: 0.86rem;
                padding: 0.45rem 1.15rem;
            }

            /* Minimal Mobile Layout for Hero Stage: Paired seals side-by-side above center content */
            main.hero-stage {
                display: grid;
                grid-template-columns: 1fr 1fr;
                grid-template-areas:
                    "seal-left seal-right"
                    "center center";
                justify-items: center;
                align-items: center;
                gap: 0.85rem 1rem;
                padding: 0.5rem 0;
                margin: auto 0;
            }

            .seal-column-left {
                grid-area: seal-left;
                justify-self: end;
                margin-right: 6px;
            }

            .seal-column-right {
                grid-area: seal-right;
                justify-self: start;
                margin-left: 6px;
            }

            .center-column {
                grid-area: center;
                width: 100%;
                max-width: 100%;
            }

            .center-column::before {
                width: 280px;
                height: 200px;
                filter: blur(24px);
            }

            /* Sleek paired emblems instead of heavy 3D reactors */
            .seal-reactor-container {
                width: 76px;
                height: 76px;
            }

            .reactor-hud-orbit {
                display: none !important;
            }

            .metallic-bezel {
                width: 72px;
                height: 72px;
            }

            .bezel-notch {
                display: none !important;
            }

            .seal-inner-frame {
                width: 62px;
                height: 62px;
            }

            .jhcsc-metallic-bezel {
                box-shadow: 
                    0 0 0 1.5px rgba(52, 211, 153, 0.75),
                    0 0 18px rgba(16, 185, 129, 0.4),
                    0 6px 14px rgba(0, 0, 0, 0.5);
            }

            .scs-metallic-bezel {
                box-shadow: 
                    0 0 0 1.5px rgba(0, 245, 212, 0.75),
                    0 0 18px rgba(0, 245, 212, 0.4),
                    0 6px 14px rgba(0, 0, 0, 0.5);
            }

            .tech-status-badge {
                font-size: 0.68rem;
                padding: 4px 13px;
                gap: 7px;
                margin-bottom: 0.85rem;
            }

            .brand-header {
                margin-bottom: 1.35rem;
            }

            .brand-title {
                font-size: 2.3rem;
            }

            .grad-cap-icon svg {
                width: 28px;
                height: 28px;
            }

            .brand-subtitle {
                font-size: 0.72rem;
                letter-spacing: 0.24em;
                margin-top: 0.45rem;
            }

            /* Hide keyboard shortcut badge on touchscreens */
            .shortcut-badge {
                display: none !important;
            }

            .search-pill-bar {
                padding: 7px 12px 7px 18px;
                gap: 10px;
            }

            .search-input {
                font-size: 0.9rem;
            }

            .search-input::placeholder {
                font-size: 0.86rem;
            }

            .bottom-section {
                margin-top: 1rem;
                gap: 0.75rem;
            }

            .discipline-row {
                font-size: 0.8rem;
                gap: 6px;
                padding: 0 4px;
            }

            .discipline-row > span {
                width: 100%;
                text-align: center;
                font-size: 0.76rem;
                color: var(--text-muted);
                margin-bottom: 2px;
            }

            .discipline-tag {
                font-size: 0.76rem;
                padding: 4px 12px;
            }

            .stats-text {
                font-size: 0.8rem;
            }

            footer.site-footer {
                gap: 1.4rem;
                padding: 0.8rem 0 0.3rem;
            }

            .footer-link {
                font-size: 0.8rem;
            }
        }

        @media (max-width: 480px) {
            .page-wrapper {
                padding: 0.75rem 0.9rem 0.4rem;
            }

            header.top-nav {
                gap: 0.65rem;
                padding: 0.2rem 0;
            }

            .nav-link {
                font-size: 0.82rem;
            }

            .btn-get-started {
                font-size: 0.8rem;
                padding: 0.38rem 0.88rem;
            }

            main.hero-stage {
                gap: 0.65rem 0.75rem;
                padding: 0.35rem 0;
            }

            .seal-reactor-container {
                width: 66px;
                height: 66px;
            }

            .metallic-bezel {
                width: 62px;
                height: 62px;
            }

            .seal-inner-frame {
                width: 54px;
                height: 54px;
            }

            .tech-status-badge {
                font-size: 0.64rem;
                padding: 3.5px 11px;
                gap: 6px;
                margin-bottom: 0.7rem;
            }

            .brand-header {
                margin-bottom: 1.15rem;
            }

            .brand-title {
                font-size: 1.95rem;
            }

            .grad-cap-icon svg {
                width: 24px;
                height: 24px;
            }

            .brand-subtitle {
                font-size: 0.66rem;
                letter-spacing: 0.2em;
                margin-top: 0.35rem;
            }

            .search-pill-bar {
                padding: 6px 10px 6px 15px;
            }

            .search-terminal-prompt {
                font-size: 0.95rem;
            }

            .search-input {
                font-size: 0.85rem;
            }

            .search-input::placeholder {
                font-size: 0.82rem;
            }

            .discipline-tag {
                font-size: 0.72rem;
                padding: 3.5px 10px;
            }

            .stats-text {
                font-size: 0.76rem;
            }

            footer.site-footer {
                gap: 1rem;
                padding: 0.65rem 0 0.2rem;
            }

            .footer-link {
                font-size: 0.76rem;
            }
        }
    </style>
</head>
<body>

    <!-- IT Digital Network Mesh Canvas -->
    <canvas id="techNetworkCanvas" class="tech-bg-canvas"></canvas>

    <!-- IT Cyber / Digital Grid Pattern Overlay -->
    <div class="cyber-grid-overlay" aria-hidden="true"></div>

    <!-- ===== MAIN PAGE VIEWPORT ===== -->
    <div class="page-wrapper">

        <!-- TOP NAVIGATION BAR -->
        <header class="top-nav">
            <a href="{{ route('repository.index') }}" class="nav-link nav-link-browse">Browse</a>
            <a href="{{ route('similarity-checker') }}" class="nav-link">Similarity</a>

            @if (Route::has('login'))
                @auth
                    <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('student.dashboard') }}" class="btn-get-started">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="nav-link">Log In</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-get-started">
                            Get Started
                        </a>
                    @endif
                @endauth
            @endif
        </header>

        @if (session('status'))
            <div style="position: fixed; top: 76px; left: 50%; transform: translateX(-50%); z-index: 9999; background: rgba(6, 78, 59, 0.95); border: 1px solid rgba(16, 185, 129, 0.4); backdrop-filter: blur(12px); color: #ecfdf5; padding: 10px 22px; border-radius: 9999px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.35); pointer-events: none;">
                <span>✅</span>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <!-- HERO STAGE -->
        <main class="hero-stage">

            <!-- LEFT SEAL: J.H. Cerilles State College (JHCSC Emerald Cyber Reactor) -->
            <div class="seal-column seal-column-left">
                <div class="seal-reactor-container seal-reactor-left" title="J.H. Cerilles State College">
                    <!-- Concentric Sci-Fi HUD Orbit Ring -->
                    <svg class="reactor-hud-orbit" viewBox="0 0 320 320" aria-hidden="true">
                        <circle cx="160" cy="160" r="148" stroke="rgba(16, 185, 129, 0.22)" stroke-width="1.2" stroke-dasharray="4 8" fill="none"/>
                        <circle cx="160" cy="160" r="154" stroke="rgba(16, 185, 129, 0.45)" stroke-width="1.8" stroke-dasharray="25 60 40 80" fill="none"/>
                        <path d="M 12 160 A 148 148 0 0 1 160 12" stroke="rgba(16, 185, 129, 0.6)" stroke-width="2.2" fill="none" stroke-linecap="round"/>
                        <path d="M 308 160 A 148 148 0 0 1 160 308" stroke="rgba(16, 185, 129, 0.6)" stroke-width="2.2" fill="none" stroke-linecap="round"/>
                    </svg>

                    <!-- Metallic Bezel Frame -->
                    <div class="metallic-bezel jhcsc-metallic-bezel">
                        <span class="bezel-notch notch-t"></span>
                        <span class="bezel-notch notch-r"></span>
                        <span class="bezel-notch notch-b"></span>
                        <span class="bezel-notch notch-l"></span>
                        
                        <div class="seal-inner-frame">
                            <img src="{{ asset('images/jhcsc.png') }}" alt="J.H. Cerilles State College Seal — JHCSC">
                        </div>
                    </div>
                </div>
            </div>

            <!-- CENTER COLUMN: Brand Title and Search Bar -->
            <div class="center-column">
                
                <!-- High-Tech Status Indicator -->
                <div class="tech-status-badge">
                    <span class="status-pulse-dot"></span>
                    <span class="tech-badge-code">REPOSITORY-ONLINE</span>
                    <span class="tech-badge-sep">&bull;</span>
                    <span class="tech-badge-inst">SCS &bull; IT ARCHIVE</span>
                </div>

                <!-- Logo & Subtitle -->
                <div class="brand-header">
                    <div class="brand-title-wrap">
                        <h1 class="brand-title">
                            <span>capstone</span><span class="brand-hub">Hub</span>
                        </h1>
                        <!-- Graduation Cap Icon -->
                        <span class="grad-cap-icon" aria-hidden="true">
                            <svg width="40" height="40" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <polygon points="24,8 43,18 24,28 5,18" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round" fill="rgba(0, 245, 212, 0.15)"/>
                                <path d="M12 22V33C12 37 17 40 24 40C31 40 36 37 36 33V22" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" fill="none"/>
                                <path d="M40 20V32" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <circle cx="40" cy="33.5" r="1.8" fill="currentColor"/>
                            </svg>
                        </span>
                    </div>
                    <div class="brand-subtitle">CAPSTONE REPOSITORY</div>
                </div>

                <!-- Search Container -->
                <div class="search-container">
                    <form action="{{ route('search.index') }}" method="GET" id="searchForm">
                        <div class="search-pill-bar" id="searchBar">
                            <!-- Terminal Prompt Indicator -->
                            <div class="search-terminal-prompt" aria-hidden="true">&gt;_</div>

                            <input 
                                type="text" 
                                name="q" 
                                id="searchInput"
                                class="search-input" 
                                placeholder="Search capstones, algorithms, abstracts, or authors..." 
                                autocomplete="off"
                                spellcheck="false"
                            >
                            
                            <!-- Shortcut Key Badge -->
                            <div class="shortcut-badge" id="shortcutBadge" title="Press ⌘K or Ctrl+K to search">Ctrl K</div>

                            <!-- Search Icon Button -->
                            <button type="submit" class="search-icon-btn" aria-label="Search">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"/>
                                    <path d="M21 21l-4.35-4.35"/>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>

            </div>

            <!-- RIGHT SEAL: School of Computing Studies - JHCSC (Chrome/Titanium Cyber Reactor) -->
            <div class="seal-column seal-column-right">
                <div class="seal-reactor-container seal-reactor-right" title="School of Computing Studies — J.H. Cerilles State College">
                    <!-- Concentric Sci-Fi HUD Orbit Ring -->
                    <svg class="reactor-hud-orbit" viewBox="0 0 320 320" aria-hidden="true">
                        <circle cx="160" cy="160" r="148" stroke="rgba(0, 245, 212, 0.22)" stroke-width="1.2" stroke-dasharray="4 8" fill="none"/>
                        <circle cx="160" cy="160" r="154" stroke="rgba(0, 245, 212, 0.45)" stroke-width="1.8" stroke-dasharray="35 70 25 80" fill="none"/>
                        <path d="M 160 12 A 148 148 0 0 1 308 160" stroke="rgba(0, 245, 212, 0.6)" stroke-width="2.2" fill="none" stroke-linecap="round"/>
                        <path d="M 160 308 A 148 148 0 0 1 12 160" stroke="rgba(0, 245, 212, 0.6)" stroke-width="2.2" fill="none" stroke-linecap="round"/>
                    </svg>

                    <!-- Metallic Bezel Frame -->
                    <div class="metallic-bezel scs-metallic-bezel">
                        <span class="bezel-notch notch-t"></span>
                        <span class="bezel-notch notch-r"></span>
                        <span class="bezel-notch notch-b"></span>
                        <span class="bezel-notch notch-l"></span>
                        
                        <div class="seal-inner-frame">
                            <img src="{{ asset('images/scs.png') }}" alt="School of Computing Studies Seal — JHCSC">
                        </div>
                    </div>
                </div>
            </div>

        </main>

        <!-- FOOTER LINKS -->
        <footer class="site-footer">
            <button type="button" class="footer-link" onclick="openModal('aboutModal')">About</button>
            <button type="button" class="footer-link" onclick="openModal('termsModal')">Terms</button>
            <button type="button" class="footer-link" onclick="openModal('privacyModal')">Privacy</button>
            <button type="button" class="footer-link" onclick="openModal('helpModal')">Help</button>
        </footer>

    </div>

    <!-- ===== MODALS ===== -->

    <!-- About Modal -->
    <div class="modal-overlay" id="aboutModal" onclick="closeOnBackdrop(event, 'aboutModal')">
        <div class="modal-card">
            <button class="modal-close-btn" onclick="closeModal('aboutModal')">&times;</button>
            <h2 class="modal-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
                </svg>
                About capstoneHub
            </h2>
            <div class="modal-body">
                <p style="margin-bottom: 0.8rem;">
                    <strong>capstoneHub</strong> is the digital capstone research repository system of the <strong>School of Computing Studies (SCS)</strong> at J.H. Cerilles State College (JHCSC).
                </p>
                <p>
                    Engineered to preserve academic integrity, streamline manuscript archiving, and facilitate research discovery using smart semantic matching and similarity verification algorithms.
                </p>
            </div>
        </div>
    </div>

    <!-- Terms Modal -->
    <div class="modal-overlay" id="termsModal" onclick="closeOnBackdrop(event, 'termsModal')">
        <div class="modal-card">
            <button class="modal-close-btn" onclick="closeModal('termsModal')">&times;</button>
            <h2 class="modal-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
                </svg>
                Terms of Use
            </h2>
            <div class="modal-body">
                <p style="margin-bottom: 0.8rem;">
                    Access to capstoneHub is provided for academic, scholarly, and educational research purposes by students and faculty of J.H. Cerilles State College.
                </p>
                <p>
                    All research papers and intellectual materials remain the property of their respective student researchers and JHCSC under applicable academic intellectual property policies. Unauthorized redistribution is prohibited.
                </p>
            </div>
        </div>
    </div>

    <!-- Privacy Modal -->
    <div class="modal-overlay" id="privacyModal" onclick="closeOnBackdrop(event, 'privacyModal')">
        <div class="modal-card">
            <button class="modal-close-btn" onclick="closeModal('privacyModal')">&times;</button>
            <h2 class="modal-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                Privacy Policy
            </h2>
            <div class="modal-body">
                <p style="margin-bottom: 0.8rem;">
                    capstoneHub respects the privacy of students and researchers. Personal account information and uploaded research manuscripts are encrypted and safeguarded.
                </p>
                <p>
                    Search logs and manuscript similarity comparisons are processed strictly for quality evaluation, relevance optimization, and academic verification.
                </p>
            </div>
        </div>
    </div>

    <!-- Help Modal -->
    <div class="modal-overlay" id="helpModal" onclick="closeOnBackdrop(event, 'helpModal')">
        <div class="modal-card">
            <button class="modal-close-btn" onclick="closeModal('helpModal')">&times;</button>
            <h2 class="modal-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                Help & Guidelines
            </h2>
            <div class="modal-body">
                <p style="margin-bottom: 0.8rem;">
                    • <strong>Search:</strong> Type keywords, authors, or topics in the search bar. Use <code>⌘K</code> or <code>Ctrl+K</code> to jump straight to the search box.
                </p>
                <p>
                    • <strong>Similarity Check:</strong> Students and faculty can check manuscript uniqueness via the <a href="{{ route('similarity-checker') }}" style="color:#10b981; text-decoration:underline;">Similarity Checker</a>.
                </p>
            </div>
        </div>
    </div>

    <!-- ===== JAVASCRIPT LOGIC ===== -->
    <script>
        const searchInput = document.getElementById('searchInput');

        // Keyboard Shortcut: Cmd/Ctrl + K to focus search
        window.addEventListener('keydown', (e) => {
            if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                searchInput.focus();
                searchInput.select();
            }
        });

        // Detect OS for shortcut display badge (Ctrl K on Windows/Linux, ⌘K on Mac)
        const isMac = navigator.platform.toUpperCase().indexOf('MAC') >= 0;
        const shortcutBadge = document.getElementById('shortcutBadge');
        if (shortcutBadge && !isMac) {
            shortcutBadge.textContent = 'Ctrl K';
        }

        shortcutBadge?.addEventListener('click', () => {
            searchInput.focus();
            searchInput.select();
        });

        // Modal Open / Close Logic
        function openModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.add('open');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.remove('open');
                document.body.style.overflow = '';
            }
        }

        function closeOnBackdrop(event, id) {
            if (event.target === event.currentTarget) {
                closeModal(id);
            }
        }

        // Close modal on Escape key
        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-overlay.open').forEach(m => {
                    m.classList.remove('open');
                });
                document.body.style.overflow = '';
            }
        });

        // ===== IT NETWORK PARTICLES INTERACTIVE CANVAS =====
        const canvas = document.getElementById('techNetworkCanvas');
        if (canvas) {
            const ctx = canvas.getContext('2d');
            let width, height;
            let particles = [];
            let mouse = { x: null, y: null, maxDist: 150 };

            function resizeCanvas() {
                width = canvas.width = window.innerWidth;
                height = canvas.height = window.innerHeight;
            }
            resizeCanvas();
            window.addEventListener('resize', resizeCanvas);

            window.addEventListener('mousemove', (e) => {
                mouse.x = e.clientX;
                mouse.y = e.clientY;
            });
            window.addEventListener('mouseleave', () => {
                mouse.x = null;
                mouse.y = null;
            });

            // Lightweight particle nodes for smooth 60fps performance
            const PARTICLE_COUNT = Math.min(Math.floor((window.innerWidth * window.innerHeight) / 22000), 55);
            for (let i = 0; i < PARTICLE_COUNT; i++) {
                particles.push({
                    x: Math.random() * (width || window.innerWidth),
                    y: Math.random() * (height || window.innerHeight),
                    vx: (Math.random() - 0.5) * 0.5,
                    vy: (Math.random() - 0.5) * 0.5,
                    radius: Math.random() * 1.6 + 1.2,
                    isCyan: Math.random() > 0.65
                });
            }

            function animateParticles() {
                ctx.clearRect(0, 0, width, height);

                // Update & Connect Particles
                for (let i = 0; i < particles.length; i++) {
                    const p1 = particles[i];

                    p1.x += p1.vx;
                    p1.y += p1.vy;
                    if (p1.x < 0 || p1.x > width) p1.vx *= -1;
                    if (p1.y < 0 || p1.y > height) p1.vy *= -1;

                    // Mouse Interaction: Draw dynamic trace line to cursor
                    if (mouse.x !== null && mouse.y !== null) {
                        const dxm = mouse.x - p1.x;
                        const dym = mouse.y - p1.y;
                        const distM = Math.hypot(dxm, dym);
                        if (distM < mouse.maxDist) {
                            const alpha = (1 - (distM / mouse.maxDist)) * 0.45;
                            ctx.beginPath();
                            ctx.moveTo(p1.x, p1.y);
                            ctx.lineTo(mouse.x, mouse.y);
                            ctx.strokeStyle = `rgba(16, 185, 129, ${alpha})`;
                            ctx.lineWidth = 1;
                            ctx.stroke();
                        }
                    }

                    // Node to Node mesh connections
                    for (let j = i + 1; j < particles.length; j++) {
                        const p2 = particles[j];
                        const dx = p1.x - p2.x;
                        const dy = p1.y - p2.y;
                        const dist = Math.hypot(dx, dy);

                        if (dist < 130) {
                            const alpha = (1 - (dist / 130)) * 0.32;
                            ctx.beginPath();
                            ctx.moveTo(p1.x, p1.y);
                            ctx.lineTo(p2.x, p2.y);
                            ctx.strokeStyle = p1.isCyan 
                                ? `rgba(56, 189, 248, ${alpha})` 
                                : `rgba(16, 185, 129, ${alpha})`;
                            ctx.lineWidth = 0.9;
                            ctx.stroke();
                        }
                    }

                    // Draw Node Point with glow
                    ctx.beginPath();
                    ctx.arc(p1.x, p1.y, p1.radius, 0, Math.PI * 2);
                    ctx.fillStyle = p1.isCyan ? 'rgba(56, 189, 248, 0.85)' : 'rgba(52, 211, 153, 0.85)';
                    ctx.shadowColor = p1.isCyan ? '#38bdf8' : '#10b981';
                    ctx.shadowBlur = 8;
                    ctx.fill();
                    ctx.shadowBlur = 0;
                }

                requestAnimationFrame(animateParticles);
            }
            requestAnimationFrame(animateParticles);
        }
    </script>
</body>
</html>