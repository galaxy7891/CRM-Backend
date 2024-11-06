<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_company_id')->nullable();
            $table->string('google_id', 255)->nullable();
            $table->string('email', 100)->unique();
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('password', 255)->nullable();
            $table->string('phone', 15)->nullable();
            $table->string('job_position', 50)->nullable();
            $table->enum('role', ['super_admin', 'admin', 'employee'])->default('super_admin');
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('image_url', 255)->nullable();
            $table->string('image_public_id', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign Key Constraints
            $table->foreign('user_company_id')->references('id')->on('users_companies')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
