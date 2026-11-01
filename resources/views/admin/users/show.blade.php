@extends('layouts.admin')
@section('title', $user->name . ' — User Profile')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

    .admin-user-show-body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: #2d2b56;
    }

    .user-hero-admin {
        background: linear-gradient(135deg, #0c4233 0%, #082d23 100%);
        border-radius: 28px;
        padding: 32px;
        color: #ffffff;
        box-shadow: 0 14px 40px rgba(12, 66, 51, 0.25);
    }

    .card-detail-section {
        background: #ffffff;
        border-radius: 24px;
        border: 1.5px solid #e2e8f0;
        padding: 28px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
    }
</style>

<div class="admin-user-show-body space-y-6 max-w-7xl mx-auto">
    {{-- HERO HEADER --}}
    <div class="user-hero-admin space-y-4">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 rounded-full font-extrabold text-xs text-white transition inline-flex items-center gap-2">
                ← Back to Users List
            </a>

            <a href="{{ route('admin.users.edit', $user) }}" class="px-4 py-2 bg-amber-400 text-amber-950 font-extrabold text-xs rounded-full shadow-xs transition">
                ✏️ Edit Account
            </a>
        </div>

        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-white/10 border border-white/20 text-white font-black text-2xl flex items-center justify-center shrink-0">
                {{ substr($user->name, 0, 1) }}
            </div>
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-extrabold text-white leading-tight">{{ $user->name }}</h1>
                    @if($user->role == 'admin')
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-400 text-amber-950 uppercase tracking-wider">Admin</span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-400 text-emerald-950 uppercase tracking-wider">Student</span>
                    @endif
                </div>
                <p class="text-xs text-emerald-100 font-medium">✉️ {{ $user->email }}</p>
            </div>
        </div>
    </div>

    {{-- METADATA GRID (2 COLUMNS) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="card-detail-section space-y-4">
            <h3 class="font-extrabold text-base text-[#2d2b56] flex items-center gap-2 border-b border-slate-100 pb-3">
                <span>👤</span> Account Metadata
            </h3>

            <div class="space-y-3 text-xs">
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-400 font-bold uppercase tracking-wider">Verification Status:</span>
                    <span class="font-extrabold text-emerald-700">
                        @if($user->email_verified_at)
                            ✓ Verified ({{ $user->email_verified_at->format('M d, Y') }})
                        @else
                            <span class="text-slate-400 font-semibold">Unverified</span>
                        @endif
                    </span>
                </div>

                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-400 font-bold uppercase tracking-wider">Member Since:</span>
                    <span class="font-bold text-slate-800">{{ $user->created_at->format('F d, Y g:i A') }}</span>
                </div>

                <div class="flex justify-between py-1">
                    <span class="text-slate-400 font-bold uppercase tracking-wider">Last Profile Update:</span>
                    <span class="font-bold text-slate-800">{{ $user->updated_at->format('F d, Y g:i A') }}</span>
                </div>
            </div>
        </div>

        <div class="card-detail-section space-y-4">
            <h3 class="font-extrabold text-base text-[#2d2b56] flex items-center gap-2 border-b border-slate-100 pb-3">
                <span>📊</span> Submissions Overview
            </h3>

            <div class="flex items-center justify-around py-4 bg-slate-50 rounded-2xl border border-slate-100 text-center">
                <div>
                    <span class="text-2xl font-black text-[#0c4233] block">{{ $researches->total() }}</span>
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Total Submissions</span>
                </div>
                <div class="w-px h-10 bg-slate-200"></div>
                <div>
                    <span class="text-2xl font-black text-purple-700 block">{{ $user->researches()->where('status', 'approved')->count() }}</span>
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Approved Papers</span>
                </div>
            </div>
        </div>
    </div>

    {{-- CAPSTONE PAPERS SUBMITTED BY THIS USER --}}
    <div class="card-detail-section space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-extrabold text-base text-[#2d2b56] flex items-center gap-2">
                <span>📚</span> Submitted Capstones by {{ $user->name }}
            </h3>
            <span class="text-xs font-bold text-slate-400">Total: {{ $researches->total() }} Papers</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200/80 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
                        <th class="py-4 px-6">Title</th>
                        <th class="py-4 px-6">Category</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-semibold">
                    @forelse($researches as $research)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-6 max-w-md">
                                <a href="{{ route('admin.researches.show', $research) }}" class="font-extrabold text-[#2d2b56] hover:text-[#5840bb] transition line-clamp-2 leading-snug">
                                    {{ $research->title }}
                                </a>
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-0.5 bg-purple-50 text-purple-800 text-[11px] font-extrabold rounded-full">
                                    {{ $research->category->name ?? 'Uncategorized' }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                @if($research->status == 'pending')
                                    <span class="px-2.5 py-0.5 bg-amber-100 text-amber-900 text-[11px] font-extrabold rounded-full">⏳ Pending</span>
                                @elseif($research->status == 'approved')
                                    <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-900 text-[11px] font-extrabold rounded-full">Approved</span>
                                @elseif($research->status == 'rejected')
                                    <span class="px-2.5 py-0.5 bg-rose-100 text-rose-900 text-[11px] font-extrabold rounded-full">✕ Rejected</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('admin.researches.show', $research) }}" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-[#2d2b56] rounded-full font-extrabold text-xs transition">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-slate-400 text-xs font-medium">
                                This user has not submitted any capstone manuscripts yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($researches->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $researches->links() }}
            </div>
        @endif
    </div>
</div>
@endsection