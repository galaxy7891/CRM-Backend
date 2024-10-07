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
            $table->string('email')->primary();
            $table->string('token')->unique();
            $table->timestamp('expired_at'); 
            $table->enum('status', ['pending', 'accepted', 'expired'])->default('pending'); 
            $table->string('invited_by');
            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('invited_by')->references('email')->on('users')->onDelete('cascade');
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
