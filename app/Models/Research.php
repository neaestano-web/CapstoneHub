<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Research extends Model
{
    protected $table = 'researches';

    protected $fillable = [
        'user_id',
        'research_category_id',
        'title',
        'abstract',
        'project_context',
        'objectives',
        'keywords',
        'authors',
        'year',
        'pdf_path',
        'status',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Legacy: primary category (single FK).
     */
    public function category()
    {
        return $this->belongsTo(
            ResearchCategory::class,
            'research_category_id'
        );
    }

    /**
     * Many-to-many: all categories this research is classified under.
     */
    public function categories()
    {
        return $this->belongsToMany(
            ResearchCategory::class,
            'category_research'
        );
    }

    /**
     * Many-to-many: project types assigned to this research.
     */
    public function projectTypes()
    {
        return $this->belongsToMany(
            ProjectType::class,
            'project_type_research'
        );
    }

    public function embedding()
    {
        return $this->hasOne(
            ResearchEmbedding::class
        );
    }

    public function downloadRequests()
    {
        return $this->hasMany(
            DownloadRequest::class
        );
    }

    public function files()
    {
        return $this->hasMany(ResearchFile::class)->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
    }

    /**
     * Check if a research already exists by title (exact, normalized, or high similarity).
     *
     * @param string $title
     * @param int|null $excludeId
     * @return self|null
     */
    public static function findExistingByTitle(string $title, ?int $excludeId = null): ?self
    {
        $title = trim($title);
        if (mb_strlen($title) < 3) {
            return null;
        }

        $query = self::query();
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        // 1. Direct case-insensitive trimmed match (exact title)
        $exact = (clone $query)->whereRaw('LOWER(TRIM(title)) = ?', [mb_strtolower($title)])->first();
        if ($exact) {
            return $exact;
        }

        // 2. Normalized alphanumeric comparison (ignoring case, punctuation, multiple spaces)
        $cleanInput = preg_replace('/[^\p{L}\p{N}\s]/u', '', mb_strtolower($title));
        $cleanInput = preg_replace('/\s+/', ' ', trim($cleanInput ?? ''));

        if (!$cleanInput || mb_strlen($cleanInput) < 3) {
            return null;
        }

        // Fetch candidate researches for close inspection
        $candidates = (clone $query)->select('id', 'title', 'authors', 'year', 'status')->get();

        foreach ($candidates as $candidate) {
            $cleanCandidate = preg_replace('/[^\p{L}\p{N}\s]/u', '', mb_strtolower($candidate->title));
            $cleanCandidate = preg_replace('/\s+/', ' ', trim($cleanCandidate ?? ''));

            if ($cleanInput === $cleanCandidate) {
                return $candidate;
            }

            // Check high string similarity (85%+) for titles with length >= 12
            if (mb_strlen($cleanInput) >= 12 && mb_strlen($cleanCandidate) >= 12) {
                similar_text($cleanInput, $cleanCandidate, $percent);
                if ($percent >= 85) {
                    return $candidate;
                }
            }
        }

        return null;
    }
}
