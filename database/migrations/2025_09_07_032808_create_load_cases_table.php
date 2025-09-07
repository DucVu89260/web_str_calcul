<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('load_cases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('model_id')->nullable();

            $table->string('name');
            $table->string('type')->nullable(); // dead, live, wind, etc
            $table->json('pattern')->nullable();
            $table->json('magnitudes')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('model_id')
                ->references('id')->on('models')
                ->nullOnDelete();
        });
    }
    public function down() {
        Schema::dropIfExists('load_cases');
    }
};
