<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = [
        'name',
        'type',
        'properties',
        'diameter',
        'thickness',
        'weight_per_m',
        'price',
        'standard_ref'
    ];
}
