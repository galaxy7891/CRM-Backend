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
            $table->uuid('organization_id')->nullable();
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->enum('customerCategory', ['leads', 'contact'])->default('leads');
            $table->string('job', 100)->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['hot', 'warm', 'cold']);
            $table->date('birthdate')->nullable();
            $table->string('email', 100)->nullable();
            $table->string('phone', 15)->nullable();
            $table->string('owner', 100);
            $table->string('country', 50)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('subdistrict', 100)->nullable();
            $table->string('village', 100)->nullable();
            $table->string('zip_code', 5)->nullable();
            $table->string('address', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign Key Constraints
            $table->foreign('owner')->references('email')->on('users')->onDelete('cascade');
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
