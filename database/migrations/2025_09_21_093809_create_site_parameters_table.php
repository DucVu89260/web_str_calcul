<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteParametersTable extends Migration
{
    public function up()
    {
        Schema::create('site_parameters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('country')->default('VN');
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->double('elevation')->nullable();           // cao độ

            $table->string('terrain_category')->nullable();     // loại địa hình I, II, III, IV theo TCVN
            $table->string('exposure_category')->nullable();    // Exposure B, C, D theo ASCE
            $table->double('topography_factor')->nullable();    // Kzt hoặc hệ số tương đương

            $table->string('importance_category')->nullable();  // công trình cấp I, II, III,...

            $table->json('meta')->nullable();                   // dành cho mở rộng
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('site_parameters');
    }
}

