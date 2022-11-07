<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUserSubscribedSyllabusMonthTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_subscribed_syllabus_months', function (Blueprint $table) {
            $table->uuid("class_group_syllabus_id")->after("syllabus_subscription_month_id");
            $table->foreign("class_group_syllabus_id", "ussm_class_group_syllabus_id")
                ->references("class_group_syllabus_id")
                ->on("class_group_syllabuses");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_subscribed_syllabus_months', function (Blueprint $table) {
            //
        });
    }
}
