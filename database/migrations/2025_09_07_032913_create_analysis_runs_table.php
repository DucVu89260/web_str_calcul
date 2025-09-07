<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('analysis_runs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->unsignedBigInteger('initiated_by')->nullable();
            $table->string('runner'); // python_wrapper, sap_api, local_solver
            $table->unsignedBigInteger('standard_id')->nullable();
            $table->enum('status', ['queued','running','success','failed'])->default('queued');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->json('result_summary')->nullable();
            $table->unsignedBigInteger('result_file_id')->nullable();
            $table->text('error_log')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            // Index
            $table->index(['model_id','status','created_at']);

            // Foreign keys
            $table->foreign('project_id')->references('id')->on('projects')->nullOnDelete();
            $table->foreign('model_id')->references('id')->on('models')->nullOnDelete();
            $table->foreign('initiated_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('standard_id')->references('id')->on('standards')->nullOnDelete();
            $table->foreign('result_file_id')->references('id')->on('files')->nullOnDelete();
        });

    }
    public function down() {
        Schema::dropIfExists('analysis_runs');
    }
};

