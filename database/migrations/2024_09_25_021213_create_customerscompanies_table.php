<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersCompaniesTable extends Migration
{
    public function up()
    {
        Schema::create('customers_companies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100)->unique();
            $table->string('industry', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->enum('status', ['hot', 'warm', 'cold']);
            $table->string('phone', 15)->nullable();
            $table->string('owner', 100);
            $table->text('description')->nullable();
            $table->string('website', 255)->nullable();
            $table->string('address', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('subdistrict', 100)->nullable();
            $table->string('village', 100)->nullable();
            $table->string('zip_code', 5)->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign Key Constraints
            $table->foreign('owner')->references('email')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('customers_companies');
    }
}