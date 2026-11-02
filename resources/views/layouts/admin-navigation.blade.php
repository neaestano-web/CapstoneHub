<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

    .admin-sidebar-fixed {
        position: fixed;
        left: 0;
        top: 0;
        bottom: 0;
        height: 100vh;
        z-index: 100;
        width: 240px;
        transition: width 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        background: #0c4233;
        overflow-x: hidden;
    }

    .ecoursie-sidebar-panel {
        height: 100vh;
        padding: 32px 18px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    /* COLLAPSED STATES */
    html.admin-sidebar-is-collapsed .admin-sidebar-fixed,
    .admin-sidebar-fixed.is-collapsed {
        width: 80px;
    }

    html.admin-sidebar-is-collapsed .ecoursie-sidebar-panel,
    .admin-sidebar-fixed.is-collapsed .ecoursie-sidebar-panel {
        padding: 28px 12px;
    }

    /* SMOOTH FADE FOR LABELS */
    .sidebar-label {
        white-space: nowrap;
        opacity: 1;
        max-width: 160px;
        transition: opacity 0.2s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.35s cubic-bezier(0.4, 0, 0.2, 1), margin 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        display: inline-block;
        vertical-align: middle;
    }

    html.admin-sidebar-is-collapsed .sidebar-label,
    .admin-sidebar-fixed.is-collapsed .sidebar-label {
        opacity: 0;
        max-width: 0px !important;
        margin: 0 !important;
        pointer-events: none;
    }

    /* NAV ITEM STYLING */
    .ecoursie-nav-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 15px;
        border-radius: 16px;
        font-size: 13px;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        margin-bottom: 4px;
        position: relative;
    }

    html.admin-sidebar-is-collapsed .ecoursie-nav-item,
    .admin-sidebar-fixed.is-collapsed .ecoursie-nav-item {
        justify-content: center;
        padding: 11px 0;
    }

    .ecoursie-nav-item:hover {
        background: rgba(255, 255, 255, 0.12);
        color: #ffffff;
    }

    /* ACTIVE HIGHLIGHT PILL */
    .ecoursie-nav-active {
        background: rgba(255, 255, 255, 0.2) !important;
        color: #ffffff !important;
        font-weight: 800 !important;
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.2);
    }

    .nav-svg-icon {
        width: 19px;
        height: 19px;
        flex-shrink: 0;
    }

    .sidebar-badge-count {
        margin-left: auto;
        padding: 2px 7px;
        border-radius: 50px;
        font-size: 10px;
        font-weight: 800;
        line-height: 1;
    }
    
    html.admin-sidebar-is-collapsed .sidebar-badge-count,
    .admin-sidebar-fixed.is-collapsed .sidebar-badge-count {
        display: none;
    }
</style>

@php
    $pendingResCount = \App\Models\Research::where('status', 'pending')->count();
    $pendingRegCount = \App\Models\User::where('role','student')->whereNotNull('email_verified_at')->where('registration_status','pending')->count();
    $pendingDownloadCount = \App\Models\DownloadRequest::where('status','pending')->count();
@endphp

