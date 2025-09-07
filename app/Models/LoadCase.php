<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoadCase extends Model
{
    protected $table = 'load_cases';

    protected $fillable = [
        'model_id',
        'name',
        'type',
        'description'
    ];

    public function model()
    {
        return $this->belongsTo(StrcModel::class, 'model_id');
    }
}

