<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('integrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->string('type'); // sap2000, etabs
            $table->json('config')->nullable(); // host, port, api_key
            $table->string('last_status')->nullable();
            $table->timestamp('last_sync_at')->nullable();
            $table->timestamps();

            // Foreign key
            $table->foreign('project_id')
                  ->references('id')->on('projects')
                  ->nullOnDelete();
        });
    }

    public function down() {
        Schema::dropIfExists('integrations');
    }
};
