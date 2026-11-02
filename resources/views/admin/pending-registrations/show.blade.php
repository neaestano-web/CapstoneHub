@extends('layouts.admin')
@section('title', 'Review: ' . ($user->name ?? 'Student') . ' — Admin Review')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    .admin-review-wrap {
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

<div class="admin-review-wrap max-w-6xl mx-auto space-y-6">

    {{-- ALERT NOTIFICATIONS --}}
    @if(session('success'))
        <div class="flex items-center gap-3 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-semibold">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center gap-3 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-semibold">
            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- TOP NAVIGATION & STATUS --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-200">
        <a href="{{ route('admin.pending-registrations.index') }}" 
           class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-slate-900 transition w-fit">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back
        </a>

        <div class="flex items-center gap-2">
            @if($user->registration_status === 'pending')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                    Pending Review
                </span>
            @elseif($user->registration_status === 'approved')
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Approved
                </span>
            @elseif($user->registration_status === 'rejected')
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-800 border border-rose-200">
                    <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Rejected
                </span>
            @endif
        </div>
    </div>

    {{-- HEADER WITH ACTIONS --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Student Application</div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $user->name }}</h1>
            <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 mt-1.5 font-medium">
                <span class="inline-flex items-center gap-1">
                    <span class="text-slate-400">ID:</span>
                    <strong class="text-slate-700 font-semibold">{{ $user->student_id_number ?? 'Not provided' }}</strong>
                </span>
                <span class="text-slate-300">•</span>
                <span>{{ $user->email }}</span>
                <span class="text-slate-300">•</span>
                <span>Submitted {{ $user->created_at->format('M d, Y') }}</span>
            </div>
        </div>

        {{-- ACTION BUTTONS --}}
        @if($user->registration_status !== 'approved')
            <div class="flex flex-wrap items-center gap-2 pt-2 md:pt-0">
                <form action="{{ route('admin.pending-registrations.approve', $user) }}" method="POST"
                      onsubmit="return confirm('Approve registration for {{ addslashes($user->name) }}?')">
                    @csrf @method('PATCH')
                    <button type="submit" 
                            class="px-5 py-2.5 bg-[#0c4233] hover:bg-[#082d23] text-white font-bold text-xs rounded-xl transition shadow-xs flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Approve
                    </button>
                </form>

                <button type="button" onclick="openRejectModal()"
                        class="px-4 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs rounded-xl transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Reject
                </button>
            </div>
        @else
            <div class="text-xs text-slate-400 font-medium bg-slate-50 border border-slate-100 px-4 py-2 rounded-xl">
                This account is active and verified.
            </div>
        @endif
    </div>

    {{-- REJECTION REASON NOTICE (IF REJECTED) --}}
    @if($user->registration_status === 'rejected' && $user->registration_rejection_reason)
        <div class="p-4 bg-rose-50/70 border border-rose-200/80 rounded-xl text-xs space-y-1">
            <span class="font-bold text-rose-900 uppercase tracking-wider text-[10px] block">Rejection Feedback Given:</span>
            <p class="text-rose-800 leading-relaxed font-medium">{{ $user->registration_rejection_reason }}</p>
        </div>
    @endif

    {{-- 2-COLUMN MAIN CONTENT --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- LEFT COLUMN: STUDENT INFORMATION (5 COLS) --}}
        <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200/90 p-6 space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-sm font-bold text-slate-900">Student Information</h2>
                <p class="text-[11px] text-slate-400">Account details submitted during registration</p>
            </div>

            <div class="space-y-4 text-xs">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">First Name</div>
                        <div class="font-semibold text-slate-800 text-sm">{{ $user->first_name ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Last Name</div>
                        <div class="font-semibold text-slate-800 text-sm">{{ $user->last_name ?? '-' }}</div>
                    </div>
                </div>

                <div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Student ID Number</div>
                    <div class="inline-block px-2.5 py-1 rounded-md bg-slate-100 text-slate-800 font-mono text-xs font-bold">
                        {{ $user->student_id_number ?? 'Not provided' }}
                    </div>
                </div>

                <div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Email Address</div>
                    <div class="font-medium text-slate-800 break-all">{{ $user->email }}</div>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-3 border-t border-slate-100">
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Email Status</div>
                        <div>
                            @if($user->email_verified_at)
                                <span class="inline-flex items-center gap-1 text-emerald-700 font-bold text-xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Verified
                                </span>
                            @else
                                <span class="text-amber-700 font-bold text-xs">Pending OTP</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Registered On</div>
                        <div class="text-slate-700 font-medium">{{ $user->created_at->format('M d, Y h:i A') }}</div>
                    </div>
                </div>

                <div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Account Role</div>
                    <span class="inline-block px-2 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider bg-slate-100 text-slate-600">
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: ID PHOTOS (7 COLS) --}}
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/90 p-6 space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Student ID Photos</h2>
                    <p class="text-[11px] text-slate-400">Click any photo to enlarge and inspect</p>
                </div>
                <span class="text-[10px] font-semibold text-slate-400">2 sides</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Front ID --}}
                <div class="space-y-1.5">
                    <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Front Side</div>
                    @if($user->id_picture_front)
                        <div class="group relative rounded-xl border border-slate-200 bg-slate-50 overflow-hidden cursor-pointer hover:border-slate-400 transition"
                             onclick="openImageModal('{{ route('admin.pending-registrations.id-picture', [$user, 'front']) }}', 'Front ID — {{ addslashes($user->name) }}')">
                            <img src="{{ route('admin.pending-registrations.id-picture', [$user, 'front']) }}" 
                                 alt="Front of Student ID" 
                                 class="w-full h-48 object-contain p-1 group-hover:opacity-95 transition"
                                 loading="lazy">
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition flex items-center justify-center">
                                <span class="opacity-0 group-hover:opacity-100 transition px-2.5 py-1 bg-white/90 text-slate-800 rounded-lg text-[10px] font-bold shadow-xs">
                                    Click to Enlarge
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="h-48 rounded-xl border border-dashed border-slate-200 bg-slate-50 flex flex-col items-center justify-center text-slate-400 text-xs">
                            <span>No front photo uploaded</span>
                        </div>
                    @endif
                </div>

                {{-- Back ID --}}
                <div class="space-y-1.5">
                    <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Back Side</div>
                    @if($user->id_picture_back)
                        <div class="group relative rounded-xl border border-slate-200 bg-slate-50 overflow-hidden cursor-pointer hover:border-slate-400 transition"
                             onclick="openImageModal('{{ route('admin.pending-registrations.id-picture', [$user, 'back']) }}', 'Back ID — {{ addslashes($user->name) }}')">
                            <img src="{{ route('admin.pending-registrations.id-picture', [$user, 'back']) }}" 
                                 alt="Back of Student ID" 
                                 class="w-full h-48 object-contain p-1 group-hover:opacity-95 transition"
                                 loading="lazy">
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition flex items-center justify-center">
                                <span class="opacity-0 group-hover:opacity-100 transition px-2.5 py-1 bg-white/90 text-slate-800 rounded-lg text-[10px] font-bold shadow-xs">
                                    Click to Enlarge
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="h-48 rounded-xl border border-dashed border-slate-200 bg-slate-50 flex flex-col items-center justify-center text-slate-400 text-xs">
                            <span>No back photo uploaded</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

