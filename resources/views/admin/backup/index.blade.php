@extends('layouts.admin')
@section('title', 'Backup & Recovery — JHCSC Capstone Portal')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    .backup-body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: #2d2b56;
    }

    .backup-card {
        background: #ffffff;
        border-radius: 22px;
        border: 1.5px solid #e2e8f0;
        padding: 24px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.03);
    }

    .backup-tab-btn {
        padding: 10px 18px;
        border-radius: 9999px;
        font-size: 12px;
        font-weight: 800;
        transition: all 0.2s ease;
        border: 1.5px solid transparent;
        cursor: pointer;
    }

    .backup-tab-btn.active {
        background: #0c4233;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(12, 66, 51, 0.22);
    }

    .backup-tab-btn:not(.active) {
        background: #f8fafc;
        color: #475569;
        border-color: #e2e8f0;
    }

    .backup-tab-btn:not(.active):hover {
        background: #f1f5f9;
        color: #2d2b56;
    }
</style>

@php
    $activeTab = request('tab', 'backup');
    if (!in_array($activeTab, ['backup', 'restore', 'recycle', 'audit'], true)) {
        $activeTab = 'backup';
    }
    $totalTrashed = $trashedResearches->count() + $trashedUsers->count() + $trashedCategories->count();
@endphp

