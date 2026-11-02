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

    .modal-backdrop-custom {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
        z-index: 1000;
        display: none;
        align-items: center;
        justify-content: center;
    }

    .modal-backdrop-custom.active {
        display: flex;
    }
</style>

<div class="admin-user-show-body space-y-6 max-w-7xl mx-auto">
    {{-- HERO HEADER --}}
    <div class="user-hero-admin space-y-4">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 rounded-full font-extrabold text-xs text-white transition inline-flex items-center gap-2">
                ← Back to Users List
            </a>

            <div class="flex items-center gap-2">
                @if($user->role === 'student')
                    @if($user->registration_status === 'pending')
                        <span class="px-3 py-1 rounded-full text-xs font-black bg-amber-400 text-amber-950 uppercase tracking-wider inline-flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-950 animate-ping"></span>
                            ⏳ Pending Registration
                        </span>
                    @elseif($user->registration_status === 'approved')
                        <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-400 text-emerald-950 uppercase tracking-wider">
                            ✓ Approved Student
                        </span>
                    @elseif($user->registration_status === 'rejected')
                        <span class="px-3 py-1 rounded-full text-xs font-black bg-rose-400 text-rose-950 uppercase tracking-wider">
                            ✕ Registration Rejected
                        </span>
                    @endif
                @endif

                <a href="{{ route('admin.users.edit', $user) }}" class="px-4 py-2 bg-amber-400 text-amber-950 font-extrabold text-xs rounded-full shadow-xs transition">
                    ✏️ Edit Account
                </a>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-white/10 border border-white/20 text-white font-black text-2xl flex items-center justify-center shrink-0">
                {{ substr($user->name, 0, 1) }}
            </div>
            <div class="space-y-1">
                <div class="flex items-center gap-2 flex-wrap">
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

    {{-- APPROVAL ACTION BAR FOR PENDING / REJECTED STUDENTS --}}
    @if($user->role === 'student' && $user->registration_status !== 'approved')
        <div class="p-5 bg-white rounded-3xl border-2 border-amber-300 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="space-y-1 text-center md:text-left">
                <div class="flex items-center justify-center md:justify-start gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-900 uppercase tracking-wider">
                        Action Required
                    </span>
                    <span class="text-xs font-extrabold text-slate-700">
                        {{ $user->registration_status === 'pending' ? 'Pending Registration Review' : 'Registration Rejected' }}
                    </span>
                </div>
                <p class="text-xs text-slate-500">
                    {{ $user->registration_status === 'pending' 
                        ? 'This student cannot submit or download manuscripts until an administrator approves their application.' 
                        : 'This student application was rejected. You can re-approve their access below.' }}
                </p>
            </div>

            <div class="flex items-center gap-3 w-full md:w-auto justify-end">
                <form action="{{ route('admin.pending-registrations.approve', $user) }}" method="POST" class="inline" onsubmit="return confirm('Approve registration for {{ addslashes($user->name) }} and send confirmation email?')">
                    @csrf @method('PATCH')
                    <button type="submit" class="px-6 py-2.5 bg-[#0c4233] hover:bg-[#082d23] text-white font-extrabold text-xs rounded-full shadow-xs transition">
                        ✓ Approve & Send Email
                    </button>
                </form>

                @if($user->registration_status === 'pending')
                    <button type="button" onclick="openRejectModal()" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-full shadow-xs transition">
                        ✕ Reject Application
                    </button>
                @endif
            </div>
        </div>
    @endif

    {{-- METADATA GRID (2 COLUMNS) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="card-detail-section space-y-4">
            <h3 class="font-extrabold text-base text-[#2d2b56] flex items-center gap-2 border-b border-slate-100 pb-3">
                <span>👤</span> Account & Verification Metadata
            </h3>

            <div class="space-y-3 text-xs">
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-400 font-bold uppercase tracking-wider">Email OTP Status:</span>
                    <span class="font-extrabold">
                        @if($user->email_verified_at)
                            <span class="text-emerald-700">✓ Email Verified via OTP ({{ $user->email_verified_at->format('M d, Y') }})</span>
                        @else
                            <span class="text-amber-600 font-bold">Pending OTP Verification</span>
                        @endif
                    </span>
                </div>

                @if($user->role === 'student')
                    <div class="flex justify-between py-1 border-b border-slate-50">
                        <span class="text-slate-400 font-bold uppercase tracking-wider">Student ID Number:</span>
                        <span class="font-extrabold text-slate-800">
                            <code>{{ $user->student_id_number ?? 'Not provided' }}</code>
                        </span>
                    </div>

                    <div class="flex justify-between py-1 border-b border-slate-50 items-center">
                        <span class="text-slate-400 font-bold uppercase tracking-wider">Registration Status:</span>
                        <span>
                            @if($user->registration_status === 'pending')
                                <span class="px-2.5 py-1 rounded-full text-xs font-black bg-amber-100 text-amber-900 inline-flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600 animate-ping"></span>
                                    ⏳ Pending Admin Review
                                </span>
                            @elseif($user->registration_status === 'approved')
                                <span class="px-2.5 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-900">
                                    ✅ Approved (Full Access)
                                </span>
                            @elseif($user->registration_status === 'rejected')
                                <span class="px-2.5 py-1 rounded-full text-xs font-black bg-rose-100 text-rose-900">
                                    ✕ Rejected
                                </span>
                            @endif
                        </span>
                    </div>

                    @if($user->registration_status === 'rejected' && $user->registration_rejection_reason)
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-400 font-bold uppercase tracking-wider">Rejection Reason:</span>
                            <span class="font-bold text-rose-700 text-right max-w-xs">
                                {{ $user->registration_rejection_reason }}
                            </span>
                        </div>
                    @endif
                @endif

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

    {{-- STUDENT ID PICTURES (IF STUDENT) --}}
    @if($user->role === 'student' && ($user->id_picture_front || $user->id_picture_back))
        <div class="card-detail-section space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-extrabold text-base text-[#2d2b56] flex items-center gap-2">
                    <span>🪪</span> Uploaded Student ID Photos
                </h3>
                <a href="{{ route('admin.pending-registrations.show', $user) }}" class="text-xs font-bold text-emerald-700 hover:underline">
                    View in Verification Reviewer →
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Front ID --}}
                <div class="space-y-2 p-3 bg-slate-50 rounded-2xl border border-slate-100">
                    <span class="text-xs font-extrabold text-slate-600 block">ID Front Side</span>
                    @if($user->id_picture_front)
                        <img src="{{ route('admin.pending-registrations.id-picture', [$user, 'front']) }}" 
                             alt="Student ID Front" 
                             class="w-full h-48 object-cover rounded-xl border border-slate-200 shadow-xs cursor-pointer hover:opacity-95 transition"
                             onclick="window.open(this.src, '_blank')">
                    @else
                        <div class="h-48 flex items-center justify-center text-xs text-slate-400 font-bold bg-slate-100 rounded-xl">
                            No front photo uploaded
                        </div>
                    @endif
                </div>

                {{-- Back ID --}}
                <div class="space-y-2 p-3 bg-slate-50 rounded-2xl border border-slate-100">
                    <span class="text-xs font-extrabold text-slate-600 block">ID Back Side</span>
                    @if($user->id_picture_back)
                        <img src="{{ route('admin.pending-registrations.id-picture', [$user, 'back']) }}" 
                             alt="Student ID Back" 
                             class="w-full h-48 object-cover rounded-xl border border-slate-200 shadow-xs cursor-pointer hover:opacity-95 transition"
                             onclick="window.open(this.src, '_blank')">
                    @else
                        <div class="h-48 flex items-center justify-center text-xs text-slate-400 font-bold bg-slate-100 rounded-xl">
                            No back photo uploaded
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

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

