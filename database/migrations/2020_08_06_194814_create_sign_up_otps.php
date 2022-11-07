<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSignUpOtps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sign_up_otps', function (Blueprint $table) {
            $table->bigIncrements('sign_up_otp_id');
            $table->uuid('user_id');
            $table->integer('otp');
            $table->dateTime('expires_at')->nullable();
            $table->boolean('is_used')->default(false);
            $table->foreign('user_id')->references('user_id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mobile_signup_otp');
    }
}
