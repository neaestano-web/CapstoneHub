<x-guest-layout>
    <div class="auth-center-card auth-card-wide">
        <!-- Center Emblem / Logo -->
        <div class="auth-emblem-badge" aria-hidden="true">
            <img src="{{ asset('images/scs.png') }}" alt="SCS Logo">
        </div>

        <h1 class="auth-heading">Registration Status</h1>
        <p class="auth-subheading">
            Your account application is currently being processed by administrators.
        </p>

        <div class="w-full">
            @if($user->isPending())
                <div class="status-card status-pending">
                    <div class="status-icon-wrap pending-icon">
                        <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <h3>Pending Review</h3>
                    <p>Your registration is currently under review by an administrator. This usually takes 1-2 business days.</p>
                    
                    <div class="status-details">
                        <div class="detail-row">
                            <span class="detail-label">Name</span>
                            <span class="detail-value">{{ $user->name }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Student ID</span>
                            <span class="detail-value">{{ $user->student_id_number }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Email</span>
                            <span class="detail-value">{{ $user->email }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Submitted</span>
                            <span class="detail-value">{{ $user->created_at->format('M d, Y h:i A') }}</span>
                        </div>
                    </div>
                </div>
            @elseif($user->isRejected())
                <div class="status-card status-rejected">
                    <div class="status-icon-wrap rejected-icon">
                        <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="15" y1="9" x2="9" y2="15"/>
                            <line x1="9" y1="9" x2="15" y2="15"/>
                        </svg>
                    </div>
                    <h3>Registration Rejected</h3>
                    <p>Unfortunately, your student registration was not approved.</p>

                    @if($user->registration_rejection_reason)
                        <div class="rejection-reason">
                            <span class="rejection-label">Reason:</span>
                            <p>{{ $user->registration_rejection_reason }}</p>
                        </div>
                    @endif

                    <div class="status-details">
                        <div class="detail-row">
                            <span class="detail-label">Name</span>
                            <span class="detail-value">{{ $user->name }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Student ID</span>
                            <span class="detail-value">{{ $user->student_id_number }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="auth-btn-submit">
                    Log Out
                </button>
            </form>
        </div>
    </div>

    <style>
        .status-card {
            border-radius: 16px;
            padding: 1.6rem;
            text-align: center;
            background: rgba(22, 38, 54, 0.78);
            border: 1.5px solid rgba(255, 255, 255, 0.12);
        }
        .status-pending {
            border-color: rgba(234, 179, 8, 0.4);
            background: rgba(234, 179, 8, 0.05);
        }
        .status-rejected {
            border-color: rgba(239, 68, 68, 0.4);
            background: rgba(239, 68, 68, 0.05);
        }
        .status-icon-wrap {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }
        .pending-icon {
            background: rgba(234, 179, 8, 0.15);
            color: #facc15;
            box-shadow: 0 0 20px rgba(234, 179, 8, 0.25);
        }
        .rejected-icon {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            box-shadow: 0 0 20px rgba(239, 68, 68, 0.25);
        }
        .status-card h3 {
            font-size: 1.15rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 0.4rem;
        }
        .status-card > p {
            font-size: 0.82rem;
            color: #94a3b8;
            line-height: 1.5;
            margin-bottom: 1.15rem;
        }
        .status-details {
            text-align: left;
            background: rgba(11, 18, 27, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 0.75rem 1rem;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.4rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .detail-row:last-child { border-bottom: none; }
        .detail-label {
            font-size: 0.76rem;
            color: #94a3b8;
            font-weight: 600;
        }
        .detail-value {
            font-size: 0.82rem;
            color: #f8fafc;
            font-weight: 700;
        }
        .rejection-reason {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.25);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            text-align: left;
            margin-bottom: 1rem;
        }
        .rejection-label {
            font-size: 0.7rem;
            font-weight: 800;
            color: #f87171;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .rejection-reason p {
            font-size: 0.8rem;
            color: #fca5a5;
            margin-top: 0.2rem;
        }
    </style>
</x-guest-layout>
