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
        Schema::create('accounts_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_company_id');
            $table->enum('account_type', ['trial', 'regular', 'professional', 'business', 'unactive'])->default('trial');
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
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
        Schema::dropIfExists('accounts_types');
    }
};
