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
            $table->string('name', 100)->unique();
            $table->string('category', 100);
            $table->string('code', 100)->nullable();
            $table->integer('quantity');
            $table->enum('unit', ['box', 'pcs', 'unit']);
            $table->decimal('price', 20, 2);
            $table->text('description')->nullable();
            $table->string('image_url', 255)->nullable();
            $table->string('image_public_id', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}
