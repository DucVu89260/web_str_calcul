<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $table = 'results';

    protected $fillable = ['analysis_run_id', 'summary'];

    protected $casts = ['summary' => 'array'];

    public function analysisRun()
    {
        return $this->belongsTo(AnalysisRun::class);
    }

    public function items()
    {
        return $this->hasMany(ResultItem::class);
    }
}