<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('standards', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // AISC360-10, ACI318-14, TCVN5575:2024
            $table->string('version')->nullable();
            $table->json('meta')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('standards');
    }
};
