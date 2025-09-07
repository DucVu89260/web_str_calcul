<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // Quan hệ tới users
            $table->unsignedBigInteger('user_id')->nullable();

            $table->string('action');
            $table->string('object_type')->nullable();
            $table->unsignedBigInteger('object_id')->nullable();
            $table->json('meta')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamps();

            // Foreign key
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->nullOnDelete();
        });
    }

    public function down() {
        Schema::dropIfExists('audit_logs');
    }
};