@extends('layouts.admin')
@section('title', 'Download Requests — JHCSC Capstone Portal')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto pb-12">
    {{-- HEADER SECTION --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Administration</span>
                <span class="text-slate-300">/</span>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Download Requests</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight mt-1">Download Requests</h1>
            <p class="text-slate-500 text-xs font-medium mt-0.5">
                Review student requests to access and download complete capstone manuscripts.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-slate-500 bg-slate-100 border border-slate-200 px-3 py-1.5 rounded-xl">
                Total: {{ $requests->total() }} {{ $requests->total() === 1 ? 'Request' : 'Requests' }}
            </span>
        </div>
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

    {{-- FILTER TOOLBAR --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-1.5 flex-wrap w-full md:w-auto">
            <a href="{{ route('admin.download-requests.index', ['status' => 'all']) }}" 
               class="px-3 py-1.5 rounded-lg text-xs font-bold transition inline-flex items-center gap-1.5 {{ ($status ?? 'all') === 'all' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                <span>All Requests</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-extrabold {{ ($status ?? 'all') === 'all' ? 'bg-white text-slate-900' : 'bg-slate-100 text-slate-700' }}">
                    {{ $pendingCount + $approvedCount + $rejectedCount }}
                </span>
            </a>

            <a href="{{ route('admin.download-requests.index', ['status' => 'pending']) }}" 
               class="px-3 py-1.5 rounded-lg text-xs font-bold transition inline-flex items-center gap-1.5 {{ ($status ?? '') === 'pending' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                <span>Pending</span>
                @if($pendingCount > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-extrabold {{ ($status ?? '') === 'pending' ? 'bg-amber-400 text-amber-950' : 'bg-amber-100 text-amber-800' }}">
                        {{ $pendingCount }}
                    </span>
                @endif
            </a>

            <a href="{{ route('admin.download-requests.index', ['status' => 'approved']) }}" 
               class="px-3 py-1.5 rounded-lg text-xs font-bold transition inline-flex items-center gap-1.5 {{ ($status ?? '') === 'approved' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                <span>Approved</span>
                @if($approvedCount > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-extrabold {{ ($status ?? '') === 'approved' ? 'bg-white text-slate-900' : 'bg-emerald-100 text-emerald-800' }}">
                        {{ $approvedCount }}
                    </span>
                @endif
            </a>

            <a href="{{ route('admin.download-requests.index', ['status' => 'rejected']) }}" 
               class="px-3 py-1.5 rounded-lg text-xs font-bold transition inline-flex items-center gap-1.5 {{ ($status ?? '') === 'rejected' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                <span>Rejected</span>
                @if($rejectedCount > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-extrabold {{ ($status ?? '') === 'rejected' ? 'bg-white text-slate-900' : 'bg-rose-100 text-rose-800' }}">
                        {{ $rejectedCount }}
                    </span>
                @endif
            </a>
        </div>
    </div>

    {{-- TABLE CONTAINER CARD --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-bold text-sm text-slate-900">Access Requests List</h2>
            <span class="text-xs text-slate-400 font-medium">Total: {{ $requests->total() }}</span>
        </div>

        <div class="overflow-x-auto min-h-[240px]">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-3 px-6">Student</th>
                        <th class="py-3 px-6">Requested Manuscript</th>
                        <th class="py-3 px-6">Purpose / Reason</th>
                        <th class="py-3 px-6">Date</th>
                        <th class="py-3 px-6">Status</th>
                        <th class="py-3 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($requests as $req)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            {{-- Student --}}
                            <td class="py-3.5 px-6">
                                <div class="space-y-0.5">
                                    <span class="font-bold text-slate-900 block">{{ $req->user->name ?? 'N/A' }}</span>
                                    <span class="text-slate-500 block text-[11px]">{{ $req->user->email ?? '' }}</span>
                                    @if($req->user && $req->user->student_id_number)
                                        <span class="inline-block font-mono text-[10px] text-slate-600 bg-slate-100 px-1.5 py-0.2 rounded border border-slate-200">
                                            ID: {{ $req->user->student_id_number }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Requested Manuscript --}}
                            <td class="py-3.5 px-6 max-w-xs">
                                <div class="space-y-0.5">
                                    @if($req->research)
                                        <a href="{{ route('repository.show', $req->research) }}" target="_blank" 
                                           class="font-bold text-slate-900 hover:text-emerald-700 transition line-clamp-2 leading-snug">
                                            {{ $req->research->title }}
                                        </a>
                                        <span class="text-slate-400 text-[11px] block truncate">
                                            Authors: {{ $req->research->authors }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 italic">Research unavailable</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Purpose / Reason --}}
                            <td class="py-3.5 px-6 max-w-xs text-slate-600">
                                <p class="line-clamp-2 leading-relaxed text-[11px]">
                                    {{ $req->reason ? $req->reason : 'No specific purpose provided.' }}
                                </p>
                            </td>

                            {{-- Date --}}
                            <td class="py-3.5 px-6 text-slate-400 font-medium whitespace-nowrap">
                                <span class="block text-slate-700">{{ $req->created_at->format('M d, Y') }}</span>
                                <span class="text-[10px] text-slate-400">{{ $req->created_at->format('h:i A') }}</span>
                            </td>

                            {{-- Status --}}
                            <td class="py-3.5 px-6">
                                @if($req->isPending())
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Pending Review
                                    </span>
                                @elseif($req->isApproved())
                                    <div class="inline-flex flex-col items-start gap-0.5">
                                        <span class="inline-flex items-center gap-1 text-emerald-700 font-semibold text-xs">
                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Access Granted
                                        </span>
                                        @if($req->actionedBy)
                                            <span class="text-[10px] text-slate-400 font-medium">by {{ $req->actionedBy->name }}</span>
                                        @endif
                                    </div>
                                @else
                                    <div class="inline-flex flex-col items-start gap-0.5">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-rose-50 text-rose-800 border border-rose-200">
                                            <svg class="w-3 h-3 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            Access Denied
                                        </span>
                                        @if($req->actionedBy)
                                            <span class="text-[10px] text-slate-400 font-medium">by {{ $req->actionedBy->name }}</span>
                                        @endif
                                    </div>
                                @endif
                            </td>

                            {{-- Actions --}}
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
                                         class="absolute right-0 mt-1 w-40 bg-white rounded-xl shadow-lg border border-slate-200 py-1 z-50 focus:outline-none"
                                         style="display: none;">
                                        
                                        @if($req->research)
                                            <a href="{{ route('repository.show', $req->research) }}" target="_blank" 
                                               class="flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition text-left">
                                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                View Manuscript
                                            </a>
                                        @endif

                                        @if($req->isPending())
                                            <form action="{{ route('admin.download-requests.approve', $req) }}" method="POST" onsubmit="return confirm('Grant full manuscript download access to {{ addslashes($req->user->name ?? 'student') }}?')">
                                                @csrf @method('PATCH')
                                                <button type="submit" 
                                                        class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-50 transition text-left">
                                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    Approve
                                                </button>
                                            </form>

                                            <form action="{{ route('admin.download-requests.reject', $req) }}" method="POST" onsubmit="return confirm('Deny download access for this request?')">
                                                @csrf @method('PATCH')
                                                <button type="submit" 
                                                        class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition text-left">
                                                    <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                    Reject
                                                </button>
                                            </form>
                                        @elseif($req->isApproved())
                                            <form action="{{ route('admin.download-requests.reject', $req) }}" method="POST" onsubmit="return confirm('Revoke download access for this student?')">
                                                @csrf @method('PATCH')
                                                <button type="submit" 
                                                        class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition text-left">
                                                    <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                    </svg>
                                                    Revoke Access
                                                </button>
                                            </form>
                                        @elseif($req->isRejected())
                                            <form action="{{ route('admin.download-requests.approve', $req) }}" method="POST" onsubmit="return confirm('Grant download access to this student?')">
                                                @csrf @method('PATCH')
                                                <button type="submit" 
                                                        class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-50 transition text-left">
                                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    Grant Access
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
                                No download requests found matching your selected filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