{{-- REJECTION MODAL --}}
<div id="rejectModal" class="modal-backdrop-custom">
    <div class="bg-white rounded-3xl p-6 max-w-lg w-full mx-4 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-extrabold text-base text-[#2d2b56] flex items-center gap-2">
                <span>✕</span> Reject Student Registration
            </h3>
            <button type="button" onclick="closeRejectModal()" class="text-slate-400 hover:text-slate-700 text-lg font-black">✕</button>
        </div>

        <form action="{{ route('admin.pending-registrations.reject', $user) }}" method="POST" class="space-y-4">
            @csrf @method('PATCH')
            <div>
                <label for="rejection_reason" class="block text-xs font-extrabold text-slate-600 uppercase tracking-wider mb-2">
                    Reason for Rejection *
                </label>
                <textarea name="rejection_reason" id="rejection_reason" rows="4" required
                          placeholder="Explain why the registration was rejected (e.g. blurry ID photo, invalid student ID number, not currently enrolled)..."
                          class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-3.5 text-xs font-semibold text-[#2d2b56] outline-none focus:border-rose-500 focus:bg-white transition"></textarea>
                <p class="text-[11px] text-slate-400 mt-1">This explanation will be emailed directly to the student's Gmail account.</p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-full font-bold text-xs transition">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-full font-extrabold text-xs transition shadow-xs">
                    Confirm Rejection & Send Email
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRejectModal() {
        document.getElementById('rejectModal').classList.add('active');
    }
    function closeRejectModal() {
        document.getElementById('rejectModal').classList.remove('active');
    }
</script>
@endsection
