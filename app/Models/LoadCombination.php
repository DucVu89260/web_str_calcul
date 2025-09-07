<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoadCombination extends Model
{
    protected $table = 'load_combinations';

    protected $fillable = [
        'model_id',
        'name',
        'combination'
    ];

    protected $casts = [
        'combination' => 'array',
    ];

    public function model()
    {
        return $this->belongsTo(StrcModel::class, 'model_id');
    }
}

