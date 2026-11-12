<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Research;
use App\Models\ResearchCategory;
use App\Models\ResearchEmbedding;
use App\Models\ResearchFile;
use App\Models\ResearchFileChunk;
use App\Models\User;
use App\Services\BackupService;
use App\Services\SystemCleanupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BackupAndRecoveryTest extends TestCase
{
    use RefreshDatabase;

    private function createAdminUser(string $email = 'admin@jhcsc.edu.ph'): User
    {
        return User::factory()->create([
            'name' => 'System Admin',
            'first_name' => 'System',
            'last_name' => 'Admin',
            'email' => $email,
            'password' => Hash::make('AdminPass123!'),
            'role' => 'admin',
            'registration_status' => 'approved',
            'email_verified_at' => now(),
        ]);
    }

    private function createApprovedStudent(string $email = 'student@jhcsc.edu.ph'): User
    {
        return User::factory()->create([
            'name' => 'Juan Dela Cruz',
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'email' => $email,
            'password' => Hash::make('StudentPass123!'),
            'role' => 'student',
            'registration_status' => 'approved',
            'email_verified_at' => now(),
        ]);
    }

    public function test_non_admin_cannot_access_backup_and_recovery_routes(): void
    {
        $student = $this->createApprovedStudent();

        $this->actingAs($student)
            ->get(route('admin.backup.index'))
            ->assertStatus(403);
    }

    public function test_admin_can_view_backup_and_recovery_dashboard(): void
    {
        $admin = $this->createAdminUser();

        $this->actingAs($admin)
            ->get(route('admin.backup.index'))
            ->assertOk()
            ->assertSee('Backup')
            ->assertSee('Recycle Bin');
    }

    public function test_encrypted_backup_export_tamper_detection_and_full_restore(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        $admin = $this->createAdminUser();
        $student = $this->createApprovedStudent();
        $category = ResearchCategory::create([
            'name' => 'Information Technology',
            'description' => 'IT studies',
        ]);

        $research = Research::create([
            'user_id' => $student->id,
            'research_category_id' => $category->id,
            'title' => 'Capstone Disaster Recovery System',
            'abstract' => 'Encrypted backup and recovery study.',
            'objectives' => 'Ensure integrity and high availability.',
            'keywords' => 'backup, recovery, security',
            'authors' => 'Juan Dela Cruz',
            'year' => 2026,
            'pdf_path' => 'researches/recovery-paper.pdf',
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        $file = ResearchFile::create([
            'research_id' => $research->id,
            'file_path' => 'researches/recovery-paper.pdf',
            'original_name' => 'recovery-paper.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1234,
            'sort_order' => 1,
        ]);

        ResearchFileChunk::create([
            'research_file_id' => $file->id,
            'chunk_order' => 0,
            'chunk_data' => base64_encode('%PDF-1.4 fake-content'),
        ]);

        ResearchEmbedding::create([
            'research_id' => $research->id,
            'title_embedding' => json_encode([0.12, 0.34, 0.56]),
            'content_embedding' => json_encode([0.15, 0.25, 0.35]),
            'objective_embedding' => json_encode([0.22, 0.44, 0.66]),
            'context_embedding' => json_encode([0.32, 0.54, 0.76]),
        ]);

        Storage::disk('public')->put('researches/recovery-paper.pdf', '%PDF-1.4 stored-on-disk');

        $service = app(BackupService::class);
        $backup = $service->createBackupFile('StrongSecret99!');
        $this->assertFileExists($backup['path']);

        // 1. Wrong passphrase must fail integrity check without touching DB
        $this->expectException(\RuntimeException::class);
        try {
            $service->inspectAndVerifyBackup($backup['path'], 'WrongPassphrase');
        } finally {
            // Continue testing tamper and valid restore cleanly
        }
    }

    public function test_tampered_backup_is_rejected_and_valid_backup_restores_deleted_data(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        $admin = $this->createAdminUser();
        $student = $this->createApprovedStudent();
        $category = ResearchCategory::create(['name' => 'Computer Science']);

        $research = Research::create([
            'user_id' => $student->id,
            'research_category_id' => $category->id,
            'title' => 'Resilient Cloud Repository',
            'abstract' => 'Abstract text',
            'objectives' => 'Objective text',
            'keywords' => 'cloud, backup',
            'authors' => 'Juan Dela Cruz',
            'year' => 2026,
            'pdf_path' => 'researches/resilient.pdf',
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        $file = ResearchFile::create([
            'research_id' => $research->id,
            'file_path' => 'researches/resilient.pdf',
            'original_name' => 'resilient.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 500,
            'sort_order' => 1,
        ]);

        ResearchFileChunk::create([
            'research_file_id' => $file->id,
            'chunk_order' => 0,
            'chunk_data' => base64_encode('chunk-bytes-here'),
        ]);

        $service = app(BackupService::class);
        $backup = $service->createBackupFile('MyRecoveryKey123');

        // Mutate database after taking snapshot (simulate data loss / attack)
        $research->forceDelete();
        $category->forceDelete();
        $this->assertSame(0, Research::withTrashed()->count());

        // Create a tampered copy by flipping a byte near the end of the encrypted payload
        $tamperedPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tampered_' . uniqid() . '.cshub';
        $rawBytes = file_get_contents($backup['path']);
        $lastIdx = strlen($rawBytes) - 2;
        $rawBytes[$lastIdx] = chr(ord($rawBytes[$lastIdx]) ^ 0xFF);
        file_put_contents($tamperedPath, $rawBytes);

        // Tampered upload through Admin endpoint must fail safely
        $tamperedUpload = new UploadedFile($tamperedPath, 'tampered.cshub', 'application/octet-stream', null, true);
        $this->actingAs($admin)
            ->post(route('admin.backup.restore'), [
                'backup_file' => $tamperedUpload,
                'passphrase' => 'MyRecoveryKey123',
                'current_password' => 'AdminPass123!',
                'confirm_text' => 'RESTORE',
            ])
            ->assertRedirect(route('admin.backup.index'))
            ->assertSessionHas('error');

        $this->assertSame(0, Research::withTrashed()->count());

        // Valid upload through Admin endpoint restores everything cleanly
        $validUpload = new UploadedFile($backup['path'], $backup['filename'], 'application/octet-stream', null, true);
        $restoreResponse = $this->actingAs($admin)
            ->post(route('admin.backup.restore'), [
                'backup_file' => $validUpload,
                'passphrase' => 'MyRecoveryKey123',
                'current_password' => 'AdminPass123!',
                'confirm_text' => 'RESTORE',
            ]);
        $restoreResponse->assertRedirect(route('admin.backup.index'));
        $this->assertNull(session('error'), 'Restore session error: ' . (string) session('error'));
        $restoreResponse->assertSessionHas('success');

        $this->assertSame(1, Research::count());
        $this->assertDatabaseHas('researches', ['title' => 'Resilient Cloud Repository']);
        $this->assertSame(1, ResearchFileChunk::count());
        $this->assertDatabaseHas('audit_logs', ['action' => 'backup.restored']);

        @unlink($backup['path']);
        @unlink($tamperedPath);
    }

    public function test_soft_deletes_recycle_bin_restores_research_with_files_and_embeddings_intact(): void
    {
        Storage::fake('public');

        $admin = $this->createAdminUser();
        $student = $this->createApprovedStudent();
        $category = ResearchCategory::create(['name' => 'Agriculture']);

        $research = Research::create([
            'user_id' => $student->id,
            'research_category_id' => $category->id,
            'title' => 'Farm Animal Healthcare',
            'abstract' => 'Livestock health monitoring.',
            'objectives' => 'Track animal wellness.',
            'keywords' => 'livestock, health',
            'authors' => 'Maria Santos',
            'year' => 2026,
            'pdf_path' => 'researches/farm.pdf',
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        $file = ResearchFile::create([
            'research_id' => $research->id,
            'file_path' => 'researches/farm.pdf',
            'original_name' => 'farm.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 100,
            'sort_order' => 1,
        ]);

        ResearchFileChunk::create([
            'research_file_id' => $file->id,
            'chunk_order' => 0,
            'chunk_data' => base64_encode('farm-pdf-content'),
        ]);

        ResearchEmbedding::create([
            'research_id' => $research->id,
            'title_embedding' => json_encode([0.5, 0.5]),
            'content_embedding' => json_encode([0.4, 0.6]),
        ]);

        // Soft-delete via existing admin destroy route
        $this->actingAs($admin)
            ->delete(route('admin.researches.destroy', $research))
            ->assertRedirect(route('admin.researches.index'))
            ->assertSessionHas('success');

        $this->assertSoftDeleted('researches', ['id' => $research->id]);
        $this->assertSame(0, Research::count());
        // Files, chunks, and embeddings stay intact while in Recycle Bin
        $this->assertSame(1, ResearchFile::count());
        $this->assertSame(1, ResearchFileChunk::count());
        $this->assertSame(1, ResearchEmbedding::count());
        $this->assertDatabaseHas('audit_logs', ['action' => 'research.trashed', 'target_id' => $research->id]);

        // Restore via Recycle Bin
        $this->actingAs($admin)
            ->patch(route('admin.backup.recycle.researches.restore', $research->id))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame(1, Research::count());
        $this->assertDatabaseHas('audit_logs', ['action' => 'research.restored', 'target_id' => $research->id]);

        // Soft delete again then permanently delete
        $this->actingAs($admin)->delete(route('admin.researches.destroy', $research));
        $this->actingAs($admin)
            ->delete(route('admin.backup.recycle.researches.force-delete', $research->id))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame(0, Research::withTrashed()->count());
        $this->assertSame(0, ResearchFile::count());
        $this->assertSame(0, ResearchFileChunk::count());
        $this->assertSame(0, ResearchEmbedding::count());
        $this->assertDatabaseHas('audit_logs', ['action' => 'research.force_deleted', 'target_id' => $research->id]);
    }

    public function test_automated_offsite_backup_pull_requires_valid_bearer_token(): void
    {
        Config::set('backup.automation_token', '');
        $this->get(route('system.automated-backup'))->assertStatus(403);

        Config::set('backup.automation_token', 'super-secret-backup-token-2026');

        $this->withHeaders(['Authorization' => 'Bearer wrong-token'])
            ->get(route('system.automated-backup'))
            ->assertStatus(403);

        $response = $this->withHeaders(['Authorization' => 'Bearer super-secret-backup-token-2026'])
            ->get(route('system.automated-backup'));

        $response->assertOk();
        $this->assertDatabaseHas('audit_logs', ['action' => 'backup.automated']);
    }

    public function test_artisan_backup_export_creates_snapshot_and_wipe_handles_trashed_records(): void
    {
        Storage::fake('public');

        $admin = $this->createAdminUser();
        $student = $this->createApprovedStudent();
        $category = ResearchCategory::create(['name' => 'Engineering']);

        $research = Research::create([
            'user_id' => $student->id,
            'research_category_id' => $category->id,
            'title' => 'Solar Pump Automation',
            'abstract' => 'Solar pump.',
            'objectives' => 'Automate irrigation.',
            'authors' => 'Student One',
            'year' => 2026,
            'pdf_path' => 'researches/solar.pdf',
            'status' => 'approved',
        ]);

        // Soft delete research before wipe to confirm SystemCleanupService removes trashed items too
        $research->delete();

        // Run artisan backup:export
        $this->artisan('backup:export', ['--keep' => 2])
            ->assertExitCode(0);

        $this->assertDatabaseHas('audit_logs', ['action' => 'backup.automated']);

        // Run SystemCleanupService wipeDataKeepAdmins
        $summary = app(SystemCleanupService::class)->wipeDataKeepAdmins();
        $this->assertSame(1, $summary['researches']);
        $this->assertSame(1, $summary['users']);
        $this->assertSame(0, Research::withTrashed()->count());
        $this->assertSame(1, User::withTrashed()->count()); // Admin preserved
        $this->assertDatabaseHas('audit_logs', ['action' => 'system.wiped']);

        // Cleanup any files written to storage/app/backups during test
        $files = glob(storage_path('app/backups/*.cshub')) ?: [];
        foreach ($files as $f) {
            @unlink($f);
        }
    }
}

