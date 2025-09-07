<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('models', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('snapshot_file_id')->nullable();

            $table->string('name');
            $table->unsignedInteger('version')->default(1);
            $table->enum('status', ['draft','locked'])->default('draft');
            $table->string('checksum', 64)->nullable();
            $table->json('snapshot_meta')->nullable();

            $table->timestamps();

            $table->unique(['project_id', 'name', 'version']);
        });
    }
    public function down() {
        Schema::dropIfExists('models');
    }
};