<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersFcmTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_fcm_tokens', function (Blueprint $table) {
            $table->bigIncrements("users_fcm_token_id");
            $table->uuid("user_id");
            $table->string("token");
            $table->timestamps();
            $table->foreign("user_id")->references("user_id")->on("users");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_fcm_tokens');
    }
}
