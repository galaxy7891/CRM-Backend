<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrialAccountsTable extends Migration
{
    public function up()
    {   
        Schema::create('trial_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary(); 
            $table->uuid('user_company_id'); 
            $table->dateTime('trial_end_date');
            $table->timestamps();
            
            // Foreign Key Constraints
            $table->foreign('user_company_id')->references('id')->on('users_companies')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('trial_accounts');
    }
}
