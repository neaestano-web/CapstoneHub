<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\OtpCode;
use App\Models\Research;
use App\Models\ResearchCategory;
use App\Models\User;
use App\Services\BackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BackupRecoveryController extends Controller
{
    public function index(BackupService $backupService)
    {
        $overview = $backupService->getSystemBackupOverview();

        $trashedResearches = Research::onlyTrashed()
            ->with(['category' => fn ($q) => $q->withTrashed(), 'user' => fn ($q) => $q->withTrashed()])
            ->latest('deleted_at')
            ->get();

        $trashedUsers = User::onlyTrashed()
            ->latest('deleted_at')
            ->get();

        $trashedCategories = ResearchCategory::onlyTrashed()
            ->latest('deleted_at')
            ->get();

        $auditLogs = AuditLog::latest()->take(60)->get();

        $lastManualBackup = AuditLog::where('action', 'backup.exported')->latest()->first();
        $lastAutomatedBackup = AuditLog::where('action', 'backup.automated')->latest()->first();
        $lastRestore = AuditLog::where('action', 'backup.restored')->latest()->first();

        $localBackups = [];
        $backupDir = storage_path('app/backups');
        if (is_dir($backupDir)) {
            $files = glob($backupDir . DIRECTORY_SEPARATOR . '*.cshub') ?: [];
            usort($files, fn ($a, $b) => filemtime($b) <=> filemtime($a));
            foreach (array_slice($files, 0, 10) as $file) {
                $localBackups[] = [
                    'name' => basename($file),
                    'size' => filesize($file) ?: 0,
                    'modified_at' => date('Y-m-d H:i:s', filemtime($file)),
                ];
            }
        }

        return view('admin.backup.index', compact(
            'overview',
            'trashedResearches',
            'trashedUsers',
            'trashedCategories',
            'auditLogs',
            'lastManualBackup',
            'lastAutomatedBackup',
            'lastRestore',
            'localBackups'
        ));
    }

    public function export(Request $request, BackupService $backupService)
    {
        $request->validate([
            'passphrase' => ['nullable', 'string', 'min:6', 'max:128'],
        ]);

        $passphrase = $request->input('passphrase');
        $result = $backupService->createBackupFile($passphrase);

        AuditLog::record(
            'backup.exported',
            'backup',
            null,
            $result['filename'],
            [
                'size_bytes' => $result['size'],
                'sha256' => $result['sha256'],
                'key_mode' => $result['key_mode'],
                'tables' => $result['tables'],
                'storage_files_count' => $result['storage_files_count'],
            ]
        );

        return response()->download($result['path'], $result['filename'], [
            'Content-Type' => 'application/octet-stream',
            'X-Backup-SHA256' => $result['sha256'],
        ])->deleteFileAfterSend(true);
    }

    public function restore(Request $request, BackupService $backupService)
    {
        $request->validate([
            'backup_file' => ['required', 'file', 'max:102400'],
            'passphrase' => ['nullable', 'string', 'max:128'],
            'current_password' => ['required', 'current_password'],
            'confirm_text' => ['required', 'in:RESTORE'],
        ], [
            'confirm_text.in' => 'Please type RESTORE in uppercase to confirm full system recovery.',
            'current_password.current_password' => 'Your current administrator password is incorrect.',
        ]);

        $uploadedFile = $request->file('backup_file');
        $originalName = $uploadedFile->getClientOriginalName();
        $passphrase = $request->input('passphrase');

        try {
            $result = $backupService->restoreFromBackup($uploadedFile->getRealPath(), $passphrase);

            AuditLog::record(
                'backup.restored',
                'backup',
                null,
                $originalName,
                [
                    'restored_tables' => $result['restored_tables'],
                    'restored_files' => $result['restored_files'],
                    'backup_created_at' => $result['manifest']['created_at'] ?? null,
                ]
            );

            $totalRows = array_sum($result['restored_tables']);

            return redirect()->route('admin.backup.index')->with(
                'success',
                "System restored cleanly from \"{$originalName}\" ({$totalRows} database records and {$result['restored_files']} storage files recovered)."
            );
        } catch (\Throwable $e) {
            Log::error('Backup restore failed: ' . $e->getMessage());

            AuditLog::record(
                'backup.restore_failed',
                'backup',
                null,
                $originalName,
                ['error' => $e->getMessage()]
            );

            return redirect()->route('admin.backup.index')
                ->with('error', 'Restore aborted safely (no data was changed): ' . $e->getMessage());
        }
    }

    public function restoreResearch(int $id)
    {
        $research = Research::onlyTrashed()->findOrFail($id);
        $research->restore();

        AuditLog::record('research.restored', 'research', $research->id, $research->title);

        return redirect()->route('admin.backup.index', ['tab' => 'recycle'])
            ->with('success', "Research \"{$research->title}\" has been restored from the Recycle Bin.");
    }

    public function forceDeleteResearch(int $id)
    {
        $research = Research::onlyTrashed()->findOrFail($id);
        $title = $research->title;
        $researchId = $research->id;

        foreach ($research->files as $f) {
            if ($f->file_path && Storage::disk('public')->exists($f->file_path)) {
                Storage::disk('public')->delete($f->file_path);
            }
            $f->delete();
        }

        if ($research->pdf_path && Storage::disk('public')->exists($research->pdf_path)) {
            Storage::disk('public')->delete($research->pdf_path);
        }

        if ($research->embedding) {
            $research->embedding->delete();
        }

        $research->categories()->detach();
        $research->projectTypes()->detach();

        if (session('current_viewed_research_id') == $research->id) {
            session()->forget('current_viewed_research_id');
        }

        $research->forceDelete();

        AuditLog::record('research.force_deleted', 'research', $researchId, $title);

        return redirect()->route('admin.backup.index', ['tab' => 'recycle'])
            ->with('success', "Research \"{$title}\" was permanently deleted.");
    }

    public function restoreUser(int $id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        AuditLog::record('user.restored', 'user', $user->id, $user->email);

        return redirect()->route('admin.backup.index', ['tab' => 'recycle'])
            ->with('success', "User \"{$user->first_last_name}\" ({$user->email}) has been restored.");
    }

    public function forceDeleteUser(int $id)
    {
        $user = User::onlyTrashed()->findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->route('admin.backup.index', ['tab' => 'recycle'])
                ->with('error', 'Cannot permanently delete your own account.');
        }

        $researchesCount = Research::withTrashed()->where('user_id', $user->id)->count();
        if ($researchesCount > 0) {
            return redirect()->route('admin.backup.index', ['tab' => 'recycle'])
                ->with('error', "Cannot permanently delete \"{$user->email}\". They still have {$researchesCount} research record(s) (including trashed ones). Permanently delete those researches first.");
        }

        $email = $user->email;
        $userId = $user->id;

        if ($user->id_picture_front && !str_starts_with($user->id_picture_front, 'data:image/')) {
            Storage::disk('local')->delete($user->id_picture_front);
            Storage::disk('public')->delete($user->id_picture_front);
        }
        if ($user->id_picture_back && !str_starts_with($user->id_picture_back, 'data:image/')) {
            Storage::disk('local')->delete($user->id_picture_back);
            Storage::disk('public')->delete($user->id_picture_back);
        }

        OtpCode::where('user_id', $user->id)->delete();
        $user->forceDelete();

        AuditLog::record('user.force_deleted', 'user', $userId, $email);

        return redirect()->route('admin.backup.index', ['tab' => 'recycle'])
            ->with('success', "User \"{$email}\" was permanently deleted.");
    }

    public function restoreCategory(int $id)
    {
        $category = ResearchCategory::onlyTrashed()->findOrFail($id);
        $category->restore();

        AuditLog::record('category.restored', 'category', $category->id, $category->name);

        return redirect()->route('admin.backup.index', ['tab' => 'recycle'])
            ->with('success', "Category \"{$category->name}\" has been restored.");
    }

    public function forceDeleteCategory(int $id)
    {
        $category = ResearchCategory::onlyTrashed()->findOrFail($id);

        $linkedCount = Research::withTrashed()->where('research_category_id', $category->id)->count()
            + DB::table('category_research')->where('research_category_id', $category->id)->count();

        if ($linkedCount > 0) {
            return redirect()->route('admin.backup.index', ['tab' => 'recycle'])
                ->with('error', "Cannot permanently delete category \"{$category->name}\" while researches (active or trashed) still reference it.");
        }

        $name = $category->name;
        $catId = $category->id;
        $category->forceDelete();

        AuditLog::record('category.force_deleted', 'category', $catId, $name);

        return redirect()->route('admin.backup.index', ['tab' => 'recycle'])
            ->with('success', "Category \"{$name}\" was permanently deleted.");
    }

    /**
     * Token-authenticated endpoint for automated off-site backup pulls (e.g. GitHub Actions cron).
     */
    public function automatedPull(Request $request, BackupService $backupService)
    {
        $configuredToken = trim((string) config('backup.automation_token', ''));
        if ($configuredToken === '') {
            abort(403, 'Automated backup token is not configured.');
        }

        $providedToken = (string) ($request->bearerToken() ?: $request->header('X-Backup-Token', ''));
        if ($providedToken === '' || !hash_equals($configuredToken, $providedToken)) {
            AuditLog::record('backup.automated_unauthorized', 'backup', null, 'automated-pull');
            abort(403, 'Unauthorized automated backup token.');
        }

        $result = $backupService->createBackupFile();

        AuditLog::record(
            'backup.automated',
            'backup',
            null,
            $result['filename'],
            [
                'source' => 'http_pull',
                'size_bytes' => $result['size'],
                'sha256' => $result['sha256'],
                'tables' => $result['tables'],
            ]
        );

        return response()->download($result['path'], $result['filename'], [
            'Content-Type' => 'application/octet-stream',
            'X-Backup-SHA256' => $result['sha256'],
        ])->deleteFileAfterSend(true);
    }
}

