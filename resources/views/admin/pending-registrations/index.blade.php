@extends('layouts.admin')
@section('title', 'Student Registrations — JHCSC Capstone Portal')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto pb-12">
    {{-- HEADER SECTION --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Student Registrations</h1>
            <p class="text-slate-500 text-xs font-medium mt-0.5">
                Review and verify student identity documents and approve account activation.
            </p>
        </div>
        <a href="{{ route('admin.users.index') }}" 
           class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition inline-flex items-center gap-2">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Active User Accounts
        </a>
    </div>

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

    {{-- FILTER & SEARCH TOOLBAR --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 flex flex-col md:flex-row items-center justify-between gap-4">
        {{-- Status Filter Tabs --}}
        <div class="flex items-center gap-1.5 flex-wrap w-full md:w-auto">
            <a href="{{ route('admin.pending-registrations.index', ['filter' => 'pending', 'search' => request('search')]) }}" 
               class="px-3 py-1.5 rounded-lg text-xs font-bold transition inline-flex items-center gap-1.5 {{ ($filter ?? 'pending') === 'pending' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                <span>Pending Review</span>
                @if(($pendingCount ?? 0) > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-extrabold {{ ($filter ?? 'pending') === 'pending' ? 'bg-amber-400 text-amber-950' : 'bg-amber-100 text-amber-800' }}">
                        {{ $pendingCount }}
                    </span>
                @endif
            </a>

            <a href="{{ route('admin.pending-registrations.index', ['filter' => 'rejected', 'search' => request('search')]) }}" 
               class="px-3 py-1.5 rounded-lg text-xs font-bold transition inline-flex items-center gap-1.5 {{ ($filter ?? '') === 'rejected' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                <span>Rejected</span>
                @if(($rejectedCount ?? 0) > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-extrabold {{ ($filter ?? '') === 'rejected' ? 'bg-white text-slate-900' : 'bg-rose-100 text-rose-800' }}">
                        {{ $rejectedCount }}
                    </span>
                @endif
            </a>

            <a href="{{ route('admin.pending-registrations.index', ['filter' => 'all', 'search' => request('search')]) }}" 
               class="px-3 py-1.5 rounded-lg text-xs font-bold transition inline-flex items-center gap-1.5 {{ ($filter ?? '') === 'all' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                <span>All Applications</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-extrabold {{ ($filter ?? '') === 'all' ? 'bg-white text-slate-900' : 'bg-slate-100 text-slate-700' }}">
                    {{ $allCount ?? $registrations->total() }}
                </span>
            </a>
        </div>

        {{-- Search Form --}}
        <form method="GET" action="{{ route('admin.pending-registrations.index') }}" class="flex items-center gap-2 w-full md:w-auto">
            <input type="hidden" name="filter" value="{{ $filter }}">
            <div class="relative w-full md:w-64">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search name, email, ID..."
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2 pl-9 pr-3 text-xs font-medium text-slate-800 outline-none focus:border-slate-400 focus:bg-white transition">
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <button type="submit" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition shrink-0">
                Search
            </button>
            @if($search)
                <a href="{{ route('admin.pending-registrations.index', ['filter' => $filter]) }}" class="px-2 py-2 text-slate-400 hover:text-slate-600 font-bold text-xs shrink-0">
                    Clear
                </a>
            @endif
        </form>
    </div>

    {{-- TABLE CONTAINER CARD --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-bold text-sm text-slate-900">Student Applications</h2>
            <span class="text-xs text-slate-400 font-medium">Total: {{ $registrations->total() }}</span>
        </div>

        <div class="overflow-x-auto min-h-[240px]">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-3 px-6">Student</th>
                        <th class="py-3 px-6">Student ID</th>
                        <th class="py-3 px-6">Email</th>
                        <th class="py-3 px-6">Status</th>
                        <th class="py-3 px-6">Submitted Date</th>
                        <th class="py-3 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($registrations as $registration)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-slate-100 border border-slate-200 overflow-hidden flex items-center justify-center shrink-0">
                                        @if($registration->id_picture_front)
                                            <img src="{{ route('admin.pending-registrations.id-picture', [$registration, 'front']) }}" 
                                                 alt="{{ $registration->name }}"
                                                 class="w-full h-full object-cover"
                                                 onerror="this.onerror=null; this.parentElement.innerHTML='<span class=\'font-bold text-xs text-slate-600\'>{{ strtoupper(substr($registration->name, 0, 1)) }}</span>';">
                                        @else
                                            <span class="font-bold text-xs text-slate-600">{{ strtoupper(substr($registration->name, 0, 1)) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.pending-registrations.show', $registration) }}" 
                                           class="font-bold text-slate-900 hover:text-emerald-700 transition">
                                            {{ $registration->name }}
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="font-mono text-xs text-slate-700 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                                    {{ $registration->student_id_number ?? '—' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-slate-600">
                                {{ $registration->email }}
                            </td>
                            <td class="py-3.5 px-6">
                                @if($registration->registration_status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Pending Review
                                    </span>
                                @elseif($registration->registration_status === 'rejected')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-rose-50 text-rose-800 border border-rose-200">
                                        <svg class="w-3 h-3 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Rejected
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-emerald-700 font-semibold text-xs">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Approved
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6 text-slate-400 font-medium">
                                {{ $registration->created_at->format('M d, Y') }}
                            </td>
                            <td class="py-3.5 px-6 text-right whitespace-nowrap relative">
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
                                        
                                        <a href="{{ route('admin.pending-registrations.show', $registration) }}" 
                                           class="flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition text-left">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Review
                                        </a>

                                        @if($registration->registration_status === 'pending')
                                            <form action="{{ route('admin.pending-registrations.approve', $registration) }}" method="POST" onsubmit="return confirm('Approve registration for {{ addslashes($registration->name) }}?')">
                                                @csrf @method('PATCH')
                                                <button type="submit" 
                                                        class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-50 transition text-left">
                                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    Approve
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 text-xs">
                                No student registrations found matching your filter or search.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($registrations->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $registrations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
