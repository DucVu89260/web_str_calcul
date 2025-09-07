<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('api_requests', function (Blueprint $table) {
            $table->id();
            
            // Optional relations
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();

            $table->string('endpoint');
            $table->string('method', 10);
            $table->unsignedInteger('status_code')->nullable();
            $table->float('response_time')->nullable();
            $table->text('payload')->nullable();
            $table->text('response')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('project_id')
                  ->references('id')->on('projects')
                  ->nullOnDelete();

            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->nullOnDelete();
        });
    }

    public function down() {
        Schema::dropIfExists('api_requests');
    }
};
