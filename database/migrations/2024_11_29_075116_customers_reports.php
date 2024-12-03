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
        Schema::create('customers_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_company_id');
            $table->integer('added_leads')->default(0);
            $table->integer('added_contact')->default(0);
            $table->integer('converted_contact')->default(0);
            $table->integer('removed_leads')->default(0);
            $table->integer('removed_contact')->default(0);
            $table->integer('total_leads_cold')->default(0);
            $table->integer('total_leads_warm')->default(0);
            $table->integer('total_leads_hot')->default(0);
            $table->integer('total_leads')->default(0);
            $table->integer('total_contact_cold')->default(0);
            $table->integer('total_contact_warm')->default(0);
            $table->integer('total_contact_hot')->default(0);
            $table->integer('total_contact')->default(0);
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
        Schema::dropIfExists('customers_reports');
    }
};
