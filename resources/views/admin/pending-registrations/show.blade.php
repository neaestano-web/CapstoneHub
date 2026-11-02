@extends('layouts.admin')
@section('title', 'Review Student Registration — JHCSC Capstone Portal')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

    .admin-review-body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: #2d2b56;
    }

    .review-hero-admin {
        background: linear-gradient(135deg, #0c4233 0%, #082d23 100%);
        border-radius: 28px;
        padding: 32px;
        color: #ffffff;
        box-shadow: 0 14px 40px rgba(12, 66, 51, 0.25);
    }

    .card-review-section {
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

<div class="admin-review-body space-y-6 max-w-7xl mx-auto">
    {{-- HERO HEADER --}}
    <div class="review-hero-admin space-y-4">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.pending-registrations.index') }}" class="px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 rounded-full font-extrabold text-xs text-white transition inline-flex items-center gap-2">
                ← Back to Registrations List
            </a>

            @if($user->registration_status === 'pending')
                <span class="px-3.5 py-1 rounded-full font-extrabold text-xs bg-amber-400 text-amber-950 inline-flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-900 animate-ping"></span>
                    Pending Verification
                </span>
            @elseif($user->registration_status === 'approved')
                <span class="px-3.5 py-1 rounded-full font-extrabold text-xs bg-emerald-400 text-emerald-950">
                    ✓ Verified & Approved
                </span>
            @elseif($user->registration_status === 'rejected')
                <span class="px-3.5 py-1 rounded-full font-extrabold text-xs bg-rose-400 text-rose-950">
                    ✕ Registration Rejected
                </span>
            @endif
        </div>

        <div class="space-y-1">
            <span class="inline-flex items-center gap-2 px-3 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-widest bg-emerald-900/80 text-emerald-200 border border-emerald-700/50">
                🎓 Identity Verification
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white leading-tight">
                Review Student Application: {{ $user->name }}
            </h1>
            <p class="text-xs text-emerald-100 font-medium">
                Verify student ID credentials against institutional registration requirements.
            </p>
        </div>
    </div>

    {{-- ACTION BAR --}}
    @if($user->registration_status !== 'approved')
        <div class="p-4 bg-white rounded-3xl border border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-xs">
            <span class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Verification Actions:</span>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <form action="{{ route('admin.pending-registrations.approve', $user) }}" method="POST" class="w-full sm:w-auto" onsubmit="return confirm('Approve registration for {{ addslashes($user->name) }}?')">
                    @csrf @method('PATCH')
                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-[#0c4233] hover:bg-[#082d23] text-white font-extrabold text-xs rounded-full shadow-xs transition">
                        ✓ Approve Student Registration
                    </button>
                </form>

                <button type="button" onclick="openRejectModal()" class="w-full sm:w-auto px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-full shadow-xs transition">
                    ✕ Reject Registration
                </button>

                <form action="{{ route('admin.pending-registrations.destroy', $user) }}" method="POST" class="w-full sm:w-auto" onsubmit="return confirm('Permanently delete registration application for {{ addslashes($user->name) }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full sm:w-auto px-4 py-2.5 bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-700 font-extrabold text-xs rounded-full transition">
                        🗑 Delete Application
                    </button>
                </form>
            </div>
        </div>
    @endif

    {{-- CONTENT GRID (2 COLUMNS) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Left: Student Details --}}
        <div class="card-review-section space-y-6">
            <h3 class="font-extrabold text-base text-[#2d2b56] flex items-center gap-2 border-b border-slate-100 pb-3">
                <span>📋</span> Student Information & Credentials
            </h3>

            <div class="space-y-4 text-xs">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">First Name</span>
                        <p class="font-extrabold text-sm text-[#2d2b56]">{{ $user->first_name ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Last Name</span>
                        <p class="font-extrabold text-sm text-[#2d2b56]">{{ $user->last_name ?? '-' }}</p>
                    </div>
                </div>

                <div>
                    <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Student ID Number</span>
                    <code class="inline-block px-3 py-1.5 bg-slate-100 text-[#0c4233] font-black text-sm rounded-xl">
                        {{ $user->student_id_number ?? 'Not provided' }}
                    </code>
                </div>

                <div>
                    <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Email Address</span>
                    <p class="font-bold text-slate-800">{{ $user->email }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                    <div>
                        <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Registration Date</span>
                        <p class="font-bold text-slate-800">{{ $user->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    <div>
                        <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Email Verified</span>
                        <p class="font-bold">
                            @if($user->email_verified_at)
                                <span class="text-emerald-700">✓ Verified</span>
                            @else
                                <span class="text-amber-700">⏳ Pending</span>
                            @endif
                        </p>
                    </div>
                </div>

                @if($user->registration_status === 'rejected' && $user->registration_rejection_reason)
                    <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl space-y-1 mt-4">
                        <span class="block text-xs font-extrabold uppercase tracking-wider">Rejection Reason Feedback:</span>
                        <p class="text-xs font-medium leading-relaxed">{{ $user->registration_rejection_reason }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Right: Student ID Documents --}}
        <div class="card-review-section space-y-6">
            <h3 class="font-extrabold text-base text-[#2d2b56] flex items-center gap-2 border-b border-slate-100 pb-3">
                <span>🪪</span> Uploaded Student ID Photos
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Front ID --}}
                <div class="space-y-2">
                    <span class="block text-xs font-extrabold text-slate-400 uppercase tracking-wider">Front of Student ID</span>
                    @if($user->id_picture_front)
                        <div class="border border-slate-200 rounded-2xl overflow-hidden cursor-pointer hover:border-[#0c4233] transition group"
                             onclick="openImageModal('{{ route('admin.pending-registrations.id-picture', [$user, 'front']) }}', 'Front of Student ID')">
                            <img src="{{ route('admin.pending-registrations.id-picture', [$user, 'front']) }}" alt="Front ID" 
                                 class="w-full h-44 object-cover group-hover:scale-105 transition duration-300">
                        </div>
                    @else
                        <div class="h-44 bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl flex items-center justify-center text-xs font-bold text-slate-400">
                            No front ID uploaded
                        </div>
                    @endif
                </div>

                {{-- Back ID --}}
                <div class="space-y-2">
                    <span class="block text-xs font-extrabold text-slate-400 uppercase tracking-wider">Back of Student ID</span>
                    @if($user->id_picture_back)
                        <div class="border border-slate-200 rounded-2xl overflow-hidden cursor-pointer hover:border-[#0c4233] transition group"
                             onclick="openImageModal('{{ route('admin.pending-registrations.id-picture', [$user, 'back']) }}', 'Back of Student ID')">
                            <img src="{{ route('admin.pending-registrations.id-picture', [$user, 'back']) }}" alt="Back ID" 
                                 class="w-full h-44 object-cover group-hover:scale-105 transition duration-300">
                        </div>
                    @else
                        <div class="h-44 bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl flex items-center justify-center text-xs font-bold text-slate-400">
                            No back ID uploaded
                        </div>
                    @endif
                </div>
            </div>
            <p class="text-[11px] font-semibold text-slate-400 text-center">Click on any ID photo to expand full size.</p>
        </div>
    </div>
</div>

{{-- REJECT REGISTRATION MODAL --}}
<div class="modal-backdrop-custom" id="rejectModal">
    <div class="bg-white rounded-3xl p-6 max-w-lg w-full shadow-2xl border border-slate-200 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-extrabold text-base text-[#2d2b56]">Reject Registration Application</h3>
            <button onclick="closeRejectModal()" class="text-slate-400 font-bold text-lg">✕</button>
        </div>

        <form action="{{ route('admin.pending-registrations.reject', $user) }}" method="POST" class="space-y-4">
            @csrf @method('PATCH')
            <p class="text-xs text-slate-500 font-medium leading-relaxed">
                Specify the reason for rejecting <strong>{{ $user->name }}</strong>'s student account request.
            </p>

            <div>
                <label for="rejection_reason" class="block text-xs font-extrabold text-[#2d2b56] uppercase tracking-wider mb-2">
                    Rejection Feedback *
                </label>
                <textarea id="rejection_reason" name="rejection_reason" rows="4" required
                          placeholder="e.g. Student ID picture is unreadable. Please re-register with a clear photo..."
                          class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs font-medium text-[#2d2b56] outline-none focus:border-[#0c4233] focus:bg-white transition"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-slate-100 text-slate-700 font-extrabold text-xs rounded-full">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 bg-rose-600 text-white font-extrabold text-xs rounded-full shadow-xs">
                    Confirm Rejection
                </button>
            </div>
        </form>
    </div>
</div>

{{-- FULL IMAGE MODAL --}}
<div class="modal-backdrop-custom" id="imageModal" onclick="closeImageModal()">
    <div class="max-w-4xl max-h-[90vh] relative p-4" onclick="event.stopPropagation()">
        <button onclick="closeImageModal()" class="absolute top-2 right-2 w-8 h-8 rounded-full bg-rose-600 text-white font-bold flex items-center justify-center text-sm shadow-lg">✕</button>
        <img id="modalImage" src="" alt="" class="max-w-full max-h-[80vh] rounded-2xl shadow-2xl object-contain bg-slate-900">
        <p id="modalCaption" class="text-center text-white text-xs font-bold mt-3"></p>
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
