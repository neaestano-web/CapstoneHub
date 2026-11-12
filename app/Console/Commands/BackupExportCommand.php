<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Services\BackupService;
use Illuminate\Console\Command;

class BackupExportCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'backup:export
                            {--keep= : Maximum number of local snapshots to retain}
                            {--passphrase= : Optional custom encryption passphrase}';

    /**
     * The console command description.
     */
    protected $description = 'Create an encrypted, integrity-verified full system backup snapshot (.cshub)';

    public function handle(BackupService $backupService): int
    {
        $keep = $this->option('keep') !== null ? (int) $this->option('keep') : null;
        $passphrase = $this->option('passphrase');

        $this->info('Generating encrypted CapstoneHub backup snapshot...');

        try {
            $result = $backupService->createLocalScheduledBackup($passphrase, $keep);

            AuditLog::record(
                'backup.automated',
                'backup',
                null,
                $result['filename'],
                [
                    'source' => 'artisan',
                    'size_bytes' => $result['size'],
                    'sha256' => $result['sha256'],
                    'key_mode' => $result['key_mode'],
                    'tables' => $result['tables'],
                ]
            );

            $kb = number_format($result['size'] / 1024, 2);
            $this->info("Backup created successfully: {$result['path']} ({$kb} KB)");
            $this->line("SHA-256: {$result['sha256']}");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Backup failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}

