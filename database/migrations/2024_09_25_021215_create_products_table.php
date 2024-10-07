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
            $table->string('name')->unique();
            $table->string('category');
            $table->string('code')->nullable();
            $table->integer('quantity');
            $table->string('unit');
            $table->decimal('price', 8, 2);
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->string('image_public_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}
