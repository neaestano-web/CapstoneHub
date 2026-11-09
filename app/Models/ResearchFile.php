<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ResearchFile extends Model
{
    protected $table = 'research_files';

    protected $fillable = [
        'research_id',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'sort_order',
        'file_data',
    ];

    protected static function booted()
    {
        static::deleting(function ($file) {
            try {
                $file->chunks()->delete();
            } catch (\Throwable $e) {
                // Ignore cleanup error if table does not exist
            }
        });
    }

    public function research()
    {
        return $this->belongsTo(Research::class);
    }

    public function chunks()
    {
        return $this->hasMany(ResearchFileChunk::class, 'research_file_id')->orderBy('chunk_order', 'asc');
    }

    /**
     * Safely store file content into small ~1MB base64 database chunks.
     * Uses stream reading to avoid memory exhaustion and strictly stays below TiDB's 6MB entry limit.
     */
    public function storeChunksFromFile(string $filePath): void
    {
        try {
            if (!file_exists($filePath)) {
                return;
            }

            // If file is very large (> 25MB), skip DB backup to avoid high DB storage/quota usage
            if (filesize($filePath) > 25 * 1024 * 1024) {
                return;
            }

            $handle = fopen($filePath, 'rb');
            if (!$handle) {
                return;
            }

            // Delete old chunks if updating
            $this->chunks()->delete();

            // 768,000 raw bytes produces exactly 1,024,000 chars of base64 (multiple of 3, no padding, well under 6MB TiDB limit)
            $chunkSize = 768000;
            $order = 0;

            while (!feof($handle)) {
                $raw = fread($handle, $chunkSize);
                if ($raw === false || strlen($raw) === 0) {
                    break;
                }
                $encodedChunk = base64_encode($raw);
                $this->chunks()->create([
                    'chunk_order' => $order++,
                    'chunk_data'  => $encodedChunk,
                ]);
            }
            fclose($handle);
        } catch (\Throwable $e) {
            Log::warning("Could not backup file {$this->id} to DB chunks: " . $e->getMessage());
        }
    }

    /**
     * Restore physical file to local disk if container restarted and wiped local storage.
     * Decodes chunks stream-by-stream directly to disk for low memory consumption.
     */
    public function restoreToDisk(): bool
    {
        try {
            if (empty($this->file_path)) {
                return false;
            }

            $chunks = $this->chunks()->orderBy('chunk_order', 'asc')->get();
            if ($chunks->isNotEmpty()) {
                $absPath = Storage::disk('public')->path($this->file_path);
                $dir = dirname($absPath);
                if (!is_dir($dir)) {
                    @mkdir($dir, 0755, true);
                }

                $outHandle = fopen($absPath, 'wb');
                if ($outHandle) {
                    foreach ($chunks as $chunk) {
                        $raw = base64_decode($chunk->chunk_data);
                        if ($raw !== false && strlen($raw) > 0) {
                            fwrite($outHandle, $raw);
                        }
                    }
                    fclose($outHandle);
                    return file_exists($absPath) && filesize($absPath) > 0;
                }
            } elseif (!empty($this->file_data)) {
                // Backward compatibility for files saved before chunking
                $content = base64_decode($this->file_data);
                if ($content !== false && strlen($content) > 0) {
                    Storage::disk('public')->put($this->file_path, $content);
                    return true;
                }
            }
        } catch (\Throwable $e) {
            Log::warning("Failed to restore file {$this->id} from DB: " . $e->getMessage());
        }

        return false;
    }
}
