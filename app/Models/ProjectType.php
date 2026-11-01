<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectType extends Model
{
    protected $fillable = [
        'research_category_id',
        'name',
        'description',
    ];

    public function category()
    {
        return $this->belongsTo(ResearchCategory::class, 'research_category_id');
    }

    public function researches()
    {
        return $this->belongsToMany(Research::class, 'project_type_research');
    }
}
