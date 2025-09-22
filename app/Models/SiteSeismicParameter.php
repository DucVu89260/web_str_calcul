<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSeismicParameter extends Model
{
    protected $fillable = [
        'site_parameter_id',
        'standard_code',
        'agR',
        'site_class',
        'importance_factor',
        'soil_factor',
        'Ss',
        'S1',
        'sd_short',
        'sd_1',
        'notes',
    ];

    public function site()
    {
        return $this->belongsTo(SiteParameter::class, 'site_parameter_id');
    }
}