<div class="backup-body space-y-7 max-w-7xl mx-auto">
    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-5 border-b border-slate-200/80">
        <div>
            <span class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-emerald-100 text-emerald-900 mb-2">
                🛡️ Resilience & Disaster Recovery
            </span>
            <h1 style="font-size: 2rem;" class="font-extrabold text-[#2d2b56] tracking-tight">
                Backup &amp; <span class="text-[#0c4233]">Recovery</span>
            </h1>
            <p class="text-slate-500 text-xs font-medium mt-1">
                AES-256 encrypted snapshots, HMAC integrity verification, Recycle Bin recovery, and automated off-site backups.
            </p>
        </div>

        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-[#2d2b56] rounded-full font-extrabold text-xs transition inline-flex items-center gap-2 self-start md:self-auto">
            ← Back to Dashboard
        </a>
    </div>

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-semibold flex items-center gap-3">
            <span class="text-base">✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-semibold flex items-center gap-3">
            <span class="text-base">⚠️</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-semibold space-y-1">
            <div class="font-extrabold">⚠️ Validation Error:</div>
            @foreach($errors->all() as $err)
                <div>• {{ $err }}</div>
            @endforeach
        </div>
    @endif

    {{-- TOP HEALTH SUMMARY CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="backup-card">
            <div class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Protected Records</div>
            <div class="mt-3 flex items-baseline justify-between">
                <p class="text-3xl font-extrabold text-[#2d2b56]">{{ number_format($overview['total_records']) }}</p>
                <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 uppercase">
                    {{ $overview['db_driver'] }}
                </span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Across {{ count($overview['tables']) }} core database tables</p>
        </div>

        <div class="backup-card">
            <div class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Last Manual Backup</div>
            <div class="mt-3">
                @if($lastManualBackup)
                    <p class="text-sm font-extrabold text-[#0c4233]">{{ $lastManualBackup->created_at->format('M d, Y h:i A') }}</p>
                    <p class="text-[11px] text-slate-400 mt-1 truncate">By {{ $lastManualBackup->actor_email ?? 'Admin' }}</p>
                @else
                    <p class="text-sm font-extrabold text-amber-700">No Snapshot Yet</p>
                    <p class="text-[11px] text-slate-400 mt-1">Download a backup below</p>
                @endif
            </div>
        </div>

        <div class="backup-card">
            <div class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Automated Off-Site</div>
            <div class="mt-3">
                @if($overview['automation_configured'])
                    <span class="inline-flex items-center gap-1.5 text-xs font-extrabold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800">
                        ● Token Configured
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 text-xs font-extrabold px-2.5 py-1 rounded-full bg-amber-100 text-amber-800">
                        ○ Needs Env Token
                    </span>
                @endif
                <p class="text-[11px] text-slate-400 mt-2">
                    @if($lastAutomatedBackup)
                        Last run: {{ $lastAutomatedBackup->created_at->diffForHumans() }}
                    @else
                        No automated run logged yet
                    @endif
                </p>
            </div>
        </div>

        <div class="backup-card">
            <div class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Recycle Bin</div>
            <div class="mt-3 flex items-baseline justify-between">
                <p class="text-3xl font-extrabold {{ $totalTrashed > 0 ? 'text-amber-600' : 'text-[#2d2b56]' }}">{{ number_format($totalTrashed) }}</p>
                <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600">Recoverable</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">
                {{ $trashedResearches->count() }} papers • {{ $trashedUsers->count() }} users • {{ $trashedCategories->count() }} categories
            </p>
        </div>
    </div>

    {{-- SECTION NAVIGATION TABS --}}
    <div class="flex flex-wrap items-center gap-2.5">
        <button type="button" onclick="switchBackupTab('backup')" id="tab-btn-backup" class="backup-tab-btn {{ $activeTab === 'backup' ? 'active' : '' }}">
            💾 Export &amp; Automation
        </button>
        <button type="button" onclick="switchBackupTab('restore')" id="tab-btn-restore" class="backup-tab-btn {{ $activeTab === 'restore' ? 'active' : '' }}">
            🔄 Restore System
        </button>
        <button type="button" onclick="switchBackupTab('recycle')" id="tab-btn-recycle" class="backup-tab-btn {{ $activeTab === 'recycle' ? 'active' : '' }}">
            🗑️ Recycle Bin ({{ $totalTrashed }})
        </button>
        <button type="button" onclick="switchBackupTab('audit')" id="tab-btn-audit" class="backup-tab-btn {{ $activeTab === 'audit' ? 'active' : '' }}">
            📜 Audit Logs ({{ $auditLogs->count() }})
        </button>
    </div>

    {{-- TAB 1: EXPORT & AUTOMATED OFF-SITE BACKUP --}}
    <div id="tab-panel-backup" class="space-y-6 {{ $activeTab === 'backup' ? '' : 'hidden' }}">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Manual Encrypted Backup Card --}}
            <div class="backup-card space-y-5">
                <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-4">
                    <div>
                        <h2 class="text-base font-extrabold text-[#2d2b56]">Download Encrypted Full Snapshot</h2>
                        <p class="text-xs text-slate-500 mt-1">
                            Exports all database records, chunked manuscripts, AI embeddings, users, and storage files into a compressed, AES-256 encrypted <code class="px-1.5 py-0.5 bg-slate-100 rounded">.cshub</code> package.
                        </p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-800 border border-emerald-200 shrink-0">
                        AES-256 + HMAC
                    </span>
                </div>

                <form method="POST" action="{{ route('admin.backup.export') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="export_passphrase" class="block text-xs font-extrabold text-[#2d2b56] mb-1.5">
                            Custom Recovery Passphrase <span class="font-medium text-slate-400">(Optional — Recommended for cross-server recovery)</span>
                        </label>
                        <input type="password"
                               id="export_passphrase"
                               name="passphrase"
                               minlength="6"
                               autocomplete="new-password"
                               placeholder="Leave blank to encrypt with server APP_KEY / BACKUP_SECRET"
                               class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:border-[#0c4233] focus:ring-2 focus:ring-emerald-100 outline-none">
                        <p class="text-[11px] text-slate-400 mt-1.5">
                            If set, store this passphrase safely (minimum 6 characters). You will need it when restoring on a fresh server.
                        </p>
                    </div>

                    <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-[#0c4233] hover:bg-[#082d23] text-white rounded-full font-extrabold text-xs transition inline-flex items-center justify-center gap-2 shadow-sm cursor-pointer">
                        <span>⬇️</span>
                        <span>Generate &amp; Download Backup (.cshub)</span>
                    </button>
                </form>

                {{-- Table Scope Breakdown --}}
                <div class="pt-3 border-t border-slate-100">
                    <div class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider mb-2.5">Included Tables Snapshot</div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @foreach($overview['tables'] as $tbl => $cnt)
                            <div class="px-3 py-2 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between text-[11px]">
                                <span class="font-semibold text-slate-600 truncate pr-2">{{ $tbl }}</span>
                                <span class="font-mono font-extrabold text-[#0c4233]">{{ number_format($cnt) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Automated Off-Site Setup Card --}}
            <div class="backup-card space-y-5">
                <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-4">
                    <div>
                        <h2 class="text-base font-extrabold text-[#2d2b56]">Automated Daily Off-Site Backup</h2>
                        <p class="text-xs text-slate-500 mt-1">
                            Protects against ransomware or server compromise by pulling encrypted snapshots daily outside Render via GitHub Actions or CLI cron.
                        </p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold {{ $overview['automation_configured'] ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }} shrink-0">
                        {{ $overview['automation_configured'] ? 'READY' : 'SETUP NEEDED' }}
                    </span>
                </div>

                <div class="space-y-3 text-xs text-slate-600">
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 space-y-1.5">
                        <div class="font-extrabold text-[#2d2b56]">1. Render Environment Variables</div>
                        <p class="text-[11px] text-slate-500">Set a strong random secret in Render Environment:</p>
                        <code class="block px-3 py-1.5 rounded-lg bg-slate-900 text-emerald-300 text-[11px] font-mono overflow-x-auto">BACKUP_AUTOMATION_TOKEN=your-random-secret-token</code>
                    </div>

                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 space-y-1.5">
                        <div class="font-extrabold text-[#2d2b56]">2. GitHub Repository Secrets (Daily 02:00 UTC Workflow)</div>
                        <p class="text-[11px] text-slate-500">
                            The workflow <code class="bg-slate-200 px-1 rounded">.github/workflows/daily-backup.yml</code> stores encrypted snapshots as private 30-day GitHub Artifacts when these secrets are configured:
                        </p>
                        <ul class="list-disc list-inside text-[11px] text-slate-500 space-y-0.5">
                            <li><code>BACKUP_APP_URL</code> = <code>{{ config('app.url') }}</code></li>
                            <li><code>BACKUP_AUTOMATION_TOKEN</code> = same token as Render</li>
                        </ul>
                    </div>

                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 space-y-1.5">
                        <div class="font-extrabold text-[#2d2b56]">3. Local / Server CLI Export</div>
                        <code class="block px-3 py-1.5 rounded-lg bg-slate-900 text-emerald-300 text-[11px] font-mono overflow-x-auto">php artisan backup:export --keep=7</code>
                    </div>
                </div>

                @if(!empty($localBackups))
                    <div class="pt-3 border-t border-slate-100">
                        <div class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider mb-2">Local CLI Snapshots (storage/app/backups)</div>
                        <div class="space-y-1.5">
                            @foreach($localBackups as $lb)
                                <div class="flex items-center justify-between text-[11px] px-3 py-2 rounded-xl bg-slate-50 border border-slate-100">
                                    <span class="font-mono font-bold text-[#2d2b56] truncate">{{ $lb['name'] }}</span>
                                    <span class="text-slate-400 shrink-0 ml-2">{{ number_format($lb['size'] / 1024, 1) }} KB • {{ $lb['modified_at'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- TAB 2: DISASTER RECOVERY (RESTORE) --}}
    <div id="tab-panel-restore" class="space-y-6 {{ $activeTab === 'restore' ? '' : 'hidden' }}">
        <div class="backup-card max-w-3xl space-y-5 border-amber-200/80">
            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-950 text-xs space-y-1">
                <div class="font-extrabold flex items-center gap-2">
                    <span>⚠️</span>
                    <span>Transactional Full-System Recovery</span>
                </div>
                <p class="text-[11px] leading-relaxed">
                    Restoring a snapshot verifies the <strong>HMAC-SHA256</strong> signature first to reject corrupted or tampered files, then restores all core tables inside an <strong>ACID database transaction</strong> (automatically rolling back if any error occurs). Download a fresh backup before restoring.
                </p>
            </div>

            <form method="POST" action="{{ route('admin.backup.restore') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label for="backup_file" class="block text-xs font-extrabold text-[#2d2b56] mb-1.5">
                        Select Backup Snapshot File (.cshub)
                    </label>
                    <input type="file"
                           id="backup_file"
                           name="backup_file"
                           accept=".cshub"
                           required
                           class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:border-[#0c4233] outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="restore_passphrase" class="block text-xs font-extrabold text-[#2d2b56] mb-1.5">
                            Backup Passphrase <span class="font-medium text-slate-400">(If used during export)</span>
                        </label>
                        <input type="password"
                               id="restore_passphrase"
                               name="passphrase"
                               autocomplete="off"
                               placeholder="Leave blank if encrypted with APP_KEY"
                               class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:border-[#0c4233] outline-none">
                    </div>

                    <div>
                        <label for="current_password" class="block text-xs font-extrabold text-[#2d2b56] mb-1.5">
                            Your Current Admin Password <span class="text-rose-600">*</span>
                        </label>
                        <input type="password"
                               id="current_password"
                               name="current_password"
                               required
                               autocomplete="current-password"
                               placeholder="Verify administrator identity"
                               class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:border-[#0c4233] outline-none">
                    </div>
                </div>

                <div>
                    <label for="confirm_text" class="block text-xs font-extrabold text-[#2d2b56] mb-1.5">
                        Type <code class="px-1.5 py-0.5 rounded bg-rose-100 text-rose-800">RESTORE</code> to confirm <span class="text-rose-600">*</span>
                    </label>
                    <input type="text"
                           id="confirm_text"
                           name="confirm_text"
                           required
                           autocomplete="off"
                           placeholder="RESTORE"
                           class="w-full sm:w-64 px-4 py-2.5 text-xs font-mono uppercase rounded-xl border border-slate-200 focus:border-rose-500 outline-none">
                </div>

                <button type="submit"
                        onclick="return confirm('Are you sure you want to restore from this backup snapshot? Current data will be replaced by the snapshot contents inside a safe transaction.');"
                        class="px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white rounded-full font-extrabold text-xs transition inline-flex items-center gap-2 cursor-pointer">
                    <span>🔄</span>
                    <span>Verify &amp; Restore System</span>
                </button>
            </form>
        </div>
    </div>

    {{-- TAB 3: RECYCLE BIN (SOFT DELETES) --}}
    <div id="tab-panel-recycle" class="space-y-6 {{ $activeTab === 'recycle' ? '' : 'hidden' }}">
        {{-- Trashed Researches --}}
        <div class="backup-card space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-sm font-extrabold text-[#2d2b56]">Deleted Research Papers ({{ $trashedResearches->count() }})</h3>
                    <p class="text-[11px] text-slate-400">Soft-deleted studies keep their manuscripts, chunks, and AI embeddings intact for recovery.</p>
                </div>
            </div>

            @if($trashedResearches->isEmpty())
                <div class="p-6 text-center text-xs text-slate-400 bg-slate-50 rounded-2xl">No deleted research papers in the Recycle Bin.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-[11px] text-slate-400 uppercase">
                                <th class="py-2.5 pr-4">Title</th>
                                <th class="py-2.5 pr-4">Category</th>
                                <th class="py-2.5 pr-4">Author / Owner</th>
                                <th class="py-2.5 pr-4">Deleted At</th>
                                <th class="py-2.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($trashedResearches as $item)
                                <tr>
                                    <td class="py-3 pr-4 font-bold text-[#2d2b56] max-w-xs truncate">{{ $item->title }}</td>
                                    <td class="py-3 pr-4 text-slate-500">{{ $item->category?->name ?? '—' }}</td>
                                    <td class="py-3 pr-4 text-slate-500">{{ $item->user?->email ?? $item->authors }}</td>
                                    <td class="py-3 pr-4 text-slate-400 whitespace-nowrap">{{ $item->deleted_at?->format('M d, Y h:i A') }}</td>
                                    <td class="py-3 text-right whitespace-nowrap space-x-2">
                                        <form method="POST" action="{{ route('admin.backup.recycle.researches.restore', $item->id) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-3 py-1.5 rounded-full bg-emerald-100 hover:bg-emerald-200 text-emerald-900 font-extrabold text-[11px] cursor-pointer">
                                                ↩ Restore
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.backup.recycle.researches.force-delete', $item->id) }}" class="inline" onsubmit="return confirm('Permanently delete this research and all its files? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 rounded-full bg-rose-100 hover:bg-rose-200 text-rose-800 font-extrabold text-[11px] cursor-pointer">
                                                Delete Forever
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Trashed Users --}}
        <div class="backup-card space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-sm font-extrabold text-[#2d2b56]">Deleted User Accounts ({{ $trashedUsers->count() }})</h3>
                    <p class="text-[11px] text-slate-400">Restore mistakenly deleted accounts or purge permanently.</p>
                </div>
            </div>

            @if($trashedUsers->isEmpty())
                <div class="p-6 text-center text-xs text-slate-400 bg-slate-50 rounded-2xl">No deleted user accounts in the Recycle Bin.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-[11px] text-slate-400 uppercase">
                                <th class="py-2.5 pr-4">Name</th>
                                <th class="py-2.5 pr-4">Email</th>
                                <th class="py-2.5 pr-4">Role</th>
                                <th class="py-2.5 pr-4">Deleted At</th>
                                <th class="py-2.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($trashedUsers as $u)
                                <tr>
                                    <td class="py-3 pr-4 font-bold text-[#2d2b56]">{{ $u->first_last_name }}</td>
                                    <td class="py-3 pr-4 text-slate-500">{{ $u->email }}</td>
                                    <td class="py-3 pr-4 uppercase text-[10px] font-extrabold text-slate-500">{{ $u->role }}</td>
                                    <td class="py-3 pr-4 text-slate-400 whitespace-nowrap">{{ $u->deleted_at?->format('M d, Y h:i A') }}</td>
                                    <td class="py-3 text-right whitespace-nowrap space-x-2">
                                        <form method="POST" action="{{ route('admin.backup.recycle.users.restore', $u->id) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-3 py-1.5 rounded-full bg-emerald-100 hover:bg-emerald-200 text-emerald-900 font-extrabold text-[11px] cursor-pointer">
                                                ↩ Restore
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.backup.recycle.users.force-delete', $u->id) }}" class="inline" onsubmit="return confirm('Permanently delete this user account?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 rounded-full bg-rose-100 hover:bg-rose-200 text-rose-800 font-extrabold text-[11px] cursor-pointer">
                                                Delete Forever
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Trashed Categories --}}
        <div class="backup-card space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-sm font-extrabold text-[#2d2b56]">Deleted Categories ({{ $trashedCategories->count() }})</h3>
                    <p class="text-[11px] text-slate-400">Recover deleted subject categories.</p>
                </div>
            </div>

            @if($trashedCategories->isEmpty())
                <div class="p-6 text-center text-xs text-slate-400 bg-slate-50 rounded-2xl">No deleted categories in the Recycle Bin.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-[11px] text-slate-400 uppercase">
                                <th class="py-2.5 pr-4">Category Name</th>
                                <th class="py-2.5 pr-4">Deleted At</th>
                                <th class="py-2.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($trashedCategories as $cat)
                                <tr>
                                    <td class="py-3 pr-4 font-bold text-[#2d2b56]">{{ $cat->name }}</td>
                                    <td class="py-3 pr-4 text-slate-400 whitespace-nowrap">{{ $cat->deleted_at?->format('M d, Y h:i A') }}</td>
                                    <td class="py-3 text-right whitespace-nowrap space-x-2">
                                        <form method="POST" action="{{ route('admin.backup.recycle.categories.restore', $cat->id) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-3 py-1.5 rounded-full bg-emerald-100 hover:bg-emerald-200 text-emerald-900 font-extrabold text-[11px] cursor-pointer">
                                                ↩ Restore
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.backup.recycle.categories.force-delete', $cat->id) }}" class="inline" onsubmit="return confirm('Permanently delete this category?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 rounded-full bg-rose-100 hover:bg-rose-200 text-rose-800 font-extrabold text-[11px] cursor-pointer">
                                                Delete Forever
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- TAB 4: AUDIT LOGS --}}
    <div id="tab-panel-audit" class="space-y-6 {{ $activeTab === 'audit' ? '' : 'hidden' }}">
        <div class="backup-card space-y-4">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="text-sm font-extrabold text-[#2d2b56]">Recent Backup, Recovery &amp; Deletion Audit Trail</h3>
                <p class="text-[11px] text-slate-400">Tracks exports, automated pulls, restores, soft-deletes, permanent purges, and system wipes.</p>
            </div>

            @if($auditLogs->isEmpty())
                <div class="p-6 text-center text-xs text-slate-400 bg-slate-50 rounded-2xl">No audit events recorded yet.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-[11px] text-slate-400 uppercase">
                                <th class="py-2.5 pr-4">Timestamp</th>
                                <th class="py-2.5 pr-4">Action</th>
                                <th class="py-2.5 pr-4">Target</th>
                                <th class="py-2.5 pr-4">Actor</th>
                                <th class="py-2.5">IP Address</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($auditLogs as $log)
                                <tr>
                                    <td class="py-2.5 pr-4 text-slate-400 whitespace-nowrap">{{ $log->created_at?->format('M d, Y h:i:s A') }}</td>
                                    <td class="py-2.5 pr-4">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold font-mono
                                            {{ str_contains($log->action, 'fail') || str_contains($log->action, 'unauthorized') || str_contains($log->action, 'wiped') ? 'bg-rose-100 text-rose-800' : (str_contains($log->action, 'restore') || str_contains($log->action, 'backup') ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800') }}">
                                            {{ $log->action }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 pr-4 font-semibold text-[#2d2b56] max-w-xs truncate">{{ $log->target_label ?? '—' }}</td>
                                    <td class="py-2.5 pr-4 text-slate-500">{{ $log->actor_email ?? 'system' }}</td>
                                    <td class="py-2.5 font-mono text-[11px] text-slate-400">{{ $log->ip_address ?? 'cli' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function switchBackupTab(tab) {
    ['backup', 'restore', 'recycle', 'audit'].forEach(function (name) {
        var panel = document.getElementById('tab-panel-' + name);
        var btn = document.getElementById('tab-btn-' + name);
        if (panel) panel.classList.toggle('hidden', name !== tab);
        if (btn) btn.classList.toggle('active', name === tab);
    });
    if (window.history && window.history.replaceState) {
        var url = new URL(window.location.href);
        url.searchParams.set('tab', tab);
        window.history.replaceState({}, '', url.toString());
    }
}
</script>
@endsection

