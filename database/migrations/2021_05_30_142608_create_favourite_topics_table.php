<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFavouriteTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('favourite_topics', function (Blueprint $table) {
            $table->bigIncrements('favourite_topic_id');
            $table->uuid("user_id");
            $table->uuid("subscription_month_id");
            $table->uuid("class_group_syllabus_id");
            $table->uuid("topic_id");
            $table->uuid("created_by")->nullable();
            $table->foreign("user_id")->references("user_id")->on("users");
            $table->foreign("subscription_month_id")
                ->references("subscription_month_id")
                ->on("subscription_months");
            $table->foreign("class_group_syllabus_id")
                ->references("class_group_syllabus_id")
                ->on("class_group_syllabuses");
            $table->foreign("topic_id")
                ->references("topic_id")
                ->on("topics");
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
        Schema::dropIfExists('favourite_topics');
    }
}
