@extends('layouts.admin')
@section('title', 'Manage Users — JHCSC Capstone Portal')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

    .admin-users-body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: #2d2b56;
    }

    .table-container-card {
        background: #ffffff;
        border-radius: 24px;
        border: 1.5px solid #e2e8f0;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
    }

    .btn-create-primary {
        background: #0c4233;
        color: #ffffff;
        padding: 10px 24px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 13px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.25s ease;
        box-shadow: 0 4px 14px rgba(12, 66, 51, 0.25);
    }

    .btn-create-primary:hover {
        background: #082d23;
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(12, 66, 51, 0.35);
        color: #ffffff;
    }
</style>

<div class="admin-users-body space-y-6 max-w-7xl mx-auto">
    {{-- HEADER SECTION --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-4 border-b border-slate-200/80">
        <div>
            <h1 style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 2rem;" class="font-extrabold text-[#2d2b56] tracking-tight leading-tight">
                Manage <span class="text-[#0c4233]">User Accounts</span>
            </h1>
            <p class="text-slate-500 text-xs font-medium mt-1">
                Overview of all student researchers, faculty members, and system administrators.
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('admin.users.create') }}" class="btn-create-primary">
                <span>➕</span> Add New User
            </a>
        </div>
    </div>

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <span class="text-base">✅</span>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 text-sm font-black">✕</button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-bold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <span class="text-base">⚠️</span>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800 text-sm font-black">✕</button>
        </div>
    @endif

    {{-- SEARCH TOOLBAR --}}
    <div class="p-4 bg-white rounded-3xl border border-slate-200/80 flex items-center justify-between gap-4 shadow-xs">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-2 w-full max-w-md">
            <div class="relative w-full">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, or role..."
                       class="w-full bg-slate-50 border border-slate-200 rounded-full py-2 pl-9 pr-4 text-xs font-semibold text-[#2d2b56] outline-none focus:border-[#0c4233] focus:bg-white transition">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs">🔍</span>
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-[#2d2b56] font-extrabold text-xs rounded-full transition shrink-0">
                Search
            </button>
            @if(request('search'))
                <a href="{{ route('admin.users.index') }}" class="px-3 py-2 text-slate-400 hover:text-slate-700 font-extrabold text-xs shrink-0">
                    Clear
                </a>
            @endif
        </form>

        <span class="text-xs font-bold text-slate-400 hidden sm:inline">Total: {{ $users->total() }} Accounts</span>
    </div>

    {{-- TABLE CONTAINER CARD --}}
    <div class="table-container-card">
        <div class="p-5 border-b border-slate-100 bg-slate-50/80 flex items-center justify-between">
            <h3 class="font-extrabold text-sm text-[#2d2b56] flex items-center gap-2">
                <span>👥</span> Registered System Users
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200/80 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
                        <th class="py-4 px-6">User Account</th>
                        <th class="py-4 px-6">Email Address</th>
                        <th class="py-4 px-6">Role</th>
                        <th class="py-4 px-6">Submissions</th>
                        <th class="py-4 px-6">Joined Date</th>
                        <th class="py-4 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-semibold">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-emerald-700 text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-xs">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.users.show', $user) }}" class="font-extrabold text-sm text-[#2d2b56] hover:text-[#5840bb] transition">
                                            {{ $user->name }}
                                        </a>
                                        @if($user->id === auth()->id())
                                            <span class="ml-1.5 px-2 py-0.5 rounded-full text-[9px] font-black bg-emerald-100 text-emerald-800 uppercase tracking-wider">YOU</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-slate-600 font-bold">
                                {{ $user->email }}
                            </td>
                            <td class="py-4 px-6">
                                @if($user->role == 'admin')
                                    <span class="px-3 py-1 bg-amber-100 text-amber-900 font-extrabold text-xs rounded-full inline-flex items-center gap-1">
                                        👑 Admin
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-indigo-50 text-indigo-700 font-extrabold text-xs rounded-full inline-flex items-center gap-1">
                                        🎓 Student
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-0.5 bg-slate-100 text-slate-700 text-xs font-extrabold rounded-full">
                                    {{ $user->researches_count }} papers
                                </span>
                            </td>
                            <td class="py-4 px-6 text-slate-500">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.users.show', $user) }}" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-[#2d2b56] rounded-full font-extrabold text-xs transition">
                                        View
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="px-3.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-full font-extrabold text-xs transition">
                                        Edit
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Delete this user permanently?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="px-3.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-full font-extrabold text-xs transition">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center space-y-3">
                                <div class="w-14 h-14 bg-indigo-50 text-indigo-700 rounded-full flex items-center justify-center font-bold text-2xl mx-auto">
                                    👥
                                </div>
                                <h4 class="font-extrabold text-base text-[#2d2b56]">No Users Found</h4>
                                <p class="text-xs text-slate-400 max-w-sm mx-auto">No accounts match your query.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
