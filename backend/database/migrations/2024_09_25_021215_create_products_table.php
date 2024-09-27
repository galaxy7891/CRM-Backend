<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('product_id')->primary();
            $table->string('name');
            $table->string('category');
            $table->string('code')->nullable();
            $table->integer('quantity');
            $table->string('unit');  // String karena Laravel tidak mendukung tipe ENUM buatan
            $table->decimal('price', 8, 2);
            $table->text('description')->nullable();
            $table->string('photo_product');
            $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}
