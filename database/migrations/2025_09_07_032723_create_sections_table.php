<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id')->nullable();

            $table->string('name');
            $table->string('type')->nullable(); // I, H, Box, Rebar...
            $table->json('properties')->nullable();
            $table->string('standard_ref')->nullable();
            $table->timestamps();

            // Foreign key
            $table->foreign('project_id')
                ->references('id')->on('projects')
                ->nullOnDelete();
        });
    }
    public function down() {
        Schema::dropIfExists('sections');
    }
};