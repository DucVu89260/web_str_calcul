<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id')->nullable();

            $table->string('name');
            $table->string('type')->nullable(); // steel, concrete, etc
            $table->json('properties')->nullable(); // E, fy, density
            $table->string('standard_ref')->nullable();
            $table->timestamps();

            // Foreign key
            $table->foreign('project_id')->references('id')->on('projects')->nullOnDelete();
        });
    }
    public function down() {
        Schema::dropIfExists('materials');
    }
};

