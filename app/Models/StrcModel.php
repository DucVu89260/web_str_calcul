<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StrcModel extends Model
{
    protected $table = 'models';

    protected $fillable = [
        'project_id',
        'name',
        'description',
        'file_path',
        'snapshot_file_id',
        'version',
        'status',
        'snapshot_meta'
    ];

    protected $casts = [
        'snapshot_meta' => 'array',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function loadCases()
    {
        return $this->hasMany(LoadCase::class, 'model_id');
    }

    public function loadCombinations()
    {
        return $this->hasMany(LoadCombination::class, 'model_id');
    }

    public function analysisRuns()
    {
        return $this->hasMany(AnalysisRun::class, 'model_id');
    }

    public function latestRun()
    {
        return $this->hasOne(AnalysisRun::class, 'model_id')->latestOfMany('started_at');
    }
}
