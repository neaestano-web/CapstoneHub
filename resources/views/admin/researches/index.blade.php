@extends('layouts.admin')
@section('title', 'Manage Researches — JHCSC Capstone Portal')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto pb-12">
    {{-- HEADER SECTION --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Administration</span>
                <span class="text-slate-300">/</span>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Researches</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight mt-1">Manage Researches</h1>
            <p class="text-slate-500 text-xs font-medium mt-0.5">
                Review, approve, or reject student capstone submissions for the institutional catalog.
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('admin.researches.create') }}" 
               class="px-4 py-2 bg-[#0c4233] hover:bg-[#082d23] text-white font-bold text-xs rounded-xl transition shadow-xs inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Upload Capstone
            </a>
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

    {{-- FILTER & SEARCH TOOLBAR --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 flex flex-col md:flex-row items-center justify-between gap-4">
        {{-- Status Filter Tabs --}}
        <div class="flex items-center gap-1.5 flex-wrap w-full md:w-auto">
            <a href="{{ route('admin.researches.index', array_filter(['search' => request('search')])) }}" 
               class="px-3 py-1.5 rounded-lg text-xs font-bold transition inline-flex items-center gap-1.5 {{ !request('status') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                <span>All</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-extrabold {{ !request('status') ? 'bg-white text-slate-900' : 'bg-slate-100 text-slate-700' }}">
                    {{ $counts['all'] ?? $researches->total() }}
                </span>
            </a>

            <a href="{{ route('admin.researches.index', array_filter(['status' => 'pending', 'search' => request('search')])) }}" 
               class="px-3 py-1.5 rounded-lg text-xs font-bold transition inline-flex items-center gap-1.5 {{ request('status') === 'pending' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                <span>Pending</span>
                @if(($counts['pending'] ?? 0) > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-extrabold {{ request('status') === 'pending' ? 'bg-amber-400 text-amber-950' : 'bg-amber-100 text-amber-800' }}">
                        {{ $counts['pending'] }}
                    </span>
                @endif
            </a>

            <a href="{{ route('admin.researches.index', array_filter(['status' => 'approved', 'search' => request('search')])) }}" 
               class="px-3 py-1.5 rounded-lg text-xs font-bold transition inline-flex items-center gap-1.5 {{ request('status') === 'approved' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                <span>Approved</span>
                @if(($counts['approved'] ?? 0) > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-extrabold {{ request('status') === 'approved' ? 'bg-white text-slate-900' : 'bg-emerald-100 text-emerald-800' }}">
                        {{ $counts['approved'] }}
                    </span>
                @endif
            </a>

            <a href="{{ route('admin.researches.index', array_filter(['status' => 'rejected', 'search' => request('search')])) }}" 
               class="px-3 py-1.5 rounded-lg text-xs font-bold transition inline-flex items-center gap-1.5 {{ request('status') === 'rejected' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                <span>Rejected</span>
                @if(($counts['rejected'] ?? 0) > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-extrabold {{ request('status') === 'rejected' ? 'bg-white text-slate-900' : 'bg-rose-100 text-rose-800' }}">
                        {{ $counts['rejected'] }}
                    </span>
                @endif
            </a>
        </div>

        {{-- Search Form --}}
        <form method="GET" action="{{ route('admin.researches.index') }}" class="flex items-center gap-2 w-full md:w-auto">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="relative w-full md:w-64">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search title, author, keyword..."
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2 pl-9 pr-3 text-xs font-medium text-slate-800 outline-none focus:border-slate-400 focus:bg-white transition">
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <button type="submit" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition shrink-0">
                Search
            </button>
            @if(request('search'))
                <a href="{{ route('admin.researches.index', array_filter(['status' => request('status')])) }}" class="px-2 py-2 text-slate-400 hover:text-slate-600 font-bold text-xs shrink-0">
                    Clear
                </a>
            @endif
        </form>
    </div>

    {{-- TABLE CONTAINER CARD --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-bold text-sm text-slate-900">Manuscripts List</h2>
            <span class="text-xs text-slate-400 font-medium">Total: {{ $researches->total() }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-3 px-6 w-2/5">Manuscript Title</th>
                        <th class="py-3 px-6">Authors</th>
                        <th class="py-3 px-6">Category</th>
                        <th class="py-3 px-6">Status</th>
                        <th class="py-3 px-6">Submitted Date</th>
                        <th class="py-3 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($researches as $research)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            {{-- Manuscript Title --}}
                            <td class="py-3.5 px-6 max-w-md">
                                <a href="{{ route('admin.researches.show', $research) }}" 
                                   class="font-bold text-slate-900 hover:text-emerald-700 transition leading-snug line-clamp-2 block">
                                    {{ $research->title }}
                                </a>
                            </td>

                            {{-- Authors --}}
                            <td class="py-3.5 px-6">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-slate-100 text-slate-700 font-bold text-[10px] flex items-center justify-center shrink-0 border border-slate-200">
                                        {{ strtoupper(substr($research->authors ?? 'R', 0, 1)) }}
                                    </div>
                                    <span class="font-medium text-slate-700 truncate max-w-[150px]" title="{{ $research->authors }}">
                                        {{ $research->authors ?? 'Researchers' }}
                                    </span>
                                </div>
                            </td>

                            {{-- Category --}}
                            <td class="py-3.5 px-6">
                                @if($research->category)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/60 truncate max-w-[140px]" title="{{ $research->category->name }}">
                                        {{ $research->category->name }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-500">
                                        Uncategorized
                                    </span>
                                @endif
                            </td>

                            {{-- Status Badge --}}
                            <td class="py-3.5 px-6">
                                @if($research->status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Pending
                                    </span>
                                @elseif($research->status === 'approved')
                                    <span class="inline-flex items-center gap-1 text-emerald-700 font-semibold text-xs">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Approved
                                    </span>
                                @elseif($research->status === 'rejected')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-rose-50 text-rose-800 border border-rose-200">
                                        <svg class="w-3 h-3 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Rejected
                                    </span>
                                @else
                                    <span class="text-slate-500 text-xs">{{ ucfirst($research->status) }}</span>
                                @endif
                            </td>

                            {{-- Submitted Date --}}
                            <td class="py-3.5 px-6 text-slate-400 font-medium whitespace-nowrap">
                                {{ $research->created_at ? $research->created_at->format('M d, Y') : '—' }}
                            </td>

                            {{-- Actions --}}
                            <td class="py-3.5 px-6 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.researches.show', $research) }}" 
                                       class="px-2.5 py-1 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg font-semibold text-xs transition">
                                        View
                                    </a>

                                    @if($research->status === 'pending')
                                        <form action="{{ route('admin.researches.approve', $research) }}" method="POST" class="inline" onsubmit="return confirm('Approve this research for inclusion in the repository?')">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="px-2.5 py-1 text-emerald-700 hover:text-emerald-900 hover:bg-emerald-50 border border-emerald-200 rounded-lg font-bold text-xs transition">
                                                Approve
                                            </button>
                                        </form>

                                        <button type="button" 
                                                onclick="openRejectModal({{ $research->id }}, '{{ addslashes($research->title) }}')" 
                                                class="px-2.5 py-1 text-amber-700 hover:text-amber-900 hover:bg-amber-50 border border-amber-200 rounded-lg font-bold text-xs transition">
                                            Reject
                                        </button>
                                    @endif

                                    <form action="{{ route('admin.researches.destroy', $research) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to permanently delete this research?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="px-2 py-1 text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded-lg font-semibold text-xs transition">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 text-xs">
                                No capstone manuscripts found matching your filter or search.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($researches->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $researches->links() }}
            </div>
        @endif
    </div>
</div>

{{-- REJECT MODAL --}}
<div id="rejectModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs hidden items-center justify-center p-4 z-50">
    <div class="bg-white rounded-2xl p-6 max-w-lg w-full shadow-2xl border border-slate-200 space-y-4" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-bold text-sm text-slate-900">Reject Research Submission</h3>
            <button type="button" onclick="closeRejectModal()" class="text-slate-400 hover:text-slate-700 font-bold text-base">✕</button>
        </div>

        <form id="rejectForm" method="POST" class="space-y-4">
            @csrf @method('PATCH')
            <div>
                <p class="text-[11px] text-slate-500 font-semibold mb-1">Target Research Paper:</p>
                <p id="researchTitle" class="text-xs font-bold text-slate-800 bg-slate-50 p-2.5 rounded-lg border border-slate-200 leading-relaxed"></p>
            </div>

            <div>
                <label for="rejection_reason" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Rejection Feedback / Reason *
                </label>
                <textarea
                    name="rejection_reason"
                    id="rejection_reason"
                    rows="3"
                    required
                    placeholder="Provide specific feedback or required revisions for the student researcher..."
                    class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-xs font-medium text-slate-900 outline-none focus:border-[#0c4233] focus:bg-white transition"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-1">
                <button type="button" onclick="closeRejectModal()" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-lg transition">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-lg shadow-xs transition">
                    Confirm Rejection
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openRejectModal(id, title) {
    document.getElementById('researchTitle').textContent = title;
    document.getElementById('rejectForm').action = '{{ url("admin/researches") }}/' + id + '/reject';
    const modal = document.getElementById('rejectModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.getElementById('rejection_reason').value = '';
}
</script>
@endsection
