<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('owner_id')->nullable();
            $table->unsignedBigInteger('default_standard_id')->nullable();

            $table->string('name');
            $table->text('description')->nullable();
            $table->string('default_unit_system', 10)->default('SI');
            $table->enum('visibility', ['private','team','public'])->default('private');
            $table->json('settings')->nullable();

            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('projects');
    }
};