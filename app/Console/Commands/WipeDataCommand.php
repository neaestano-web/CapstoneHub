<?php

namespace App\Console\Commands;

use App\Services\SystemCleanupService;
use Illuminate\Console\Command;

class WipeDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:wipe-data {--force : Force operation without asking for confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all projects and non-admin users, keeping only administrators for a fresh start.';

    /**
     * Execute the console command.
     */
    public function handle(SystemCleanupService $cleanup)
    {
        if (! $this->option('force')) {
            $confirm = $this->confirm(
                'Are you sure you want to delete ALL projects and ALL non-admin users? This cannot be undone.',
                false
            );

            if (! $confirm) {
                $this->warn('Operation cancelled.');
                return 0;
            }
        }

        $this->info('Starting repository wipe operation...');
        $result = $cleanup->wipeDataKeepAdmins();

        $this->newLine();
        $this->info('=== REPOSITORY DATA WIPED SUCCESSFULLY ===');
        $this->line("- Projects Deleted: {$result['researches']}");
        $this->line("- Research Files Deleted: {$result['files']}");
        $this->line("- Non-Admin Users Deleted: {$result['users']}");
        $this->line("- Download Requests Deleted: {$result['download_requests']}");
        $this->line("- Embeddings Deleted: {$result['embeddings']}");
        $this->info('Admin accounts and categories were preserved.');

        return 0;
    }
}
