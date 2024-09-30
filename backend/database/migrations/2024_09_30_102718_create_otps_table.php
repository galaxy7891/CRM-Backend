<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('otps', function (Blueprint $table) {
            $table->uuid('otp_id')->primary();
            $table->uuid('user_id'); 
            $table->string('code');
            $table->dateTime('expire_at');
            $table->boolean('is_used')->default(false);
            $table->timestamps(); 
            $table->softDeletes();

            // Foreign key constraint linking to the users table
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('otps');
    }
}