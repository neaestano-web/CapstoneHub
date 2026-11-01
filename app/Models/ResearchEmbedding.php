<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResearchEmbedding extends Model
{
    protected $fillable = [
        'research_id',
        'title_embedding',
        'content_embedding',
        'objective_embedding',
        'context_embedding',
    ];
    public function research()
    {
        return $this->belongsTo(
            Research::class
        );
    }
}
