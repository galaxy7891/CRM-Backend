<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

Class CreatePasswordResetTokensTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            
            $table->engine = 'InnoDB'; // Pastikan tabel menggunakan InnoDB
            $table->charset = 'utf8'; // Charset untuk mendukung emoji
            $table->collation = 'utf8_unicode_ci'; // Collation untuk Unicode penuh
            
            $table->string('email', 100)->primary();
            $table->string('token', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
    }
};
