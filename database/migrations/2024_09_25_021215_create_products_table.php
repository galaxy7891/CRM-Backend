<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100);
            $table->uuid('user_company_id');
            $table->enum('category', ['stuff', 'service']);
            $table->string('code', 100);
            $table->integer('quantity')->nullable();
            $table->enum('unit', ['box', 'pcs', 'unit'])->nullable();
            $table->bigInteger('price')->unsigned();
            $table->string('description', 200)->nullable();
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
        Schema::dropIfExists('products');
    }
}
