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
        return $this->hasMany(ResearchFile::class)->orderBy('sort_order', 'asc');
    }
}
