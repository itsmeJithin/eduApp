<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('user_id')->unique();
            $table->primary('user_id');
            $table->string('name', 75);
            $table->string('email')->unique()->nullable();
            $table->string('mobile_number', 20)->unique();
            $table->string('parent_number', 20)->unique();
            $table->string('nationality', 30)->nullable();
            $table->string('state', 30)->nullable();
            $table->string('city', 60)->nullable();
            $table->string('place', 60)->nullable();
            $table->string('school', 60)->nullable();
            $table->string('school_place', 60)->nullable();
            $table->integer('pin_code')->nullable();
            $table->string('password');
            $table->string('avatar')->default('avatar.png');
            $table->boolean('is_active')->default(0);
            $table->timestamp('otp_verified_at')->nullable();
            $table->string('activation_token');
            $table->rememberToken();
            $table->softDeletes();
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
        Schema::dropIfExists('users');
    }
}
