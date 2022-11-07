<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSubscribedSyllabusMonths extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_subscribed_syllabus_months', function (Blueprint $table) {
            $table->bigIncrements('us_syllabus_month_id');
            $table->uuid('user_id');
            $table->unsignedBigInteger('syllabus_subscription_month_id');
            $table->boolean('is_active')->default(true);
            $table->string('in_active_remarks')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('update_by')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('user_id')->on('users');
            $table->foreign('syllabus_subscription_month_id', 'sdm_id')->references('syllabus_subscription_month_id')->on('syllabus_subscription_months');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_subscribed_syllabus_months');
    }
}
