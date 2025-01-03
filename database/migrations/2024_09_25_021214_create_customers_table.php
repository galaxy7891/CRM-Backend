<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // Pastikan tabel menggunakan InnoDB
            $table->charset = 'utf8'; // Charset untuk mendukung emoji
            $table->collation = 'utf8_unicode_ci'; // Collation untuk Unicode penuh
            
            $table->uuid('id')->primary();
            $table->uuid('customers_company_id')->nullable();
            $table->string('first_name', 50);
            $table->string('last_name', 50)->nullable();
            $table->enum('customerCategory', ['leads', 'contact'])->default('leads');
            $table->string('job', 100)->nullable();
            $table->string('description', 200)->nullable();
            $table->enum('status', ['hot', 'warm', 'cold']);
            $table->date('birthdate')->nullable();
            $table->string('email', 100)->nullable();
            $table->string('phone', 15);
            $table->string('owner', 100);
            $table->string('address', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('subdistrict', 100)->nullable();
            $table->string('village', 100)->nullable();
            $table->string('zip_code', 5)->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign Key Constraints
            $table->foreign('owner')->references('email')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('customers_company_id')->references('id')->on('customers_companies')->onDelete('cascade');
        }); 
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
