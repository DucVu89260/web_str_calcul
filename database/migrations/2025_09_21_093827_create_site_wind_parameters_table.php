<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteWindParametersTable extends Migration
{
    public function up()
    {
        Schema::create('site_wind_parameters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_parameter_id')->constrained('site_parameters')->onDelete('cascade');
            $table->string('standard_code');  // e.g. 'TCVN2737-2023', 'ASCE7-10'
            
            // Các tham số gió chuẩn hóa
            $table->double('basic_wind_speed')->nullable();         // tốc độ gió cơ bản (m/s hoặc km/h) -- cần ghi đơn vị
            $table->double('pressure_reference')->nullable();       // áp lực gió cơ bản nếu có (kN/m2 hoặc Pa)
            $table->string('map_region')->nullable();               // vùng gió (theo standard)
            $table->json('terrain_factors')->nullable();            // các hệ số theo địa hình, k(z_e) hoặc exposure
            $table->double('gust_effect_factor')->nullable();        // G, nếu chuẩn yêu cầu
            $table->double('directionality_factor')->nullable();     // Kd, nếu chuẩn có
            $table->json('conversion_to_other')->nullable();         // ví dụ { "to":"ASCE7-10", "factor_speed":..., "factor_pressure":... }
            
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('site_wind_parameters');
    }
}
