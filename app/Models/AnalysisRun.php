<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalysisRun extends Model
{
    protected $table = 'analysis_runs';

    protected $fillable = [
        'project_id', 'model_id', 'initiated_by', 'runner', 'standard_id', 'status',
        'started_at', 'finished_at', 'result_summary', 'result_file_id', 'error_log', 'meta'
    ];

    protected $casts = [
        'result_summary' => 'array',
        'meta' => 'array',
    ];

    public function model()
    {
        return $this->belongsTo(StrcModel::class, 'model_id');
    }
}