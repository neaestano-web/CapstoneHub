@extends('layouts.admin')
@section('title', $user->name . ' — User Profile')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    .admin-user-show-wrap {
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        color: #0f172a;
    }

    .modal-backdrop-custom {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(4px);
        z-index: 1000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }

    .modal-backdrop-custom.active {
        display: flex;
    }
</style>

<div class="admin-user-show-wrap max-w-6xl mx-auto space-y-6">

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

    {{-- TOP NAVIGATION --}}
    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
        <a href="{{ route('admin.users.index') }}" 
           class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-slate-900 transition w-fit">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to User Accounts
        </a>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.users.edit', $user) }}" 
               class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
                Edit Account
            </a>
        </div>
    </div>

    {{-- USER PROFILE HEADER --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-800 font-extrabold text-xl flex items-center justify-center shrink-0 border border-slate-200">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $user->name }}</h1>
                    @if($user->role == 'admin')
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-800 border border-amber-200">Admin</span>
                    @else
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-600">Student</span>
                    @endif

                    @if($user->role === 'student')
                        @if($user->registration_status === 'pending')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                Pending Review
                            </span>
                        @elseif($user->registration_status === 'approved')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                ✓ Approved
                            </span>
                        @elseif($user->registration_status === 'rejected')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-800 border border-rose-200">
                                ✕ Rejected
                            </span>
                        @endif
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 mt-1 font-medium">
                    <span>{{ $user->email }}</span>
                    @if($user->student_id_number)
                        <span class="text-slate-300">•</span>
                        <span>ID: <strong class="text-slate-700 font-mono">{{ $user->student_id_number }}</strong></span>
                    @endif
                    <span class="text-slate-300">•</span>
                    <span>Joined {{ $user->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

        {{-- ACTION BUTTONS FOR PENDING STUDENTS --}}
        @if($user->role === 'student' && $user->registration_status !== 'approved')
            <div class="flex items-center gap-2 pt-2 md:pt-0">
                <form action="{{ route('admin.pending-registrations.approve', $user) }}" method="POST"
                      onsubmit="return confirm('Approve registration for {{ addslashes($user->name) }}?')">
                    @csrf @method('PATCH')
                    <button type="submit" 
                            class="px-4 py-2 bg-[#0c4233] hover:bg-[#082d23] text-white font-bold text-xs rounded-xl transition shadow-xs flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Approve Student
                    </button>
                </form>

                @if($user->registration_status === 'pending')
                    <button type="button" onclick="openRejectModal()"
                            class="px-3.5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs rounded-xl transition flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Reject
                    </button>
                @endif
            </div>
        @endif
    </div>

    {{-- METADATA GRID (2 COLUMNS) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Left: Account & Verification Details --}}
        <div class="bg-white rounded-2xl border border-slate-200/90 p-6 space-y-4">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-sm font-bold text-slate-900">Account & Verification</h2>
                <p class="text-[11px] text-slate-400">Institutional registration credentials</p>
            </div>

            <div class="space-y-3 text-xs">
                <div class="flex justify-between py-1.5 border-b border-slate-50">
                    <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Email OTP:</span>
                    <div>
                        @if($user->email_verified_at)
                            <span class="inline-flex items-center gap-1 text-emerald-700 font-bold">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Verified ({{ $user->email_verified_at->format('M d, Y') }})
                            </span>
                        @else
                            <span class="text-amber-700 font-bold">Pending OTP</span>
                        @endif
                    </div>
                </div>

                @if($user->role === 'student')
                    <div class="flex justify-between py-1.5 border-b border-slate-50">
                        <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Student ID Number:</span>
                        <code class="font-mono text-slate-800 font-bold">{{ $user->student_id_number ?? 'Not provided' }}</code>
                    </div>

                    <div class="flex justify-between py-1.5 border-b border-slate-50 items-center">
                        <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Registration Status:</span>
                        <div>
                            @if($user->registration_status === 'pending')
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                    Pending Review
                                </span>
                            @elseif($user->registration_status === 'approved')
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    Approved
                                </span>
                            @elseif($user->registration_status === 'rejected')
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-rose-50 text-rose-800 border border-rose-200">
                                    Rejected
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($user->registration_status === 'rejected' && $user->registration_rejection_reason)
                        <div class="py-1.5 border-b border-slate-50 space-y-1">
                            <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px] block">Rejection Feedback:</span>
                            <p class="text-rose-700 font-medium leading-relaxed">{{ $user->registration_rejection_reason }}</p>
                        </div>
                    @endif
                @endif

                <div class="flex justify-between py-1.5 border-b border-slate-50">
                    <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Registered On:</span>
                    <span class="text-slate-700 font-medium">{{ $user->created_at->format('F d, Y g:i A') }}</span>
                </div>

                <div class="flex justify-between py-1.5">
                    <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Last Profile Update:</span>
                    <span class="text-slate-700 font-medium">{{ $user->updated_at->format('F d, Y g:i A') }}</span>
                </div>
            </div>
        </div>

        {{-- Right: Submissions Overview --}}
        <div class="bg-white rounded-2xl border border-slate-200/90 p-6 space-y-4 flex flex-col justify-between">
            <div>
                <div class="border-b border-slate-100 pb-3">
                    <h2 class="text-sm font-bold text-slate-900">Research Activity</h2>
                    <p class="text-[11px] text-slate-400">Capstone papers and repository submissions</p>
                </div>

                <div class="grid grid-cols-2 gap-4 py-6">
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 text-center">
                        <div class="text-3xl font-extrabold text-slate-900">{{ $researches->total() }}</div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1">Total Submissions</div>
                    </div>
                    <div class="p-4 rounded-xl bg-emerald-50/50 border border-emerald-100 text-center">
                        <div class="text-3xl font-extrabold text-[#0c4233]">{{ $user->researches()->where('status', 'approved')->count() }}</div>
                        <div class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider mt-1">Approved Papers</div>
                    </div>
                </div>
            </div>

            <div class="text-center text-xs text-slate-400 border-t border-slate-100 pt-3">
                Account role: <strong class="text-slate-700 capitalize">{{ $user->role }}</strong>
            </div>
        </div>
    </div>

    {{-- STUDENT ID PICTURES (IF STUDENT AND UPLOADED) --}}
    @if($user->role === 'student' && ($user->id_picture_front || $user->id_picture_back))
        <div class="bg-white rounded-2xl border border-slate-200/90 p-6 space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Student ID Photos</h2>
                    <p class="text-[11px] text-slate-400">Institutional identification documents</p>
                </div>
                <a href="{{ route('admin.pending-registrations.show', $user) }}" class="text-xs font-bold text-[#0c4233] hover:underline">
                    Open Reviewer →
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Front ID --}}
                <div class="space-y-1.5">
                    <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Front Side</div>
                    @if($user->id_picture_front)
                        <div class="group relative rounded-xl border border-slate-200 bg-slate-50 overflow-hidden cursor-pointer hover:border-slate-400 transition"
                             onclick="window.open('{{ route('admin.pending-registrations.id-picture', [$user, 'front']) }}', '_blank')">
                            <img src="{{ route('admin.pending-registrations.id-picture', [$user, 'front']) }}" 
                                 alt="Student ID Front" 
                                 class="w-full h-48 object-contain p-1 group-hover:opacity-95 transition"
                                 loading="lazy">
                        </div>
                    @else
                        <div class="h-48 rounded-xl border border-dashed border-slate-200 bg-slate-50 flex items-center justify-center text-slate-400 text-xs">
                            No front photo uploaded
                        </div>
                    @endif
                </div>

                {{-- Back ID --}}
                <div class="space-y-1.5">
                    <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Back Side</div>
                    @if($user->id_picture_back)
                        <div class="group relative rounded-xl border border-slate-200 bg-slate-50 overflow-hidden cursor-pointer hover:border-slate-400 transition"
                             onclick="window.open('{{ route('admin.pending-registrations.id-picture', [$user, 'back']) }}', '_blank')">
                            <img src="{{ route('admin.pending-registrations.id-picture', [$user, 'back']) }}" 
                                 alt="Student ID Back" 
                                 class="w-full h-48 object-contain p-1 group-hover:opacity-95 transition"
                                 loading="lazy">
                        </div>
                    @else
                        <div class="h-48 rounded-xl border border-dashed border-slate-200 bg-slate-50 flex items-center justify-center text-slate-400 text-xs">
                            No back photo uploaded
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- CAPSTONE PAPERS SUBMITTED BY THIS USER --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="font-bold text-sm text-slate-900">Submitted Capstones</h2>
                <p class="text-[11px] text-slate-400">Papers submitted by {{ $user->name }}</p>
            </div>
            <span class="text-xs text-slate-400 font-medium">Total: {{ $researches->total() }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-3 px-6">Title</th>
                        <th class="py-3 px-6">Category</th>
                        <th class="py-3 px-6">Status</th>
                        <th class="py-3 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($researches as $research)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-6 max-w-md">
                                <a href="{{ route('admin.researches.show', $research) }}" class="font-bold text-slate-900 hover:text-[#0c4233] transition line-clamp-2 leading-snug">
                                    {{ $research->title }}
                                </a>
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-700 text-[11px] font-medium rounded-md">
                                    {{ $research->category->name ?? 'Uncategorized' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6">
                                @if($research->status == 'pending')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                        Pending
                                    </span>
                                @elseif($research->status == 'approved')
                                    <span class="inline-flex items-center gap-1 text-emerald-700 font-semibold text-xs">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Approved
                                    </span>
                                @elseif($research->status == 'rejected')
                                    <span class="inline-flex items-center gap-1 text-rose-700 font-semibold text-xs">
                                        <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Rejected
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                <a href="{{ route('admin.researches.show', $research) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-semibold text-xs transition">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-slate-400 text-xs">
                                This user has not submitted any capstone manuscripts yet.
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

{{-- REJECTION MODAL --}}
<div id="rejectModal" class="modal-backdrop-custom">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full shadow-2xl border border-slate-200 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-bold text-sm text-slate-900">Reject Student Registration</h3>
            <button type="button" onclick="closeRejectModal()" class="text-slate-400 hover:text-slate-600 font-bold text-base">✕</button>
        </div>

        <form action="{{ route('admin.pending-registrations.reject', $user) }}" method="POST" class="space-y-4">
            @csrf @method('PATCH')
            <p class="text-xs text-slate-500 leading-relaxed">
                Provide feedback explaining why <strong>{{ $user->name }}</strong>'s registration is being rejected. This will be sent to their email.
            </p>

            <div>
                <label for="rejection_reason" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Reason for Rejection *
                </label>
                <textarea name="rejection_reason" id="rejection_reason" rows="3" required
                          placeholder="e.g. Student ID picture is blurry or unreadable. Please register again with a clear photo."
                          class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs text-slate-800 outline-none focus:border-slate-400 focus:bg-white transition"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-1">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl transition shadow-xs">
                    Confirm Rejection
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
