@extends('layouts.admin')
@section('title', 'Edit User Account — JHCSC Capstone Portal')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

    .admin-user-form-body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: #2d2b56;
    }

    .form-card-admin {
        background: #ffffff;
        border-radius: 28px;
        border: 1.5px solid #e2e8f0;
        padding: 32px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
    }
</style>

<div class="admin-user-form-body space-y-6 max-w-2xl mx-auto">
    <div class="flex items-center justify-between pb-4 border-b border-slate-200/80">
        <div>
            <h1 style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.85rem;" class="font-extrabold text-[#2d2b56] tracking-tight leading-tight">
                Edit <span class="text-[#0c4233]">User Account</span>
            </h1>
            <p class="text-slate-500 text-xs font-medium mt-1">
                Update account details, change role, or reset password for {{ $user->name }}.
            </p>
        </div>

        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs rounded-full transition">
            ← Back to Users
        </a>
    </div>

    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-bold">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-bold space-y-1">
            @foreach($errors->all() as $error)
                <p>⚠️ {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="form-card-admin space-y-6">
        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label for="name" class="block text-xs font-extrabold text-[#2d2b56] uppercase tracking-wider mb-2">
                    Full Name *
                </label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                       class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-3.5 text-xs font-semibold text-[#2d2b56] outline-none focus:border-[#0c4233] focus:bg-white transition">
            </div>

            <div>
                <label for="email" class="block text-xs font-extrabold text-[#2d2b56] uppercase tracking-wider mb-2">
                    Email Address *
                </label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                       class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-3.5 text-xs font-semibold text-[#2d2b56] outline-none focus:border-[#0c4233] focus:bg-white transition">
            </div>

            <div>
                <label for="role" class="block text-xs font-extrabold text-[#2d2b56] uppercase tracking-wider mb-2">
                    Account Role *
                </label>
                <select name="role" id="role" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-3.5 text-xs font-semibold text-[#2d2b56] outline-none focus:border-[#0c4233] focus:bg-white transition">
                    <option value="student" {{ old('role', $user->role) == 'student' ? 'selected' : '' }}>🎓 Student Researcher</option>
                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>👑 Administrator</option>
                </select>
            </div>

            <div class="pt-2 border-t border-slate-100 space-y-4">
                <span class="block text-xs font-extrabold text-slate-400 uppercase tracking-wider">Change Password (Optional)</span>

                <div>
                    <label for="password" class="block text-xs font-extrabold text-[#2d2b56] uppercase tracking-wider mb-1">
                        New Password
                    </label>
                    <input type="password" name="password" id="password" placeholder="Leave blank to keep existing password..."
                           class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-3.5 text-xs font-semibold text-[#2d2b56] outline-none focus:border-[#0c4233] focus:bg-white transition">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-extrabold text-[#2d2b56] uppercase tracking-wider mb-1">
                        Confirm New Password
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Re-type new password..."
                           class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-3.5 text-xs font-semibold text-[#2d2b56] outline-none focus:border-[#0c4233] focus:bg-white transition">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs rounded-full transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-[#0c4233] hover:bg-[#082d23] text-white font-extrabold text-xs rounded-full shadow-xs transition">
                    Update Account
                </button>
            </div>
        </form>
    </div>
</div>
@endsection