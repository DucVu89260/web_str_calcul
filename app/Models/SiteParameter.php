<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteParameter extends Model
{
    protected $fillable = [
        'name',
        'country',
        'latitude',
        'longitude',
        'elevation',
        'terrain_category',
        'exposure_category',
        'topography_factor',
        'importance_category',
        'meta'
    ];

    public function windParameters()
    {
        return $this->hasMany(SiteWindParameter::class);
    }

    public function seismicParameters()
    {
        return $this->hasMany(SiteSeismicParameter::class);
    }
}
