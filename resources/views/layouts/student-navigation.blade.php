<style>
    @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

    .student-sidebar-fixed {
        position: fixed;
        left: 0;
        top: 0;
        bottom: 0;
        height: 100vh;
        z-index: 100;
        width: 240px;
        transition: width 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        background: linear-gradient(180deg, #07251d 0%, #0c4233 45%, #082d23 100%);
        border-right: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 6px 0 28px rgba(0, 0, 0, 0.35);
        overflow-x: hidden;
    }

    .ecoursie-sidebar-panel {
        height: 100vh;
        padding: 26px 16px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    /* COLLAPSED STATES */
    html.sidebar-is-collapsed .student-sidebar-fixed,
    .student-sidebar-fixed.is-collapsed {
        width: 80px;
    }

    html.sidebar-is-collapsed .ecoursie-sidebar-panel,
    .student-sidebar-fixed.is-collapsed .ecoursie-sidebar-panel {
        padding: 24px 8px;
    }

    /* SMOOTH FADE FOR LABELS */
    .sidebar-label {
        white-space: nowrap;
        opacity: 1;
        max-width: 160px;
        transition: opacity 0.2s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.35s cubic-bezier(0.4, 0, 0.2, 1), margin 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        display: inline-flex;
        align-items: center;
        vertical-align: middle;
    }

    .sidebar-brand-label {
        display: flex !important;
        flex-direction: column !important;
        align-items: flex-start !important;
        justify-content: center !important;
        line-height: 1.15 !important;
    }

    html.sidebar-is-collapsed .sidebar-label,
    .student-sidebar-fixed.is-collapsed .sidebar-label {
        display: none !important;
        opacity: 0 !important;
        max-width: 0px !important;
        margin: 0 !important;
        padding: 0 !important;
        pointer-events: none !important;
    }

    html.sidebar-is-collapsed .nav-tag-pill,
    .student-sidebar-fixed.is-collapsed .nav-tag-pill {
        display: none !important;
    }

    /* SECTION TITLE HEADERS */
    .sidebar-section-title {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.65rem;
        font-weight: 800;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--guest-accent, #00f5d4);
        padding: 14px 12px 6px;
        display: block;
        transition: opacity 0.2s ease;
    }

    html.sidebar-is-collapsed .sidebar-section-title,
    .student-sidebar-fixed.is-collapsed .sidebar-section-title {
        display: none !important;
    }

    /* CYBER-ACADEMIC SIDEBAR NAV ITEM */
    .ecoursie-nav-item {
        position: relative;
        display: flex;
        align-items: center;
        gap: 13px;
        padding: 11px 14px;
        border-radius: 14px;
        font-size: 13.5px;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.72);
        text-decoration: none;
        transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
        margin-bottom: 5px;
        border: 1px solid transparent;
    }

    html.sidebar-is-collapsed .ecoursie-nav-item,
    .student-sidebar-fixed.is-collapsed .ecoursie-nav-item {
        justify-content: center !important;
        align-items: center !important;
        padding: 0 !important;
        width: 44px !important;
        height: 44px !important;
        margin-left: auto !important;
        margin-right: auto !important;
        gap: 0 !important;
    }

    .ecoursie-nav-item:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #ffffff;
        transform: translateX(3px);
    }

    html.sidebar-is-collapsed .ecoursie-nav-item:hover,
    .student-sidebar-fixed.is-collapsed .ecoursie-nav-item:hover {
        transform: none !important;
        background: rgba(255, 255, 255, 0.12);
    }

    /* ACTIVE HIGHLIGHT PILL MATCHING GUEST CYBER DESIGN */
    .ecoursie-nav-active {
        background: linear-gradient(90deg, var(--guest-accent-glow, rgba(0, 245, 212, 0.18)) 0%, rgba(16, 185, 129, 0.08) 100%) !important;
        border: 1px solid var(--guest-accent, rgba(0, 245, 212, 0.38)) !important;
        color: #ffffff !important;
        font-weight: 800 !important;
        box-shadow: 0 4px 18px var(--guest-accent-glow, rgba(0, 245, 212, 0.18)), inset 0 1px 1px rgba(255, 255, 255, 0.2) !important;
    }

    .ecoursie-nav-active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 25%;
        bottom: 25%;
        width: 3.5px;
        border-radius: 0 4px 4px 0;
        background: var(--guest-accent, #00f5d4);
        box-shadow: 0 0 10px var(--guest-accent, #00f5d4);
    }

    html.sidebar-is-collapsed .ecoursie-nav-active::before,
    .student-sidebar-fixed.is-collapsed .ecoursie-nav-active::before {
        display: none !important;
    }

    .nav-icon-wrap {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        flex-shrink: 0;
        color: rgba(255, 255, 255, 0.65);
        transition: color 0.2s ease, transform 0.2s ease;
    }

    html.sidebar-is-collapsed .nav-icon-wrap,
    .student-sidebar-fixed.is-collapsed .nav-icon-wrap {
        margin: 0 !important;
    }

    .ecoursie-nav-item:hover .nav-icon-wrap {
        color: var(--guest-accent, #00f5d4);
        transform: scale(1.1);
    }

    .ecoursie-nav-active .nav-icon-wrap {
        color: var(--guest-accent, #00f5d4) !important;
        filter: drop-shadow(0 0 8px var(--guest-accent, #00f5d4));
    }

    /* USER MINI CARD */
    .sidebar-user-card {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        background: rgba(0, 0, 0, 0.22);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 14px;
        margin-bottom: 8px;
        transition: all 0.2s ease;
    }

    .sidebar-user-card .sidebar-label {
        display: flex !important;
        flex-direction: column !important;
        align-items: flex-start !important;
        justify-content: center !important;
        line-height: 1.25 !important;
        gap: 2px !important;
    }

    html.sidebar-is-collapsed .sidebar-user-card,
    .student-sidebar-fixed.is-collapsed .sidebar-user-card {
        justify-content: center;
        padding: 10px 0;
        background: transparent;
        border-color: transparent;
    }

    .sidebar-user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--guest-accent, #00f5d4) 0%, #059669 100%);
        color: var(--guest-accent-text, #042f2e);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.82rem;
        font-weight: 900;
        flex-shrink: 0;
        box-shadow: 0 2px 8px var(--guest-accent-glow, rgba(0, 245, 212, 0.3));
    }

    .nav-tag-pill {
        margin-left: auto;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.62rem;
        font-weight: 800;
        padding: 2px 6px;
        border-radius: 6px;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        background: var(--guest-accent-glow, rgba(0, 245, 212, 0.15));
        border: 1px solid var(--guest-accent, #00f5d4);
        color: var(--guest-accent, #00f5d4);
    }
</style>

{{-- DESKTOP SIDEBAR --}}
<aside class="student-sidebar-fixed hidden lg:block"
       :class="collapsed ? 'is-collapsed' : ''">
    <div class="ecoursie-sidebar-panel">
        <div>
            {{-- Brand Logo (CLEAN NATURAL LOGO WITH NO SQUARED FRAME) --}}
            <div class="flex items-center justify-between pb-5 mb-3 border-b border-white/10" :class="collapsed ? 'justify-center flex-col gap-3 pb-5' : ''">
                <a href="{{ route('student.dashboard') }}" class="flex items-center gap-3 min-w-0 group" title="CapstoneHub Dashboard">
                    {{-- Clean SCS Logo without square border --}}
                    <div class="w-9 h-9 flex items-center justify-center shrink-0">
                        <img src="{{ asset('images/scs.png') }}" 
                             alt="JHCSC Logo" 
                             class="w-full h-full object-contain filter drop-shadow(0 2px 6px rgba(0,245,212,0.35)) transition-transform duration-300 group-hover:scale-105">
                    </div>
                    <div class="sidebar-label sidebar-brand-label">
                        <strong style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.05rem; letter-spacing: -0.5px;" class="text-white font-extrabold block w-full leading-tight">
                            CAPSTONE<span style="color: var(--guest-accent, #00f5d4);">HUB</span>
                        </strong>
                        <span class="block text-[9px] font-mono font-bold uppercase tracking-widest mt-0.5 w-full leading-tight" style="color: var(--guest-accent, #00f5d4);">
                            Student Portal
                        </span>
                    </div>
                </a>

                {{-- Collapse Toggle Button --}}
                <button @click="collapsed = !collapsed"
                        type="button"
                        class="w-7 h-7 rounded-lg bg-white/10 text-emerald-200 hover:bg-white/20 hover:text-white flex items-center justify-center transition shrink-0"
                        :title="collapsed ? 'Expand Sidebar' : 'Collapse Sidebar'">
                    <svg x-show="!collapsed" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" /></svg>
                    <svg x-show="collapsed" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                </button>
            </div>

            {{-- Navigation Items --}}
            <nav class="space-y-1">
                {{-- SECTION 1: MAIN --}}
                <span class="sidebar-section-title">Navigation</span>

                {{-- Dashboard --}}
                <a href="{{ route('student.dashboard') }}" 
                   class="ecoursie-nav-item {{ request()->routeIs('student.dashboard') ? 'ecoursie-nav-active' : '' }}"
                   title="Dashboard">
                    <span class="nav-icon-wrap">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M4 4h6v6H4V4zm10 0h6v6h-6V4zM4 14h6v6H4v-6zm10 0h6v6h-6v-6z"/>
                        </svg>
                    </span>
                    <span class="sidebar-label">Dashboard</span>
                </a>

                {{-- Browse Repository --}}
                <a href="{{ route('repository.index') }}" 
                   class="ecoursie-nav-item {{ request()->routeIs('repository.*') ? 'ecoursie-nav-active' : '' }}"
                   title="Browse Repository">
                    <span class="nav-icon-wrap">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </span>
                    <span class="sidebar-label">Browse Repository</span>
                </a>

                {{-- SECTION 2: RESEARCH TOOLS --}}
                <span class="sidebar-section-title">Research Tools</span>

                {{-- My Submissions --}}
                <a href="{{ route('student.researches.index') }}" 
                   class="ecoursie-nav-item {{ (request()->routeIs('student.researches.*') && !request()->routeIs('student.researches.create')) ? 'ecoursie-nav-active' : '' }}"
                   title="My Submissions">
                    <span class="nav-icon-wrap">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </span>
                    <span class="sidebar-label">My Submissions</span>
                </a>

                {{-- Similarity Checker --}}
                <a href="{{ route('similarity-checker') }}" 
                   class="ecoursie-nav-item {{ request()->routeIs('similarity-checker*') ? 'ecoursie-nav-active' : '' }}"
                   title="Similarity Checker">
                    <span class="nav-icon-wrap">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </span>
                    <span class="sidebar-label">Similarity Checker</span>
                </a>

                {{-- Upload Manuscript --}}
                <a href="{{ route('student.researches.create') }}" 
                   class="ecoursie-nav-item {{ request()->routeIs('student.researches.create') ? 'ecoursie-nav-active' : '' }}"
                   title="Upload Manuscript">
                    <span class="nav-icon-wrap">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                    </span>
                    <span class="sidebar-label">Upload Manuscript</span>
                </a>
            </nav>
        </div>

        {{-- Bottom Section (User Card & Logout) --}}
        <div class="pt-4 border-t border-white/10 space-y-1">
            {{-- User Mini Profile --}}
            <div class="sidebar-user-card" title="{{ Auth::user()->first_last_name }}">
                <div class="sidebar-user-avatar">
                    {{ Auth::user()->avatar_initial }}
                </div>
                <div class="sidebar-label min-w-0 flex-1">
                    <strong class="text-white text-xs font-bold block truncate w-full">{{ Auth::user()->first_last_name }}</strong>
                    <span class="text-[10px] text-emerald-300/80 font-medium block truncate w-full">Student Researcher</span>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" 
                        class="ecoursie-nav-item w-full text-rose-300 hover:text-rose-100 hover:bg-rose-950/40 hover:border-rose-500/30 cursor-pointer"
                        title="Log Out">
                    <span class="nav-icon-wrap text-rose-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </span>
                    <span class="sidebar-label">Log Out</span>
                </button>
            </form>
        </div>
    </div>
</aside>

{{-- MOBILE NAVBAR --}}
<div class="lg:hidden bg-[#07251d] border-b border-emerald-800/40 px-4 py-3 sticky top-0 z-50 flex items-center justify-between shadow-lg backdrop-blur-md">
    <div class="flex items-center gap-3">
        <div class="w-8 h-8 flex items-center justify-center shrink-0">
            <img src="{{ asset('images/scs.png') }}" alt="JHCSC Logo" class="w-full h-full object-contain filter drop-shadow(0 2px 4px rgba(0,245,212,0.3))">
        </div>
        <strong style="font-family: 'Plus Jakarta Sans', sans-serif;" class="text-white text-sm">
            CAPSTONE<span style="color: var(--guest-accent, #00f5d4);">HUB</span>
        </strong>
    </div>

    <button @click="mobileOpen = !mobileOpen" type="button" class="p-2 text-emerald-200 hover:text-white focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
</div>

{{-- MOBILE DROPDOWN --}}
<div x-show="mobileOpen" x-transition class="lg:hidden bg-[#07251d] border-b border-emerald-800/40 px-4 py-3 space-y-1.5 shadow-2xl">
    <a href="{{ route('student.dashboard') }}" class="ecoursie-nav-item {{ request()->routeIs('student.dashboard') ? 'ecoursie-nav-active' : '' }}">
        <span>Dashboard</span>
    </a>
    <a href="{{ route('repository.index') }}" class="ecoursie-nav-item {{ request()->routeIs('repository.*') ? 'ecoursie-nav-active' : '' }}">
        <span>Browse Repository</span>
    </a>
    <a href="{{ route('student.researches.index') }}" class="ecoursie-nav-item {{ (request()->routeIs('student.researches.*') && !request()->routeIs('student.researches.create')) ? 'ecoursie-nav-active' : '' }}">
        <span>My Submissions</span>
    </a>
    <a href="{{ route('similarity-checker') }}" class="ecoursie-nav-item {{ request()->routeIs('similarity-checker*') ? 'ecoursie-nav-active' : '' }}">
        <span>Similarity Checker</span>
    </a>
    <a href="{{ route('student.researches.create') }}" class="ecoursie-nav-item {{ request()->routeIs('student.researches.create') ? 'ecoursie-nav-active' : '' }}">
        <span>Upload Manuscript</span>
    </a>
    <form method="POST" action="{{ route('logout') }}" class="m-0 pt-2 border-t border-white/10">
        @csrf
        <button type="submit" class="ecoursie-nav-item w-full text-rose-300 hover:bg-rose-950/40">
            <span>Log Out</span>
        </button>
    </form>
</div>
