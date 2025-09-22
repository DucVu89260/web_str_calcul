<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Project;
use App\Models\Section;

class PreliminarySuggestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'suggested_section_id',
        'similarity_score',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array', // JSON to array
        'similarity_score' => 'double',
    ];

    /**
     * Get the project that owns the suggestion.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the suggested section.
     */
    public function section()
    {
        return $this->belongsTo(Section::class, 'suggested_section_id');
    }
}