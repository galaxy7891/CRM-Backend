<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

Class CreateUserInvitationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {   
        Schema::create('user_invitations', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // Pastikan tabel menggunakan InnoDB
            $table->charset = 'utf8'; // Charset untuk mendukung emoji
            $table->collation = 'utf8_unicode_ci'; // Collation untuk Unicode penuh
            
            $table->string('email', 100)->primary();
            $table->string('token', 255)->unique();
            $table->timestamp('expired_at'); 
            $table->enum('status', ['pending', 'accepted', 'expired'])->default('pending'); 
            $table->string('invited_by', 100);
            $table->string('job_position', 50)->nullable();
            $table->enum('role', ['super_admin', 'admin', 'employee'])->default('employee');
            $table->timestamps();
            
            // Foreign Key Constraints
            $table->foreign('invited_by')->references('email')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_invitations');
    }

};
