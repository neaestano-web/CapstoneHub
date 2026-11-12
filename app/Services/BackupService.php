<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class BackupService
{
    public const MAGIC_HEADER = 'CSHUB_BAK_V1';
    private const FRAME_RAW_BYTES = 524288; // 512 KB per encrypted frame
    private const MAX_STORAGE_FILE_BYTES = 26214400; // 25 MB cap per disk file

    /**
     * Create an encrypted, compressed, HMAC-authenticated full system backup (.cshub).
     *
     * @return array{path:string, filename:string, size:int, sha256:string, key_mode:string, tables:array<string,int>, storage_files_count:int, created_at:string}
     */
    public function createBackupFile(?string $passphrase = null, ?string $destinationPath = null): array
    {
        $tempDir = sys_get_temp_dir();
        $uid = Str::random(16);
        $gzTempPath = $tempDir . DIRECTORY_SEPARATOR . "cshub_payload_{$uid}.ndjson.gz";
        $encBodyTempPath = $tempDir . DIRECTORY_SEPARATOR . "cshub_body_{$uid}.bin";

        $createdAt = now()->toIso8601String();
        $filename = 'capstonehub_backup_' . now()->format('Y-m-d_His') . '.cshub';
        $outputPath = $destinationPath ?: ($tempDir . DIRECTORY_SEPARATOR . $filename);

        $outputDir = dirname($outputPath);
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $passphrase = $passphrase !== null ? trim($passphrase) : null;
        $usePassphrase = ($passphrase !== null && $passphrase !== '');
        $keyMode = $usePassphrase ? 'passphrase' : 'app_key';
        $salt = random_bytes(16);
        [$encKey, $macKey] = $this->deriveKeys($keyMode, $salt, $passphrase);

        $tableCounts = [];
        $storageFilesCount = 0;

        try {
            // 1. Stream database tables and storage files into gzipped NDJSON
            $gzHandle = gzopen($gzTempPath, 'wb6');
            if ($gzHandle === false) {
                throw new RuntimeException('Unable to open temporary gzip stream for backup.');
            }

            try {
                foreach ($this->configuredTables() as $table) {
                    if (!Schema::hasTable($table)) {
                        continue;
                    }

                    $count = 0;
                    $columns = Schema::getColumnListing($table);
                    $hasId = in_array('id', $columns, true);

                    if ($hasId) {
                        $chunkSize = match ($table) {
                            'research_file_chunks' => 5,
                            'research_files', 'users' => 10,
                            default => 100,
                        };

                        DB::table($table)->orderBy('id')->chunkById($chunkSize, function ($rows) use ($gzHandle, $table, &$count) {
                            foreach ($rows as $row) {
                                $line = json_encode([
                                    'type' => 'row',
                                    'table' => $table,
                                    'data' => (array) $row,
                                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . "\n";
                                gzwrite($gzHandle, $line);
                                $count++;
                            }
                        }, 'id');
                    } else {
                        foreach (DB::table($table)->cursor() as $row) {
                            $line = json_encode([
                                'type' => 'row',
                                'table' => $table,
                                'data' => (array) $row,
                            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . "\n";
                            gzwrite($gzHandle, $line);
                            $count++;
                        }
                    }

                    $tableCounts[$table] = $count;
                }

                // Stream critical disk files (legacy manuscripts & ID pictures not stored purely in DB)
                $storageTargets = [
                    ['disk' => 'public', 'prefix' => 'researches', 'exclude_prefix' => 'researches/pdf_cache'],
                    ['disk' => 'public', 'prefix' => 'id-pictures', 'exclude_prefix' => null],
                    ['disk' => 'local', 'prefix' => 'id-pictures', 'exclude_prefix' => null],
                ];

                foreach ($storageTargets as $target) {
                    $disk = Storage::disk($target['disk']);
                    if (!$disk->exists($target['prefix'])) {
                        continue;
                    }

                    foreach ($disk->allFiles($target['prefix']) as $relPath) {
                        if ($target['exclude_prefix'] && str_starts_with($relPath, $target['exclude_prefix'])) {
                            continue;
                        }

                        $fullPath = $disk->path($relPath);
                        if (!is_file($fullPath) || filesize($fullPath) > self::MAX_STORAGE_FILE_BYTES) {
                            continue;
                        }

                        $raw = file_get_contents($fullPath);
                        if ($raw === false) {
                            continue;
                        }

                        $line = json_encode([
                            'type' => 'storage',
                            'disk' => $target['disk'],
                            'path' => $relPath,
                            'data' => base64_encode($raw),
                        ], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . "\n";

                        gzwrite($gzHandle, $line);
                        $storageFilesCount++;
                    }
                }
            } finally {
                gzclose($gzHandle);
            }

            // 2. Build unsigned manifest
            $manifestBase = [
                'version' => 1,
                'app_name' => config('app.name', 'CapstoneHub'),
                'db_driver' => DB::connection()->getDriverName(),
                'created_at' => $createdAt,
                'key_mode' => $keyMode,
                'salt_hex' => bin2hex($salt),
                'tables' => $tableCounts,
                'storage_files_count' => $storageFilesCount,
            ];

            // 3. Encrypt payload into frames and compute incremental HMAC-SHA256
            $hmacCtx = hash_init('sha256', HASH_HMAC, $macKey);
            $canonicalManifest = $this->canonicalManifestJson($manifestBase);
            hash_update($hmacCtx, $canonicalManifest . "\n");

            $gzIn = fopen($gzTempPath, 'rb');
            $bodyOut = fopen($encBodyTempPath, 'wb');
            if ($gzIn === false || $bodyOut === false) {
                throw new RuntimeException('Unable to open encryption streams for backup.');
            }

            $frameCount = 0;
            try {
                while (!feof($gzIn)) {
                    $chunk = fread($gzIn, self::FRAME_RAW_BYTES);
                    if ($chunk === false || $chunk === '') {
                        break;
                    }

                    $iv = random_bytes(16);
                    $cipher = openssl_encrypt($chunk, 'aes-256-cbc', $encKey, OPENSSL_RAW_DATA, $iv);
                    if ($cipher === false) {
                        throw new RuntimeException('OpenSSL encryption failed while building backup.');
                    }

                    $frameHeader = pack('N', strlen($cipher)) . $iv;
                    fwrite($bodyOut, $frameHeader);
                    fwrite($bodyOut, $cipher);

                    hash_update($hmacCtx, $frameHeader);
                    hash_update($hmacCtx, $cipher);
                    $frameCount++;
                }
            } finally {
                fclose($gzIn);
                fclose($bodyOut);
            }

            // Include frame_count in final MAC binding as well
            hash_update($hmacCtx, pack('N', $frameCount));
            $hmac = hash_final($hmacCtx);

            $manifestFull = $manifestBase;
            $manifestFull['frame_count'] = $frameCount;
            $manifestFull['hmac_sha256'] = $hmac;

            // 4. Assemble final .cshub file
            $finalOut = fopen($outputPath, 'wb');
            $bodyIn = fopen($encBodyTempPath, 'rb');
            if ($finalOut === false || $bodyIn === false) {
                throw new RuntimeException('Unable to write final backup artifact.');
            }

            try {
                fwrite($finalOut, self::MAGIC_HEADER . "\n");
                fwrite($finalOut, json_encode($manifestFull, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . "\n");

                while (!feof($bodyIn)) {
                    $buf = fread($bodyIn, self::FRAME_RAW_BYTES);
                    if ($buf !== false && $buf !== '') {
                        fwrite($finalOut, $buf);
                    }
                }
            } finally {
                fclose($finalOut);
                fclose($bodyIn);
            }

            return [
                'path' => $outputPath,
                'filename' => basename($outputPath),
                'size' => filesize($outputPath) ?: 0,
                'sha256' => hash_file('sha256', $outputPath) ?: '',
                'key_mode' => $keyMode,
                'tables' => $tableCounts,
                'storage_files_count' => $storageFilesCount,
                'created_at' => $createdAt,
            ];
        } finally {
            @unlink($gzTempPath);
            @unlink($encBodyTempPath);
        }
    }

    /**
     * Verify header, cryptographic HMAC-SHA256 signature, and frame framing before restoring.
     *
     * @return array<string, mixed> Verified manifest array
     */
    public function inspectAndVerifyBackup(string $filePath, ?string $passphrase = null): array
    {
        if (!is_file($filePath) || !is_readable($filePath)) {
            throw new RuntimeException('Backup file could not be read.');
        }

        $handle = fopen($filePath, 'rb');
        if ($handle === false) {
            throw new RuntimeException('Unable to open backup file.');
        }

        try {
            $magic = rtrim((string) fgets($handle, 64), "\r\n");
            if ($magic !== self::MAGIC_HEADER) {
                throw new RuntimeException('Invalid backup file format. Expected a valid CapstoneHub (.cshub) backup.');
            }

            $manifestLine = fgets($handle, 1048576);
            if ($manifestLine === false) {
                throw new RuntimeException('Backup manifest is missing or corrupted.');
            }

            $manifest = json_decode(trim($manifestLine), true);
            if (!is_array($manifest) || empty($manifest['hmac_sha256']) || empty($manifest['salt_hex']) || !isset($manifest['frame_count'])) {
                throw new RuntimeException('Backup manifest metadata is invalid or incomplete.');
            }

            $keyMode = (string) ($manifest['key_mode'] ?? 'app_key');
            $passphrase = $passphrase !== null ? trim($passphrase) : null;
            if ($keyMode === 'passphrase' && ($passphrase === null || $passphrase === '')) {
                throw new RuntimeException('This backup was encrypted with a custom passphrase. Please provide the recovery passphrase.');
            }

            $salt = @hex2bin((string) $manifest['salt_hex']);
            if ($salt === false || strlen($salt) !== 16) {
                throw new RuntimeException('Backup cryptographic salt is invalid.');
            }

            [, $macKey] = $this->deriveKeys($keyMode, $salt, $passphrase);

            $manifestBase = [
                'version' => $manifest['version'] ?? 1,
                'app_name' => $manifest['app_name'] ?? 'CapstoneHub',
                'db_driver' => $manifest['db_driver'] ?? '',
                'created_at' => $manifest['created_at'] ?? '',
                'key_mode' => $keyMode,
                'salt_hex' => $manifest['salt_hex'],
                'tables' => $manifest['tables'] ?? [],
                'storage_files_count' => $manifest['storage_files_count'] ?? 0,
            ];

            $hmacCtx = hash_init('sha256', HASH_HMAC, $macKey);
            hash_update($hmacCtx, $this->canonicalManifestJson($manifestBase) . "\n");

            $expectedFrames = (int) $manifest['frame_count'];
            $actualFrames = 0;

            while (!feof($handle)) {
                $lenBin = fread($handle, 4);
                if ($lenBin === false || $lenBin === '') {
                    break;
                }
                if (strlen($lenBin) !== 4) {
                    throw new RuntimeException('Backup file appears truncated or tampered with.');
                }

                $unpacked = unpack('Nlen', $lenBin);
                $cipherLen = (int) ($unpacked['len'] ?? 0);
                if ($cipherLen <= 0 || $cipherLen > (self::FRAME_RAW_BYTES + 1024)) {
                    throw new RuntimeException('Backup frame header is corrupted.');
                }

                $iv = fread($handle, 16);
                if ($iv === false || strlen($iv) !== 16) {
                    throw new RuntimeException('Backup initialization vector is truncated.');
                }

                $cipher = $this->freadExact($handle, $cipherLen);
                if (strlen($cipher) !== $cipherLen) {
                    throw new RuntimeException('Backup encrypted payload is truncated.');
                }

                hash_update($hmacCtx, $lenBin . $iv);
                hash_update($hmacCtx, $cipher);
                $actualFrames++;
            }

            hash_update($hmacCtx, pack('N', $actualFrames));
            $computedHmac = hash_final($hmacCtx);

            if (!hash_equals((string) $manifest['hmac_sha256'], $computedHmac) || $actualFrames !== $expectedFrames) {
                throw new RuntimeException('Integrity verification failed: wrong passphrase/APP_KEY or the backup file has been modified/corrupted.');
            }

            $manifest['payload_offset'] = strlen(self::MAGIC_HEADER . "\n") + strlen($manifestLine);

            return $manifest;
        } finally {
            fclose($handle);
        }
    }

    /**
     * Decrypt, verify, and transactionally restore database tables and storage files from a .cshub backup.
     *
     * @return array{manifest:array<string,mixed>, restored_tables:array<string,int>, restored_files:int}
     */
    public function restoreFromBackup(string $filePath, ?string $passphrase = null): array
    {
        $manifest = $this->inspectAndVerifyBackup($filePath, $passphrase);

        $tempDir = sys_get_temp_dir();
        $uid = Str::random(16);
        $gzTempPath = $tempDir . DIRECTORY_SEPARATOR . "cshub_restore_{$uid}.ndjson.gz";

        $salt = hex2bin((string) $manifest['salt_hex']);
        $keyMode = (string) ($manifest['key_mode'] ?? 'app_key');
        $passphrase = $passphrase !== null ? trim($passphrase) : null;
        [$encKey] = $this->deriveKeys($keyMode, $salt, $passphrase);

        $inHandle = fopen($filePath, 'rb');
        $gzOut = fopen($gzTempPath, 'wb');
        if ($inHandle === false || $gzOut === false) {
            if (is_resource($inHandle)) {
                fclose($inHandle);
            }
            if (is_resource($gzOut)) {
                fclose($gzOut);
            }
            throw new RuntimeException('Unable to open streams for backup decryption.');
        }

        try {
            // Skip magic + manifest lines
            fgets($inHandle, 64);
            fgets($inHandle, 1048576);

            while (!feof($inHandle)) {
                $lenBin = fread($inHandle, 4);
                if ($lenBin === false || $lenBin === '') {
                    break;
                }
                $cipherLen = (int) (unpack('Nlen', $lenBin)['len'] ?? 0);
                $iv = fread($inHandle, 16);
                $cipher = $this->freadExact($inHandle, $cipherLen);

                $plainChunk = openssl_decrypt($cipher, 'aes-256-cbc', $encKey, OPENSSL_RAW_DATA, $iv);
                if ($plainChunk === false) {
                    throw new RuntimeException('Decryption failed while unpacking backup payload.');
                }

                fwrite($gzOut, $plainChunk);
            }
        } finally {
            fclose($inHandle);
            fclose($gzOut);
        }

        $restoredCounts = [];
        $storageEntriesToRestore = [];

        try {
            $tablesOrder = $this->configuredTables();
            $existingColumns = [];
            foreach ($tablesOrder as $t) {
                if (Schema::hasTable($t)) {
                    $existingColumns[$t] = array_flip(Schema::getColumnListing($t));
                }
            }

            // Disable FK checks OUTSIDE the transaction so SQLite & MySQL both honor it
            Schema::disableForeignKeyConstraints();

            try {
                DB::transaction(function () use ($gzTempPath, $tablesOrder, $existingColumns, &$restoredCounts, &$storageEntriesToRestore) {
                    // Delete existing records in reverse dependency order (DELETE keeps MySQL ACID transaction intact unlike TRUNCATE)
                    foreach (array_reverse($tablesOrder) as $table) {
                        if (isset($existingColumns[$table])) {
                            DB::table($table)->delete();
                        }
                    }

                    $gzRead = gzopen($gzTempPath, 'rb');
                    if ($gzRead === false) {
                        throw new RuntimeException('Unable to decompress backup payload.');
                    }

                    $buffers = [];
                    $currentTable = null;

                    $flushTable = function (string $tableName) use (&$buffers, &$restoredCounts) {
                        if (empty($buffers[$tableName])) {
                            return;
                        }
                        DB::table($tableName)->insert($buffers[$tableName]);
                        $restoredCounts[$tableName] = ($restoredCounts[$tableName] ?? 0) + count($buffers[$tableName]);
                        $buffers[$tableName] = [];
                    };

                    try {
                        while (!gzeof($gzRead)) {
                            $line = gzgets($gzRead);
                            if ($line === false || trim($line) === '') {
                                continue;
                            }

                            $entry = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
                            $type = $entry['type'] ?? '';

                            if ($type === 'row') {
                                $table = (string) ($entry['table'] ?? '');
                                $rowData = (array) ($entry['data'] ?? []);
                                if (!isset($existingColumns[$table])) {
                                    continue;
                                }

                                // Filter strictly to columns present in current schema
                                $filtered = array_intersect_key($rowData, $existingColumns[$table]);
                                if (empty($filtered)) {
                                    continue;
                                }

                                // Flush previous parent table before inserting child rows
                                if ($currentTable !== null && $currentTable !== $table) {
                                    $flushTable($currentTable);
                                }
                                $currentTable = $table;

                                $buffers[$table][] = $filtered;

                                $batchLimit = in_array($table, ['research_file_chunks', 'research_files', 'users'], true) ? 1 : 50;
                                if (count($buffers[$table]) >= $batchLimit) {
                                    $flushTable($table);
                                }
                            } elseif ($type === 'storage') {
                                $storageEntriesToRestore[] = $entry;
                            }
                        }

                        foreach (array_keys($buffers) as $tableName) {
                            $flushTable($tableName);
                        }
                    } finally {
                        gzclose($gzRead);
                    }
                });
            } finally {
                Schema::enableForeignKeyConstraints();
            }

            // Restore disk storage files after DB commit succeeds
            $restoredFiles = 0;
            foreach ($storageEntriesToRestore as $entry) {
                $diskName = (string) ($entry['disk'] ?? 'public');
                $relPath = ltrim(str_replace(['..', '\\'], ['', '/'], (string) ($entry['path'] ?? '')), '/');
                $b64 = (string) ($entry['data'] ?? '');

                if ($relPath === '' || $b64 === '' || !in_array($diskName, ['public', 'local'], true)) {
                    continue;
                }

                $decoded = base64_decode($b64, true);
                if ($decoded !== false) {
                    Storage::disk($diskName)->put($relPath, $decoded);
                    $restoredFiles++;
                }
            }

            return [
                'manifest' => $manifest,
                'restored_tables' => $restoredCounts,
                'restored_files' => $restoredFiles,
            ];
        } finally {
            @unlink($gzTempPath);
        }
    }

    /**
     * Save a scheduled local backup inside storage/app/backups and enforce retention count.
     */
    public function createLocalScheduledBackup(?string $passphrase = null, ?int $keep = null): array
    {
        $keepCount = $keep ?? max(1, (int) config('backup.retention_count', 7));
        $dir = storage_path('app/backups');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = 'capstonehub_auto_' . now()->format('Y-m-d_His') . '.cshub';
        $result = $this->createBackupFile($passphrase, $dir . DIRECTORY_SEPARATOR . $filename);

        // Prune old backups exceeding retention count
        $files = glob($dir . DIRECTORY_SEPARATOR . '*.cshub') ?: [];
        if (count($files) > $keepCount) {
            usort($files, fn ($a, $b) => filemtime($b) <=> filemtime($a));
            foreach (array_slice($files, $keepCount) as $oldFile) {
                @unlink($oldFile);
            }
        }

        return $result;
    }

    /**
     * Gather current system health and backup scope metrics for the Admin UI.
     */
    public function getSystemBackupOverview(): array
    {
        $counts = [];
        foreach ($this->configuredTables() as $table) {
            if (Schema::hasTable($table)) {
                $counts[$table] = DB::table($table)->count();
            }
        }

        return [
            'tables' => $counts,
            'total_records' => array_sum($counts),
            'db_driver' => DB::connection()->getDriverName(),
            'automation_configured' => trim((string) config('backup.automation_token', '')) !== '',
            'dedicated_secret_configured' => trim((string) config('backup.secret', '')) !== '',
        ];
    }

    /**
     * @return array<int, string>
     */
    private function configuredTables(): array
    {
        return config('backup.tables', [
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
        ]);
    }

    /**
     * Derive separate 32-byte AES-256 encryption key and 32-byte HMAC-SHA256 authentication key.
     *
     * @return array{0:string, 1:string}
     */
    private function deriveKeys(string $keyMode, string $salt, ?string $passphrase): array
    {
        if ($keyMode === 'passphrase') {
            $ikm = hash_pbkdf2('sha256', (string) $passphrase, $salt, 100000, 32, true);
        } else {
            $rawSecret = trim((string) config('backup.secret', ''));
            if ($rawSecret === '') {
                $rawSecret = (string) config('app.key', '');
            }

            if (str_starts_with($rawSecret, 'base64:')) {
                $decoded = base64_decode(substr($rawSecret, 7), true);
                $rawSecret = $decoded !== false ? $decoded : $rawSecret;
            }

            if ($rawSecret === '') {
                throw new RuntimeException('No application encryption key (APP_KEY) or BACKUP_SECRET is configured.');
            }

            $ikm = hash_hmac('sha256', $rawSecret, $salt, true);
        }

        $encKey = hash_hkdf('sha256', $ikm, 32, 'capstonehub-backup-aes256-enc', $salt);
        $macKey = hash_hkdf('sha256', $ikm, 32, 'capstonehub-backup-hmac-sha256', $salt);

        return [$encKey, $macKey];
    }

    private function canonicalManifestJson(array $manifestBase): string
    {
        return json_encode($manifestBase, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }

    private function freadExact($handle, int $length): string
    {
        $buffer = '';
        while (strlen($buffer) < $length && !feof($handle)) {
            $chunk = fread($handle, $length - strlen($buffer));
            if ($chunk === false || $chunk === '') {
                break;
            }
            $buffer .= $chunk;
        }
        return $buffer;
    }
}