{{-- REJECT MODAL --}}
<div class="modal-backdrop-custom" id="rejectModal">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full shadow-2xl border border-slate-200 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-bold text-sm text-slate-900">Reject Registration</h3>
            <button type="button" onclick="closeRejectModal()" class="text-slate-400 hover:text-slate-600 font-bold text-base">✕</button>
        </div>

        <form action="{{ route('admin.pending-registrations.reject', $user) }}" method="POST" class="space-y-4">
            @csrf @method('PATCH')
            <p class="text-xs text-slate-500 leading-relaxed">
                Provide feedback explaining why <strong>{{ $user->name }}</strong>'s registration is being rejected. This will be sent to their email.
            </p>

            <div>
                <label for="rejection_reason" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Rejection Reason *
                </label>
                <textarea id="rejection_reason" name="rejection_reason" rows="3" required
                          placeholder="e.g. Student ID picture is blurry or unreadable. Please register again with a clear photo."
                          class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs text-slate-800 outline-none focus:border-slate-400 focus:bg-white transition"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-1">
                <button type="button" onclick="closeRejectModal()" 
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl transition shadow-xs">
                    Confirm Rejection
                </button>
            </div>
        </form>
    </div>
</div>

{{-- IMAGE PREVIEW MODAL --}}
<div class="modal-backdrop-custom" id="imageModal" onclick="closeImageModal()">
    <div class="max-w-4xl max-h-[90vh] relative p-3 w-full" onclick="event.stopPropagation()">
        <button onclick="closeImageModal()" 
                class="absolute top-5 right-5 w-8 h-8 rounded-full bg-slate-900/80 hover:bg-slate-900 text-white font-bold flex items-center justify-center text-sm shadow-lg transition">
            ✕
        </button>
        <img id="modalImage" src="" alt="" class="max-w-full max-h-[82vh] mx-auto rounded-xl shadow-2xl object-contain bg-slate-950">
        <p id="modalCaption" class="text-center text-white/90 text-xs font-semibold mt-2.5"></p>
    </div>
</div>

<script>
function openRejectModal() {
    document.getElementById('rejectModal').classList.add('active');
}
function closeRejectModal() {
    document.getElementById('rejectModal').classList.remove('active');
}
function openImageModal(src, caption) {
    document.getElementById('modalImage').src = src;
    document.getElementById('modalCaption').textContent = caption;
    document.getElementById('imageModal').classList.add('active');
}
function closeImageModal() {
    document.getElementById('imageModal').classList.remove('active');
}
</script>
@endsection
