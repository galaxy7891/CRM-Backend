<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersCompaniesTable extends Migration
{
    public function up()
    {
        Schema::create('users_companies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100)->unique();
            $table->string('industry', 50);
            $table->string('email', 100)->nullable();
            $table->string('image_url', 255)->nullable();
            $table->string('image_public_id', 255)->nullable();
            $table->string('phone', 15)->nullable();
            $table->string('website', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users_companies');
    }
}
