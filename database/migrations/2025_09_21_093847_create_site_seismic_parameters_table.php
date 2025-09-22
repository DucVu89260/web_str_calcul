<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteSeismicParametersTable extends Migration
{
    public function up()
    {
        Schema::create('site_seismic_parameters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_parameter_id')->constrained('site_parameters')->onDelete('cascade');
            $table->string('standard_code');  // e.g. 'TCVN9386-2012', 'ASCE7-10'
            
            // Các tham số động đất chuẩn hóa
            $table->double('agR')->nullable();         // đỉnh gia tốc nền tham chiếu
            $table->string('site_class')->nullable();   // A/B/C/D/E/F
            $table->double('importance_factor')->nullable();  // γI
            $table->double('soil_factor')->nullable();        // S (nếu chuẩn yêu cầu)
            $table->double('Ss')->nullable();          // Spectral accel short period (ASCE)
            $table->double('S1')->nullable();          // Spectral accel 1s period
            $table->double('sd_short')->nullable();     // SDS
            $table->double('sd_1')->nullable();         // SD1
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('site_seismic_parameters');
    }
}

