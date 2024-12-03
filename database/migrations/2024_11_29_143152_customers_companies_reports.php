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
        Schema::create('customers_companies_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_company_id');
            $table->integer('added_customers_companies')->default(0);
            $table->integer('removed_customers_companies')->default(0);
            $table->integer('total_customers_companies_cold')->default(0);
            $table->integer('total_customers_companies_warm')->default(0);
            $table->integer('total_customers_companies_hot')->default(0);
            $table->integer('total_customers_companies')->default(0);
            $table->date('date');
            $table->timestamps();
            $table->softDeletes();

            // Foreign Key Constraints
            $table->foreign('user_company_id')->references('id')->on('users_companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers_companies_reports');
    }
};
