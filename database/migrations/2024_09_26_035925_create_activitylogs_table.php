<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // Pastikan tabel menggunakan InnoDB
            $table->charset = 'utf8'; // Charset untuk mendukung emoji
            $table->collation = 'utf8_unicode_ci'; // Collation untuk Unicode penuh
            
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->string('model_name', 100);
            $table->string('action', 255);
            $table->text('changes');
            $table->timestamps();
            $table->softDeletes();

            // Menambahkan foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_logs');
    }
}
