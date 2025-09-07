<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('analysis_run_id')->nullable();
            $table->json('summary')->nullable();
            $table->timestamps();

            // Foreign key
            $table->foreign('analysis_run_id')
                  ->references('id')->on('analysis_runs')
                  ->nullOnDelete();
        });

        Schema::create('result_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('result_id')->nullable();
            $table->string('element_ref')->nullable();
            $table->string('type')->nullable(); // node, member
            $table->json('values')->nullable(); // moment, shear, disp...
            $table->float('max_moment')->nullable();
            $table->float('max_shear')->nullable();
            $table->boolean('status_pass')->nullable();
            $table->timestamps();

            // Foreign key
            $table->foreign('result_id')
                  ->references('id')->on('results')
                  ->nullOnDelete();
        });
    }

    public function down() {
        Schema::dropIfExists('result_items');
        Schema::dropIfExists('results');
    }
};
