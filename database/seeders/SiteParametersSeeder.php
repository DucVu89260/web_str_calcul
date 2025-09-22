<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteParameter;

class SiteParametersSeeder extends Seeder
{
    public function run(): void
    {
        $sites = [
            [
                'name' => 'Hà Nội',
                'country' => 'VN',
                'latitude' => 21.0278,
                'longitude' => 105.8342,
                'elevation' => 10,
                'terrain_category' => 'II',
                'exposure_category' => 'B',
                'topography_factor' => 1.0,
                'importance_category' => 'II',
                'meta' => json_encode([
                    'wind_zone' => 'Zone 2',
                    'basic_wind_speed' => 31, // m/s
                    'seismic_agR' => 0.05
                ]),
            ],
            [
                'name' => 'Hải Phòng',
                'country' => 'VN',
                'latitude' => 20.8449,
                'longitude' => 106.6881,
                'elevation' => 5,
                'terrain_category' => 'II',
                'exposure_category' => 'C',
                'topography_factor' => 1.0,
                'importance_category' => 'II',
                'meta' => json_encode([
                    'wind_zone' => 'Zone 2',
                    'basic_wind_speed' => 32,
                    'seismic_agR' => 0.07
                ]),
            ],
            [
                'name' => 'Hải Dương',
                'country' => 'VN',
                'latitude' => 20.9399,
                'longitude' => 106.3159,
                'elevation' => 10,
                'terrain_category' => 'II',
                'exposure_category' => 'B',
                'topography_factor' => 1.0,
                'importance_category' => 'II',
                'meta' => json_encode([
                    'wind_zone' => 'Zone 2',
                    'basic_wind_speed' => 31,
                    'seismic_agR' => 0.05
                ]),
            ],
            [
                'name' => 'Bắc Ninh',
                'country' => 'VN',
                'latitude' => 21.1861,
                'longitude' => 106.0763,
                'elevation' => 8,
                'terrain_category' => 'II',
                'exposure_category' => 'B',
                'topography_factor' => 1.0,
                'importance_category' => 'II',
                'meta' => json_encode([
                    'wind_zone' => 'Zone 2',
                    'basic_wind_speed' => 31,
                    'seismic_agR' => 0.05
                ]),
            ],
            [
                'name' => 'Hưng Yên',
                'country' => 'VN',
                'latitude' => 20.6464,
                'longitude' => 106.0511,
                'elevation' => 6,
                'terrain_category' => 'II',
                'exposure_category' => 'B',
                'topography_factor' => 1.0,
                'importance_category' => 'II',
                'meta' => json_encode([
                    'wind_zone' => 'Zone 2',
                    'basic_wind_speed' => 31,
                    'seismic_agR' => 0.05
                ]),
            ],
            [
                'name' => 'Bắc Giang',
                'country' => 'VN',
                'latitude' => 21.2730,
                'longitude' => 106.1940,
                'elevation' => 10,
                'terrain_category' => 'II',
                'exposure_category' => 'B',
                'topography_factor' => 1.0,
                'importance_category' => 'II',
                'meta' => json_encode([
                    'wind_zone' => 'Zone 2',
                    'basic_wind_speed' => 32,
                    'seismic_agR' => 0.06
                ]),
            ],
            [
                'name' => 'Lào Cai',
                'country' => 'VN',
                'latitude' => 22.4850,
                'longitude' => 103.9707,
                'elevation' => 100,
                'terrain_category' => 'III',
                'exposure_category' => 'C',
                'topography_factor' => 1.0,
                'importance_category' => 'II',
                'meta' => json_encode([
                    'wind_zone' => 'Zone 1',
                    'basic_wind_speed' => 24,
                    'seismic_agR' => 0.12
                ]),
            ],
        ];

        foreach ($sites as $s) {
            SiteParameter::create($s);
        }
    }
}