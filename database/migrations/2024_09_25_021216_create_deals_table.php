<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDealsTable extends Migration
{

    public function up()
    {
        Schema::create('deals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->string('tag', 255)->nullable();
            $table->enum('status', ['hot', 'warm', 'cold']);
            $table->enum('stage', ['qualificated', 'proposal', 'negotiate', 'won', 'lose']);
            $table->date('open_date');
            $table->date('close_date')->nullable();
            $table->date('expected_close_date');
            $table->decimal('value_estimated', 20, 2);
            $table->decimal('value_actual', 20, 2)->nullable();
            $table->enum('payment_category', ['once', 'daily', 'monthly', 'yearly']); 
            $table->integer('payment_duration')->nullable();
            $table->string('owner', 100);
            $table->timestamps();
            $table->softDeletes();

            // Foreign Key Constraints
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('owner')->references('email')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('deals');
    }
}
