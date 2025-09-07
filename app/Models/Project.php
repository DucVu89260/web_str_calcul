<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function models()
    {
        return $this->hasMany(StrcModel::class, 'project_id');
    }
}

