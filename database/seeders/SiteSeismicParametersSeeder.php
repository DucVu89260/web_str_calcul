<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteParameter;
use App\Models\SiteSeismicParameter;

class SiteSeismicParametersSeeder extends Seeder
{
    public function run()
    {
        $hanoi = SiteParameter::where('name', 'Hà Nội')->first();
        if ($hanoi) {
            SiteSeismicParameter::create([
                'site_parameter_id' => $hanoi->id,
                'standard_code' => 'TCVN9386-2012',
                'agR' => 0.08,
                'site_class' => 'C',
                'importance_factor' => 1.0,
                'soil_factor' => 1.2,
                'Ss' => null,
                'S1' => null,
                'notes' => 'Hà Nội thuộc vùng động đất cấp 7 theo TCVN.',
            ]);
        }
    }
}