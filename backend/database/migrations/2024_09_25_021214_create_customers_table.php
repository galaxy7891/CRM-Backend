<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');
            $table->uuid('user_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->enum('customerCategory', ['leads', 'contact'])->default('leads');
            $table->string('job')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['hot', 'warm', 'cold']);
            $table->date('birthdate')->nullable();
            $table->string('email');
            $table->string('phone');
            $table->string('owner');
            $table->string('address')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('subdistrict')->nullable();
            $table->string('village')->nullable();
            $table->string('zip_code')->nullable();
            $table->timestamps();
            $table->dateTime('deleted_at')->nullable();

            // Foreign Key Constraints
            $table->foreign('user_id')->references('user_id')->on('users');
            $table->foreign('organization_id')->references('organization_id')->on('organizations');
        });
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
