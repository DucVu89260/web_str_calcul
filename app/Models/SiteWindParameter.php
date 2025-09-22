<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteWindParameter extends Model
{
    protected $fillable = [
        'site_parameter_id',
        'standard_code',
        'basic_wind_speed',
        'pressure_reference',
        'map_region',
        'terrain_factors',
        'gust_effect_factor',
        'directionality_factor',
        'conversion_to_other',
        'notes',
    ];

    protected $casts = [
        'terrain_factors' => 'array',
        'conversion_to_other' => 'array',
    ];

    public function site()
    {
        return $this->belongsTo(SiteParameter::class, 'site_parameter_id');
    }
}

