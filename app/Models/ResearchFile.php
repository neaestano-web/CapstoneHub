<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function research()
    {
        return $this->belongsTo(Research::class);
    }
}
