<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('files', function (Blueprint $table) {
            $table->id();

            // FK dạng unsignedBigInteger (không ràng buộc DB)
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();

            $table->string('path');
            $table->string('mime', 50)->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->string('origin')->nullable();
            $table->string('checksum', 64)->nullable();
            $table->json('meta')->nullable();

            $table->timestamps();
        });

    }
    public function down() {
        Schema::dropIfExists('files');
    }
};
