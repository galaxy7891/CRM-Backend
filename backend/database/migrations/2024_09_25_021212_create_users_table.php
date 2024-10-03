<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('user_id')->primary();
            $table->uuid('company_id')->nullable();
            $table->string('google_id')->nullable();
            $table->string('email')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('password')->nullable();
            $table->string('phone')->nullable();
            $table->string('job_position')->nullable();
            $table->enum('role', ['super_admin', 'admin', 'employee'])->default('super_admin');
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign Key Constraints
            $table->foreign('company_id')->references('company_id')->on('companies')->onDelete('cascade');
        });

    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
