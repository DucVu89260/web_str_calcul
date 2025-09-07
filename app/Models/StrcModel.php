<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StrcModel extends Model
{
    protected $table = 'models';

    protected $fillable = ['project_id', 'name', 'description'];

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
}

