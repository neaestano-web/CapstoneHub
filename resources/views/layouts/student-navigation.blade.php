<style>
    .student-sidebar-fixed {
        position: fixed;
        left: 0;
        top: 0;
        bottom: 0;
        height: 100vh;
        z-index: 100;
        width: 240px;
        transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: #0c4233;
        border-right: 1px solid rgba(255, 255, 255, 0.08);
        overflow-x: hidden;
    }

    .ecoursie-sidebar-panel {
        height: 100vh;
        padding: 24px 14px;
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
        padding: 24px 10px;
    }

    /* FADE FOR LABELS */
    .sidebar-label {
        white-space: nowrap;
        opacity: 1;
        max-width: 160px;
        transition: opacity 0.2s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        display: inline-block;
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

    /* SECTION TITLE HEADERS */
    .sidebar-section-title {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.45);
        padding: 14px 12px 6px;
        display: block;
    }

    html.sidebar-is-collapsed .sidebar-section-title,
    .student-sidebar-fixed.is-collapsed .sidebar-section-title {
        display: none !important;
    }

    /* MINIMAL SIDEBAR NAV ITEM */
    .ecoursie-nav-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 14px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.72);
        text-decoration: none;
        transition: all 0.18s ease;
        margin-bottom: 3px;
    }

    html.sidebar-is-collapsed .ecoursie-nav-item,
    .student-sidebar-fixed.is-collapsed .ecoursie-nav-item {
        justify-content: center !important;
        align-items: center !important;
        padding: 10px 0 !important;
        width: 44px !important;
        height: 44px !important;
        margin-left: auto !important;
        margin-right: auto !important;
        gap: 0 !important;
    }

    .ecoursie-nav-item:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #ffffff;
    }

    /* ACTIVE HIGHLIGHT PILL */
    .ecoursie-nav-active {
        background: rgba(255, 255, 255, 0.16) !important;
        color: #ffffff !important;
        font-weight: 700 !important;
    }

    .nav-icon-wrap {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        flex-shrink: 0;
        color: rgba(255, 255, 255, 0.65);
        transition: color 0.18s ease;
    }

    .ecoursie-nav-item:hover .nav-icon-wrap,
    .ecoursie-nav-active .nav-icon-wrap {
        color: #ffffff !important;
    }

    /* USER MINI CARD */
    .sidebar-user-card {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 10px;
        border-radius: 12px;
        background: rgba(0, 0, 0, 0.15);
        margin-bottom: 6px;
    }

    .sidebar-user-card .sidebar-label {
        display: flex !important;
        flex-direction: column !important;
        align-items: flex-start !important;
        justify-content: center !important;
        line-height: 1.2 !important;
    }

    html.sidebar-is-collapsed .sidebar-user-card,
    .student-sidebar-fixed.is-collapsed .sidebar-user-card {
        justify-content: center;
        padding: 8px 0;
        background: transparent;
    }

    .sidebar-user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: 800;
        flex-shrink: 0;
    }
</style>

