<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResearchCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Legacy: researches with this as their primary category.
     */
    public function researches()
    {
        return $this->hasMany(Research::class);
    }

    /**
     * Many-to-many: researches classified under this category.
     */
    public function classifiedResearches()
    {
        return $this->belongsToMany(Research::class, 'category_research');
    }

    /**
     * Project types (subcategories) under this category.
     */
    public function projectTypes()
    {
        return $this->hasMany(ProjectType::class);
    }

    public function searchLogs()
    {
        return $this->hasMany(SearchLog::class, 'research_category_id');
    }
}
