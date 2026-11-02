@extends('layouts.admin')
@section('title', 'Pending Registrations — JHCSC Capstone Portal')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

    .admin-pending-body {
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

    .filter-pill-admin {
        padding: 8px 18px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 700;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .filter-pill-active {
        background: #0c4233;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(12, 66, 51, 0.25);
    }

    .filter-pill-inactive {
        background: #f1f5f9;
        color: #64748b;
    }

    .filter-pill-inactive:hover {
        background: #e2e8f0;
        color: #2d2b56;
    }
</style>

<div class="admin-pending-body space-y-6 max-w-7xl mx-auto">
    {{-- HEADER SECTION --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-4 border-b border-slate-200/80">
        <div>
            <h1 style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 2rem;" class="font-extrabold text-[#2d2b56] tracking-tight leading-tight">
                Pending <span class="text-[#0c4233]">Registrations</span>
            </h1>
            <p class="text-slate-500 text-xs font-medium mt-1">
                Review, verify student identity documents, and approve registration access.
            </p>
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

    {{-- FILTER & SEARCH TOOLBAR --}}
    <div class="p-4 bg-white rounded-3xl border border-slate-200/80 flex flex-col md:flex-row items-center justify-between gap-4 shadow-xs">
        <div class="flex items-center gap-2 flex-wrap w-full md:w-auto">
            @php $pendingRegCount = \App\Models\User::where('role','student')->whereNotNull('email_verified_at')->where('registration_status','pending')->count(); @endphp
            <a href="{{ route('admin.pending-registrations.index', ['filter' => 'pending']) }}" 
               class="filter-pill-admin {{ $filter === 'pending' ? 'filter-pill-active' : 'filter-pill-inactive' }}">
                <span>⏳ Pending</span>
                @if($pendingRegCount > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-400 text-amber-950">{{ $pendingRegCount }}</span>
                @endif
            </a>

            @php $rejectedRegCount = \App\Models\User::where('role','student')->whereNotNull('email_verified_at')->where('registration_status','rejected')->count(); @endphp
            <a href="{{ route('admin.pending-registrations.index', ['filter' => 'rejected']) }}" 
               class="filter-pill-admin {{ $filter === 'rejected' ? 'filter-pill-active' : 'filter-pill-inactive' }}">
                <span>❌ Rejected</span>
                @if($rejectedRegCount > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-400 text-rose-950">{{ $rejectedRegCount }}</span>
                @endif
            </a>

            <a href="{{ route('admin.pending-registrations.index', ['filter' => 'all']) }}" 
               class="filter-pill-admin {{ $filter === 'all' ? 'filter-pill-active' : 'filter-pill-inactive' }}">
                <span>📋 All Registrations</span>
            </a>
        </div>

        <form method="GET" action="{{ route('admin.pending-registrations.index') }}" class="flex items-center gap-2 w-full md:w-auto">
            <input type="hidden" name="filter" value="{{ $filter }}">
            <div class="relative w-full md:w-64">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search name, email, or ID..."
                       class="w-full bg-slate-50 border border-slate-200 rounded-full py-2 pl-9 pr-4 text-xs font-semibold text-[#2d2b56] outline-none focus:border-[#0c4233] focus:bg-white transition">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs">🔍</span>
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-[#2d2b56] font-extrabold text-xs rounded-full transition shrink-0">
                Search
            </button>
        </form>
    </div>

    {{-- TABLE CONTAINER CARD --}}
    <div class="table-container-card">
        <div class="p-5 border-b border-slate-100 bg-slate-50/80 flex items-center justify-between">
            <h3 class="font-extrabold text-sm text-[#2d2b56] flex items-center gap-2">
                <span>🎓</span> Student Registration Applications
            </h3>
            <span class="text-xs font-bold text-slate-400">Total: {{ $registrations->total() }} Applications</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200/80 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
                        <th class="py-4 px-6">Student</th>
                        <th class="py-4 px-6">Student ID Number</th>
                        <th class="py-4 px-6">Email Address</th>
                        <th class="py-4 px-6">Registration Status</th>
                        <th class="py-4 px-6">Submitted Date</th>
                        <th class="py-4 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-semibold">
                    @forelse($registrations as $registration)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    @if($registration->id_picture_front)
                                        <img src="{{ route('admin.pending-registrations.id-picture', [$registration, 'front']) }}" alt="Student ID"
                                             class="w-9 h-9 rounded-xl object-cover border border-slate-200 shrink-0"
                                             onerror="this.onerror=null; this.src='{{ asset('storage/' . $registration->id_picture_front) }}';">
                                    @else
                                        <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-900 font-black text-xs flex items-center justify-center shrink-0">
                                            {{ substr($registration->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <a href="{{ route('admin.pending-registrations.show', $registration) }}" class="font-extrabold text-sm text-[#2d2b56] hover:text-[#5840bb] transition">
                                        {{ $registration->name }}
                                    </a>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <code class="px-3 py-1 bg-slate-100 text-slate-800 rounded-lg text-xs font-extrabold">
                                    {{ $registration->student_id_number ?? 'Not provided' }}
                                </code>
                            </td>
                            <td class="py-4 px-6 text-slate-600 font-bold">
                                {{ $registration->email }}
                            </td>
                            <td class="py-4 px-6">
                                @if($registration->registration_status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-amber-100 text-amber-900">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600 animate-ping"></span>
                                        Pending Review
                                    </span>
                                @elseif($registration->registration_status === 'rejected')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-rose-100 text-rose-900">
                                        ✕ Rejected
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-900">
                                        Approved
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-slate-500">
                                {{ $registration->created_at->format('M d, Y') }}
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.pending-registrations.show', $registration) }}" class="px-4 py-1.5 bg-[#0c4233] hover:bg-[#082d23] text-white rounded-full font-extrabold text-xs transition shadow-xs">
                                        👁 Review Verification
                                    </a>
                                    @if($registration->registration_status === 'pending')
                                        <form action="{{ route('admin.pending-registrations.approve', $registration) }}" method="POST" class="inline" onsubmit="return confirm('Approve registration for {{ addslashes($registration->name) }}?')">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="px-3.5 py-1.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-900 rounded-full font-extrabold text-xs transition">
                                                ✓ Approve
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center space-y-3">
                                <div class="w-14 h-14 bg-amber-50 text-amber-800 rounded-full flex items-center justify-center font-bold text-2xl mx-auto">
                                    🎉
                                </div>
                                <h4 class="font-extrabold text-base text-[#2d2b56]">No {{ $filter === 'all' ? '' : ucfirst($filter) }} Registrations</h4>
                                <p class="text-xs text-slate-400 max-w-sm mx-auto">All student registration requests in this view have been processed.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($registrations->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $registrations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
