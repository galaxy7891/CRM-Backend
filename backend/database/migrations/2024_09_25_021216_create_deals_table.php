<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDealsTable extends Migration
{
    public function up()
    {
        Schema::create('deals', function (Blueprint $table) {
            $table->uuid('deals_id')->primary();
            $table->uuid('customer_id');
            $table->uuid('user_id');
            $table->string('name');
            $table->string('deals_customer');
            $table->text('description')->nullable();
            $table->string('tag');
            $table->enum('stage', ['qualificated', 'proposal', 'negotiate', 'won', 'lose']);
            $table->date('open_date');
            $table->date('close_date')->nullable();
            $table->date('expected_close_date');
            $table->decimal('payment_expected', 8, 2)->nullable();
            $table->enum('payment_category', ['once', 'hours', 'daily', 'weekly', 'monthly', 'quarter', 'yearly']);
            $table->integer('payment_duration')->nullable();
            $table->string('owner');
            $table->timestamps();
            $table->softDeletes();

            // Foreign Key Constraints
            $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('cascade');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('deals');
    }
}