{{-- DESKTOP SIDEBAR --}}
<aside class="admin-sidebar-fixed hidden lg:block"
       :class="collapsed ? 'is-collapsed' : ''">
    <div class="ecoursie-sidebar-panel">
        <div>
            {{-- Brand Header --}}
            <div class="flex items-center justify-between pb-6 mb-4 border-b border-white/10" :class="collapsed ? 'justify-center flex-col gap-3 pb-6' : ''">
                <div class="flex items-center gap-3 min-w-0">
                    <a href="{{ route('admin.dashboard') }}" class="w-10 h-10 rounded-2xl bg-white/15 p-1 flex items-center justify-center shrink-0 border border-white/20 shadow-md overflow-hidden">
                        <img src="{{ asset('images/scs.png') }}" alt="JHCSC Logo" class="w-full h-full object-cover rounded-xl">
                    </a>
                    <div class="sidebar-label">
                        <strong style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.05rem; letter-spacing: -0.5px;" class="text-white font-extrabold block">
                            CAPSTONEHUB
                        </strong>
                        <span class="block text-[9px] font-bold text-amber-300 uppercase tracking-widest mt-0.5">
                            Administrator Portal
                        </span>
                    </div>
                </div>

                {{-- Collapse Toggle Button --}}
                <button @click="collapsed = !collapsed"
                        type="button"
                        class="w-6 h-6 rounded-lg bg-white/10 text-emerald-200 hover:bg-white/20 flex items-center justify-center transition shrink-0"
                        :title="collapsed ? 'Expand Sidebar' : 'Collapse Sidebar'">
                    <svg x-show="!collapsed" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" /></svg>
                    <svg x-show="collapsed" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                </button>
            </div>

            {{-- Navigation Items --}}
            <nav class="space-y-1">
                {{-- Dashboard --}}
                <a href="{{ route('admin.dashboard') }}" 
                   class="ecoursie-nav-item {{ request()->routeIs('admin.dashboard') ? 'ecoursie-nav-active' : '' }}">
                    <svg class="nav-svg-icon" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M4 4h6v6H4V4zm10 0h6v6h-6V4zM4 14h6v6H4v-6zm10 0h6v6h-6v-6z"/>
                    </svg>
                    <span class="sidebar-label">Dashboard</span>
                </a>

                {{-- Researches --}}
                <a href="{{ route('admin.researches.index') }}" 
                   class="ecoursie-nav-item {{ request()->routeIs('admin.researches.*') ? 'ecoursie-nav-active' : '' }}">
                    <svg class="nav-svg-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="sidebar-label">Researches</span>
                    @if($pendingResCount > 0)
                        <span class="sidebar-badge-count bg-amber-400 text-amber-950">{{ $pendingResCount }}</span>
                    @endif
                </a>

                {{-- Categories --}}
                <a href="{{ route('admin.categories.index') }}" 
                   class="ecoursie-nav-item {{ request()->routeIs('admin.categories.*') ? 'ecoursie-nav-active' : '' }}">
                    <svg class="nav-svg-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                    <span class="sidebar-label">Categories</span>
                </a>

                {{-- Users --}}
                <a href="{{ route('admin.users.index') }}" 
                   class="ecoursie-nav-item {{ request()->routeIs('admin.users.*') ? 'ecoursie-nav-active' : '' }}">
                    <svg class="nav-svg-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span class="sidebar-label">User Accounts</span>
                </a>

                {{-- Pending Registrations --}}
                <a href="{{ route('admin.pending-registrations.index') }}" 
                   class="ecoursie-nav-item {{ request()->routeIs('admin.pending-registrations.*') ? 'ecoursie-nav-active' : '' }}">
                    <svg class="nav-svg-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="sidebar-label">Registrations</span>
                    @if($pendingRegCount > 0)
                        <span class="sidebar-badge-count bg-amber-400 text-amber-950">{{ $pendingRegCount }}</span>
                    @endif
                </a>

                {{-- Download Requests --}}
                <a href="{{ route('admin.download-requests.index') }}" 
                   class="ecoursie-nav-item {{ request()->routeIs('admin.download-requests.*') ? 'ecoursie-nav-active' : '' }}">
                    <svg class="nav-svg-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span class="sidebar-label">Download Requests</span>
                    @if($pendingDownloadCount > 0)
                        <span class="sidebar-badge-count bg-emerald-400 text-emerald-950">{{ $pendingDownloadCount }}</span>
                    @endif
                </a>

                {{-- Repository Catalog --}}
                <a href="{{ route('repository.index') }}" 
                   class="ecoursie-nav-item {{ request()->routeIs('repository.*') ? 'ecoursie-nav-active' : '' }}">
                    <svg class="nav-svg-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span class="sidebar-label">Repository</span>
                </a>

                {{-- Similarity Checker --}}
                <a href="{{ route('similarity-checker') }}" 
                   class="ecoursie-nav-item {{ request()->routeIs('similarity-checker*') ? 'ecoursie-nav-active' : '' }}">
                    <svg class="nav-svg-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span class="sidebar-label">Similarity Checker</span>
                </a>
            </nav>
        </div>

        {{-- Bottom Section (Log Out) --}}
        <div class="pt-6 border-t border-white/10">
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="ecoursie-nav-item w-full text-rose-300 hover:text-rose-100 hover:bg-rose-900/30">
                    <svg class="nav-svg-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span class="sidebar-label">Log Out</span>
                </button>
            </form>
        </div>
    </div>
