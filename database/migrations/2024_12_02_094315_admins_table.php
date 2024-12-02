<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {   
        Schema::create('Admin', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('email', 100)->unique();
            $table->string('first_name', 50);
            $table->string('last_name', 50)->nullable();
            $table->string('phone', 15);
            $table->string('password', 255);
            $table->string('image_url', 255)->nullable();
            $table->string('image_public_id', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Admin');
    }
};