{{-- DESKTOP SIDEBAR --}}
<aside class="student-sidebar-fixed hidden lg:block"
       :class="collapsed ? 'is-collapsed' : ''">
    <div class="ecoursie-sidebar-panel">
        <div>
            {{-- Brand Logo --}}
            <div class="flex items-center justify-between pb-4 mb-3 border-b border-white/10" :class="collapsed ? 'justify-center flex-col gap-3 pb-4' : ''">
                <a href="{{ route('student.dashboard') }}" class="flex items-center gap-2.5 min-w-0" title="CapstoneHub Dashboard">
                    <div class="w-8 h-8 flex items-center justify-center shrink-0">
                        <img src="{{ asset('images/scs.png') }}" 
                             alt="JHCSC Logo" 
                             class="w-full h-full object-contain">
                    </div>
                    <div class="sidebar-label sidebar-brand-label">
                        <strong style="font-size: 0.95rem; letter-spacing: -0.3px;" class="text-white font-extrabold block w-full leading-tight">
                            CAPSTONE<span class="text-emerald-300">HUB</span>
                        </strong>
                        <span class="block text-[9px] font-bold uppercase tracking-wider text-emerald-200/70 w-full leading-tight">
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
            <nav class="space-y-0.5">
                {{-- SECTION 1: MAIN --}}
                <span class="sidebar-section-title">Navigation</span>

                {{-- Dashboard --}}
                <a href="{{ route('student.dashboard') }}" 
                   class="ecoursie-nav-item {{ request()->routeIs('student.dashboard') ? 'ecoursie-nav-active' : '' }}"
                   title="Dashboard">
                    <span class="nav-icon-wrap">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
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
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </span>
                    <span class="sidebar-label">Browse Repository</span>
                </a>

                {{-- SECTION 2: RESEARCH TOOLS --}}
                <span class="sidebar-section-title mt-2">Research Tools</span>

                {{-- My Submission --}}
                <a href="{{ route('student.researches.index') }}" 
                   class="ecoursie-nav-item {{ (request()->routeIs('student.researches.*') && !request()->routeIs('student.researches.create')) ? 'ecoursie-nav-active' : '' }}"
                   title="My Submission">
                    <span class="nav-icon-wrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </span>
                    <span class="sidebar-label">My Submission</span>
                </a>

                {{-- Similarity Checker --}}
                <a href="{{ route('similarity-checker') }}" 
                   class="ecoursie-nav-item {{ request()->routeIs('similarity-checker*') ? 'ecoursie-nav-active' : '' }}"
                   title="Similarity Checker">
                    <span class="nav-icon-wrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </span>
                    <span class="sidebar-label">Similarity Checker</span>
                </a>


            </nav>
        </div>

        {{-- Bottom Section (User Card & Logout) --}}
        <div class="pt-3 border-t border-white/10 space-y-1">
            {{-- User Mini Profile --}}
            <div class="sidebar-user-card" title="{{ Auth::user()->first_last_name }}">
                <div class="sidebar-user-avatar">
                    {{ Auth::user()->avatar_initial }}
                </div>
                <div class="sidebar-label min-w-0 flex-1">
                    <strong class="text-white text-xs font-bold block truncate w-full">{{ Auth::user()->first_last_name }}</strong>
                    <span class="text-[10px] text-emerald-200/70 font-medium block truncate w-full">Student Researcher</span>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" 
                        class="ecoursie-nav-item w-full text-rose-200/80 hover:text-white hover:bg-rose-900/30 cursor-pointer"
                        title="Log Out">
                    <span class="nav-icon-wrap text-rose-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
<div class="lg:hidden bg-[#0c4233] border-b border-white/10 px-4 py-3 sticky top-0 z-50 flex items-center justify-between shadow-xs">
    <div class="flex items-center gap-2.5">
        <div class="w-7 h-7 flex items-center justify-center shrink-0">
            <img src="{{ asset('images/scs.png') }}" alt="JHCSC Logo" class="w-full h-full object-contain">
        </div>
        <strong style="font-size: 0.95rem; letter-spacing: -0.3px;" class="text-white font-extrabold">
            CAPSTONE<span class="text-emerald-300">HUB</span>
        </strong>
    </div>

    <button @click="mobileOpen = !mobileOpen" type="button" class="p-1.5 text-emerald-100 hover:text-white focus:outline-none">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
</div>

{{-- MOBILE DROPDOWN --}}
<div x-show="mobileOpen" x-transition class="lg:hidden bg-[#0c4233] border-b border-white/10 px-4 py-3 space-y-1 shadow-lg">
    <a href="{{ route('student.dashboard') }}" class="ecoursie-nav-item {{ request()->routeIs('student.dashboard') ? 'ecoursie-nav-active' : '' }}">
        <span>Dashboard</span>
    </a>
    <a href="{{ route('repository.index') }}" class="ecoursie-nav-item {{ request()->routeIs('repository.*') ? 'ecoursie-nav-active' : '' }}">
        <span>Browse Repository</span>
    </a>
    <a href="{{ route('student.researches.index') }}" class="ecoursie-nav-item {{ (request()->routeIs('student.researches.*') && !request()->routeIs('student.researches.create')) ? 'ecoursie-nav-active' : '' }}">
        <span>My Submission</span>
    </a>
    <a href="{{ route('similarity-checker') }}" class="ecoursie-nav-item {{ request()->routeIs('similarity-checker*') ? 'ecoursie-nav-active' : '' }}">
        <span>Similarity Checker</span>
    </a>

    <form method="POST" action="{{ route('logout') }}" class="m-0 pt-2 border-t border-white/10">
        @csrf
        <button type="submit" class="ecoursie-nav-item w-full text-rose-200/80 hover:bg-rose-900/30">
            <span>Log Out</span>
        </button>
    </form>
</div>
