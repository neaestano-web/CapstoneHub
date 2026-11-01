@extends('layouts.admin')
@section('title', 'Download Requests — JHCSC Capstone Portal')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

    .admin-download-req-body {
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

<div class="admin-download-req-body space-y-6 max-w-7xl mx-auto">
    {{-- HEADER SECTION --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-4 border-b border-slate-200/80">
        <div>
            <h1 style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 2rem;" class="font-extrabold text-[#2d2b56] tracking-tight leading-tight">
                Manuscript <span class="text-[#0c4233]">Download Requests</span>
            </h1>
            <p class="text-slate-500 text-xs font-medium mt-1">
                Review student requests to download capstone manuscripts and grant or deny access.
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

    {{-- FILTER TOOLBAR --}}
    <div class="p-4 bg-white rounded-3xl border border-slate-200/80 flex flex-col md:flex-row items-center justify-between gap-4 shadow-xs">
        <div class="flex items-center gap-2 flex-wrap w-full md:w-auto">
            <a href="{{ route('admin.download-requests.index', ['status' => 'all']) }}" 
               class="filter-pill-admin {{ $status === 'all' ? 'filter-pill-active' : 'filter-pill-inactive' }}">
                <span>📋 All Requests</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-200 text-slate-700">{{ $pendingCount + $approvedCount + $rejectedCount }}</span>
            </a>

            <a href="{{ route('admin.download-requests.index', ['status' => 'pending']) }}" 
               class="filter-pill-admin {{ $status === 'pending' ? 'filter-pill-active' : 'filter-pill-inactive' }}">
                <span>⏳ Pending</span>
                @if($pendingCount > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-400 text-amber-950">{{ $pendingCount }}</span>
                @endif
            </a>

            <a href="{{ route('admin.download-requests.index', ['status' => 'approved']) }}" 
               class="filter-pill-admin {{ $status === 'approved' ? 'filter-pill-active' : 'filter-pill-inactive' }}">
                <span>✅ Approved</span>
                @if($approvedCount > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-400 text-emerald-950">{{ $approvedCount }}</span>
                @endif
            </a>

            <a href="{{ route('admin.download-requests.index', ['status' => 'rejected']) }}" 
               class="filter-pill-admin {{ $status === 'rejected' ? 'filter-pill-active' : 'filter-pill-inactive' }}">
                <span>❌ Rejected</span>
                @if($rejectedCount > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-400 text-rose-950">{{ $rejectedCount }}</span>
                @endif
            </a>
        </div>

        <span class="text-xs font-bold text-slate-400 hidden md:inline">Showing {{ $requests->total() }} Records</span>
    </div>

    {{-- TABLE CONTAINER CARD --}}
    <div class="table-container-card">
        <div class="p-5 border-b border-slate-100 bg-slate-50/80 flex items-center justify-between">
            <h3 class="font-extrabold text-sm text-[#2d2b56] flex items-center gap-2">
                <span>📥</span> Download Access Requests
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200/80 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
                        <th class="py-4 px-6">Student</th>
                        <th class="py-4 px-6">Requested Manuscript</th>
                        <th class="py-4 px-6">Reason / Purpose</th>
                        <th class="py-4 px-6">Requested On</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-semibold">
                    @forelse($requests as $req)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-6">
                                <div class="space-y-1">
                                    <span class="font-extrabold text-sm text-[#2d2b56] block">{{ $req->user->name ?? 'N/A' }}</span>
                                    <span class="text-slate-500 block text-[11px]">{{ $req->user->email ?? '' }}</span>
                                    @if($req->user->student_id_number)
                                        <code class="inline-block px-2 py-0.5 bg-slate-100 text-[#0c4233] text-[10px] font-extrabold rounded-md">
                                            ID: {{ $req->user->student_id_number }}
                                        </code>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-6 max-w-xs">
                                <div class="space-y-1">
                                    <a href="{{ route('repository.show', $req->research) }}" target="_blank" class="font-extrabold text-[#0c4233] hover:text-[#5840bb] transition line-clamp-2 leading-snug">
                                        {{ $req->research->title }}
                                    </a>
                                    <span class="text-slate-400 text-[11px] block">Authors: {{ Str::limit($req->research->authors, 35) }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-6 max-w-xs text-slate-600 font-medium">
                                <p class="line-clamp-3 leading-relaxed">{{ $req->reason ? $req->reason : 'No specific purpose provided.' }}</p>
                            </td>
                            <td class="py-4 px-6 text-slate-500">
                                <span class="block font-bold text-slate-700">{{ $req->created_at->format('M d, Y') }}</span>
                                <span class="text-[10px] text-slate-400">{{ $req->created_at->format('h:i A') }}</span>
                            </td>
                            <td class="py-4 px-6">
                                @if($req->isPending())
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-amber-100 text-amber-900">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600 animate-ping"></span>
                                        Pending Review
                                    </span>
                                @elseif($req->isApproved())
                                    <div class="inline-flex flex-col items-start gap-1">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-900">
                                            ✓ Access Granted
                                        </span>
                                        @if($req->actionedBy)
                                            <span class="text-[10px] text-slate-400 font-bold px-1">Approved by {{ $req->actionedBy->name }}</span>
                                        @endif
                                    </div>
                                @else
                                    <div class="inline-flex flex-col items-start gap-1">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-rose-100 text-rose-900">
                                            ✕ Access Denied
                                        </span>
                                        @if($req->actionedBy)
                                            <span class="text-[10px] text-slate-400 font-bold px-1">Denied by {{ $req->actionedBy->name }}</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($req->isPending())
                                        <form action="{{ route('admin.download-requests.approve', $req) }}" method="POST" class="inline" onsubmit="return confirm('Grant full manuscript download access to {{ addslashes($req->user->name ?? 'student') }}?')">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="px-3.5 py-1.5 bg-[#0c4233] hover:bg-[#082d23] text-white rounded-full font-extrabold text-xs transition shadow-xs">
                                                ✓ Approve
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.download-requests.reject', $req) }}" method="POST" class="inline" onsubmit="return confirm('Deny download access for this request?')">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="px-3.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-full font-extrabold text-xs transition">
                                                ✕ Reject
                                            </button>
                                        </form>
                                    @elseif($req->isApproved())
                                        <form action="{{ route('admin.download-requests.reject', $req) }}" method="POST" class="inline" onsubmit="return confirm('Revoke download access for this student?')">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="px-3.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-full font-extrabold text-xs transition">
                                                Revoke Access
                                            </button>
                                        </form>
                                    @elseif($req->isRejected())
                                        <form action="{{ route('admin.download-requests.approve', $req) }}" method="POST" class="inline" onsubmit="return confirm('Grant download access to this student?')">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="px-3.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 rounded-full font-extrabold text-xs transition">
                                                Grant Access
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center space-y-3">
                                <div class="w-14 h-14 bg-emerald-50 text-[#0c4233] rounded-full flex items-center justify-center font-bold text-2xl mx-auto">
                                    📭
                                </div>
                                <h4 class="font-extrabold text-base text-[#2d2b56]">No Download Requests Found</h4>
                                <p class="text-xs text-slate-400 max-w-sm mx-auto">There are no download requests matching your selected filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
