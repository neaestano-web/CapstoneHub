@extends('layouts.admin')
@section('title', 'User Accounts — Admin Panel')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    .admin-users-wrap {
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        color: #0f172a;
    }
</style>

{{-- FIXED / STICKY HEADER & CONTROLS DECK --}}
<div class="sticky top-14 lg:top-0 z-30 border-b border-slate-200/90 -mt-6 -mx-5 px-5 lg:-mt-9 lg:-mx-10 lg:px-10 pt-5 pb-4 shadow-xs transition-colors mb-6" style="background-color: var(--guest-bg-surface, #ffffff);">
    <div class="max-w-7xl mx-auto space-y-3.5">
        
        {{-- Top Row: Title, Subtitle, and Add User Button --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">User Accounts</h1>
                <p class="text-slate-500 text-xs font-medium mt-0.5">
                    Overview of active student accounts and institutional administrators.
                </p>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('admin.users.create') }}" 
                   class="px-4 py-2 bg-[#0c4233] hover:bg-[#082d23] text-white font-bold text-xs rounded-xl transition shadow-xs flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add User
                </a>
            </div>
        </div>

        {{-- Bottom Row: Role Filter Tabs & Search Form --}}
        <div class="bg-white rounded-2xl border border-slate-200/90 p-3.5 flex flex-col md:flex-row items-center justify-between gap-3 shadow-xs">
            
            {{-- Role Filter Tabs --}}
            <div class="flex items-center gap-1.5 flex-wrap w-full md:w-auto">
                <a href="{{ route('admin.users.index', ['role' => 'all', 'search' => request('search')]) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition inline-flex items-center gap-1.5 {{ ($role ?? 'all') === 'all' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                    All Active Accounts
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-extrabold {{ ($role ?? 'all') === 'all' ? 'bg-white text-slate-900' : 'bg-slate-100 text-slate-700' }}">
                        {{ $allCount ?? $users->total() }}
                    </span>
                </a>

                <a href="{{ route('admin.users.index', ['role' => 'student', 'search' => request('search')]) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition inline-flex items-center gap-1.5 {{ ($role ?? '') === 'student' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                    Approved Students
                    @if(($approvedCount ?? 0) > 0)
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-extrabold {{ ($role ?? '') === 'student' ? 'bg-white text-slate-900' : 'bg-emerald-100 text-emerald-800' }}">
                            {{ $approvedCount }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('admin.users.index', ['role' => 'admin', 'search' => request('search')]) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition inline-flex items-center gap-1.5 {{ ($role ?? '') === 'admin' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                    Administrators
                    @if(($adminCount ?? 0) > 0)
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-extrabold {{ ($role ?? '') === 'admin' ? 'bg-white text-slate-900' : 'bg-slate-100 text-slate-700' }}">
                            {{ $adminCount }}
                        </span>
                    @endif
                </a>
            </div>

            {{-- Search Form --}}
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-2 w-full md:w-auto">
                <input type="hidden" name="role" value="{{ $role ?? 'all' }}">
                <div class="relative w-full md:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, ID..."
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2 pl-9 pr-3 text-xs font-medium text-slate-800 outline-none focus:border-slate-400 focus:bg-white transition">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <button type="submit" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition shrink-0">
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.users.index', ['role' => $role ?? 'all']) }}" class="px-2 py-2 text-slate-400 hover:text-slate-600 font-bold text-xs shrink-0">
                        Clear
                    </a>
                @endif
            </form>
        </div>

    </div>
</div>

<div class="admin-users-wrap max-w-7xl mx-auto space-y-6">

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="flex items-center justify-between p-4 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-xl text-xs font-semibold">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 text-sm font-bold">✕</button>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center justify-between p-4 bg-rose-50 border border-rose-200 text-rose-900 rounded-xl text-xs font-semibold">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800 text-sm font-bold">✕</button>
        </div>
    @endif

    {{-- USERS TABLE CARD --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-bold text-sm text-slate-900">Active Accounts List</h2>
            <span class="text-xs text-slate-400 font-medium">Total: {{ $users->total() }}</span>
        </div>

        <div class="overflow-x-auto min-h-[240px]">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-3 px-6">User</th>
                        <th class="py-3 px-6">Email</th>
                        <th class="py-3 px-6">Role</th>
                        <th class="py-3 px-6">Account Status</th>
                        <th class="py-3 px-6">Papers</th>
                        <th class="py-3 px-6">Joined Date</th>
                        <th class="py-3 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0 border border-slate-200">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.users.show', $user) }}" class="font-bold text-slate-900 hover:text-[#0c4233] transition">
                                            {{ $user->name }}
                                        </a>
                                        @if($user->id === auth()->id())
                                            <span class="ml-1 px-1.5 py-0.2 rounded text-[9px] font-bold bg-slate-100 text-slate-600">YOU</span>
                                        @endif
                                        @if($user->student_id_number)
                                            <div class="text-[10px] text-slate-400 font-mono">{{ $user->student_id_number }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-6 text-slate-600 font-medium">
                                {{ $user->email }}
                            </td>
                            <td class="py-3.5 px-6">
                                @if($user->role == 'admin')
                                    <span class="px-2 py-0.5 bg-amber-50 text-amber-800 border border-amber-200 font-bold text-[11px] rounded-md">
                                        Admin
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 bg-slate-100 text-slate-700 font-medium text-[11px] rounded-md">
                                        Student
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="inline-flex items-center gap-1 text-emerald-700 font-semibold text-xs">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Active
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-slate-600 font-medium">
                                {{ $user->researches_count }}
                            </td>
                            <td class="py-3.5 px-6 text-slate-400 font-medium">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="py-3.5 px-6 text-right relative">
                                <div class="relative inline-block text-left" x-data="{ open: false }" @click.outside="open = false">
                                    <button type="button" 
                                            @click="open = !open" 
                                            class="w-8 h-8 inline-flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition" 
                                            title="Actions"
                                            aria-label="Actions">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                        </svg>
                                    </button>

                                    <div x-show="open" 
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="transform opacity-100 scale-100"
                                         x-transition:leave-end="transform opacity-0 scale-95"
                                         class="absolute right-0 mt-1 w-36 bg-white rounded-xl shadow-lg border border-slate-200 py-1 z-50 focus:outline-none"
                                         style="display: none;">
                                        
                                        <a href="{{ route('admin.users.show', $user) }}" 
                                           class="flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition text-left">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            View
                                        </a>

                                        <a href="{{ route('admin.users.edit', $user) }}" 
                                           class="flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition text-left">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit
                                        </a>

                                        @if($user->id !== auth()->id())
                                            <div class="border-t border-slate-100 my-1"></div>
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Delete this user permanently?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" 
                                                        class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition text-left">
                                                    <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 text-xs">
                                No active accounts found matching your filter or search.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
