<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_users', function (Blueprint $table) {
            $table->uuid("staff_user_id");
            $table->string("staff_name");
            $table->string("staff_code")->unique();
            $table->string("staff_phone_number")->unique();
            $table->string("staff_email")->unique();
            $table->string("staff_password");
            $table->string("address")->nullable();
            $table->string('state', 30)->nullable();
            $table->string('nationality', 30)->nullable();
            $table->integer('pin_code')->nullable();
            $table->string('avatar')->default('avatar.png');
            $table->boolean('is_active')->default(0);
            $table->timestamp('otp_verified_at')->nullable();
            $table->string('activation_token');
            $table->softDeletes();
            $table->rememberToken();
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
        Schema::dropIfExists('staff_users');
    }
}
