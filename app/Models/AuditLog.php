<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'actor_email',
        'action',
        'target_type',
        'target_id',
        'target_label',
        'ip_address',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * Safely record a backup, recovery, deletion, or security audit event.
     */
    public static function record(
        string $action,
        ?string $targetType = null,
        ?int $targetId = null,
        ?string $targetLabel = null,
        array $details = [],
        ?User $actor = null
    ): ?self {
        try {
            if (!Schema::hasTable('audit_logs')) {
                return null;
            }

            $user = $actor ?? Auth::user();
            $request = app()->bound('request') ? request() : null;

            return self::create([
                'user_id' => $user?->id,
                'actor_email' => $user?->email ?? ($details['actor'] ?? 'system'),
                'action' => $action,
                'target_type' => $targetType,
                'target_id' => $targetId,
                'target_label' => $targetLabel ? mb_substr($targetLabel, 0, 255) : null,
                'ip_address' => $request?->ip(),
                'details' => empty($details) ? null : $details,
            ]);
        } catch (\Throwable $e) {
            Log::warning('AuditLog recording failed: ' . $e->getMessage());
            return null;
        }
    }
}

