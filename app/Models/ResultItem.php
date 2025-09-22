<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultItem extends Model
{
    protected $table = 'result_items';

    protected $fillable = [
        'result_id', 'element_ref', 'type', 'values', 'max_moment', 'max_shear', 'status_pass'
    ];

    protected $casts = ['values' => 'array'];

    public function result()
    {
        return $this->belongsTo(Result::class);
    }
}