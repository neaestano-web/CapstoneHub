<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Automated Off-Site Backup Token
    |--------------------------------------------------------------------------
    |
    | Used to authenticate automated backup pulls (e.g. GitHub Actions daily
    | cron) against GET /system/automated-backup. Leave empty to disable the
    | automated HTTP pull endpoint.
    |
    */
    'automation_token' => env('BACKUP_AUTOMATION_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | Dedicated Backup Encryption Secret
    |--------------------------------------------------------------------------
    |
    | Optional key used when exporting encrypted backups without a custom
    | passphrase. Falls back to APP_KEY if not explicitly provided.
    |
    */
    'secret' => env('BACKUP_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | Local Backup Retention
    |--------------------------------------------------------------------------
    |
    | Number of automated local backup snapshots retained by `php artisan
    | backup:export` before pruning older files.
    |
    */
    'retention_count' => (int) env('BACKUP_RETENTION_COUNT', 7),

    /*
    |--------------------------------------------------------------------------
    | Core Database Tables (Ordered for Safe Parent-First Restore)
    |--------------------------------------------------------------------------
    */
    'tables' => [
        'users',
        'password_reset_tokens',
        'otp_codes',
        'research_categories',
        'project_types',
        'researches',
        'category_research',
        'project_type_research',
        'research_embeddings',
        'research_files',
        'research_file_chunks',
        'download_requests',
        'search_logs',
        'audit_logs',
    ],
];

