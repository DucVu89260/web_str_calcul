<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\StrcModel;
use App\Models\ProjectParameter;
use App\Models\PreliminarySuggestion;
use App\Models\Section;
use App\Models\Material;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'default_standard_id',
        'name',
        'description',
        'default_unit_system',
        'visibility',
        'settings',
        'preliminary_status', // Từ migration mới
        'preliminary_meta',   // Từ migration mới
    ];

    protected $casts = [
        'settings' => 'array',
        'preliminary_meta' => 'array',
        'default_unit_system' => 'string',
        'visibility' => 'string',
        'preliminary_status' => 'string',
    ];

    /**
     * Get the models for the project.
     */
    public function models()
    {
        return $this->hasMany(StrcModel::class, 'project_id');
    }

    /**
     * Get the parameters for the project.
     */
    public function parameters()
    {
        return $this->hasOne(ProjectParameter::class);
    }

    /**
     * Get the preliminary suggestions for the project.
     */
    public function preliminarySuggestions()
    {
        return $this->hasMany(PreliminarySuggestion::class);
    }

    /**
     * Get the sections for the project.
     */
    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    /**
     * Get the materials for the project.
     */
    public function materials()
    {
        return $this->hasMany(Material::class); // Giả sử có Model Material
    }

    /**
     * Scope to get projects with preliminary suggestions.
     */
    public function scopeWithSuggestions($query)
    {
        return $query->whereHas('preliminarySuggestions');
    }
}