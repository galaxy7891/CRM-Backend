<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users_companies_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_company_id');
            $table->integer('total_trial');
            $table->integer('total_regular');
            $table->integer('total_professional');
            $table->integer('total_business');
            $table->integer('total_unactive');
            $table->timestamps();
            $table->softDeletes();

            // Foreign key
            $table->foreign('user_company_id')->references('id')->on('users_companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_companies_reports');
    }
};
