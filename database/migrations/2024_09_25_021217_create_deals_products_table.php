<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDealsProductsTable extends Migration
{
    public function up()
    {
        Schema::create('deals_products', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // Pastikan tabel menggunakan InnoDB
            $table->charset = 'utf8'; // Charset untuk mendukung emoji
            $table->collation = 'utf8_unicode_ci'; // Collation untuk Unicode penuh
            
            $table->uuid('id')->primary();
            $table->uuid('product_id');
            $table->uuid('deals_id');
            $table->integer('quantity')->nullable();
            $table->enum('unit', ['box', 'pcs', 'unit'])->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign Key Constraints
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('deals_id')->references('id')->on('deals')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('deals_products');
    }
}
