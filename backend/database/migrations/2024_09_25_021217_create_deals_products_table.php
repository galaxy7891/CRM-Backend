<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDealsProductsTable extends Migration
{
    public function up()
    {
        Schema::create('deals_products', function (Blueprint $table) {
            $table->uuid('dealsProduct_Id')->primary();
            $table->uuid('product_id');
            $table->uuid('deals_id');

            // Foreign Key Constraints
            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
            $table->foreign('deals_id')->references('deals_id')->on('deals')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('deals_products');
    }
}
