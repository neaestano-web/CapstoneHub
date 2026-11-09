<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResearchFileChunk extends Model
{
    protected $table = 'research_file_chunks';

    protected $fillable = [
        'research_file_id',
        'chunk_order',
        'chunk_data',
    ];

    public function file()
    {
        return $this->belongsTo(ResearchFile::class, 'research_file_id');
    }
}