</aside>

{{-- MOBILE NAVBAR --}}
<div class="lg:hidden bg-[#0c4233] border-b border-emerald-800/40 px-4 py-3 sticky top-0 z-50 flex items-center justify-between shadow-lg">
    <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-xl bg-white/15 p-1 flex items-center justify-center shrink-0 border border-white/20 shadow-sm overflow-hidden">
            <img src="{{ asset('images/scs.png') }}" alt="JHCSC Logo" class="w-full h-full object-cover rounded-lg">
        </div>
        <strong style="font-family: 'Plus Jakarta Sans', sans-serif;" class="text-white text-sm">
            CAPSTONEHUB <span class="text-amber-300 text-[10px] font-bold">ADMIN</span>
        </strong>
    </div>

    <button @click="mobileOpen = !mobileOpen" type="button" class="p-2 text-emerald-200 hover:text-white focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
</div>

{{-- MOBILE DROPDOWN --}}
<div x-show="mobileOpen" x-transition class="lg:hidden bg-[#0c4233] border-b border-emerald-800/40 px-4 py-3 space-y-2">
    <a href="{{ route('admin.dashboard') }}" class="ecoursie-nav-item {{ request()->routeIs('admin.dashboard') ? 'ecoursie-nav-active' : '' }}">
        <span>Dashboard</span>
    </a>
    <a href="{{ route('admin.researches.index') }}" class="ecoursie-nav-item {{ request()->routeIs('admin.researches.*') ? 'ecoursie-nav-active' : '' }}">
        <span>Researches</span>
        @if($pendingResCount > 0)
            <span class="ml-auto px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-400 text-amber-950">{{ $pendingResCount }}</span>
        @endif
    </a>
    <a href="{{ route('admin.categories.index') }}" class="ecoursie-nav-item {{ request()->routeIs('admin.categories.*') ? 'ecoursie-nav-active' : '' }}">
        <span>Categories</span>
    </a>
    <a href="{{ route('admin.users.index') }}" class="ecoursie-nav-item {{ request()->routeIs('admin.users.*') ? 'ecoursie-nav-active' : '' }}">
        <span>User Accounts</span>
    </a>
    <a href="{{ route('admin.pending-registrations.index') }}" class="ecoursie-nav-item {{ request()->routeIs('admin.pending-registrations.*') ? 'ecoursie-nav-active' : '' }}">
        <span>Registrations</span>
    </a>
    <a href="{{ route('admin.download-requests.index') }}" class="ecoursie-nav-item {{ request()->routeIs('admin.download-requests.*') ? 'ecoursie-nav-active' : '' }}">
        <span>Download Requests</span>
    </a>
    <a href="{{ route('repository.index') }}" class="ecoursie-nav-item {{ request()->routeIs('repository.*') ? 'ecoursie-nav-active' : '' }}">
        <span>Repository</span>
    </a>
    <a href="{{ route('similarity-checker') }}" class="ecoursie-nav-item {{ request()->routeIs('similarity-checker*') ? 'ecoursie-nav-active' : '' }}">
        <span>Similarity Checker</span>
    </a>
    <form method="POST" action="{{ route('logout') }}" class="m-0 pt-2">
        @csrf
        <button type="submit" class="ecoursie-nav-item w-full text-rose-300">
            <span>Log Out</span>
        </button>
    </form>
</div>
