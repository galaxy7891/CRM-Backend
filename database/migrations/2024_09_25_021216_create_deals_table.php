<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDealsTable extends Migration
{

    public function up()
    {   
        Schema::create('deals', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // Pastikan tabel menggunakan InnoDB
            $table->charset = 'utf8'; // Charset untuk mendukung emoji
            $table->collation = 'utf8_unicode_ci'; // Collation untuk Unicode penuh
            
            $table->uuid('id')->primary();
            $table->enum('category', ['customers', 'customers_companies']);
            $table->uuid('customer_id')->nullable();
            $table->uuid('customers_company_id')->nullable();
            $table->string('name', 100);
            $table->string('description', 200)->nullable();
            $table->string('tag', 255)->nullable();
            $table->enum('status', ['hot', 'warm', 'cold']);
            $table->enum('stage', ['qualificated', 'proposal', 'negotiate', 'won', 'lose']);
            $table->date('open_date');
            $table->date('close_date')->nullable();
            $table->date('expected_close_date');
            $table->bigInteger('value_estimated')->unsigned();
            $table->bigInteger('value_actual')->unsigned()->nullable();
            $table->enum('payment_category', ['once', 'daily', 'monthly', 'yearly']);
            $table->integer('payment_duration')->nullable();
            $table->string('owner', 100);
            $table->timestamps();
            $table-> softDeletes();
            
            // Foreign Key Constraints
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('customers_company_id')->references('id')->on('customers_companies')->onDelete('cascade');
            $table->foreign('owner')->references('email')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('deals');
    }
}
