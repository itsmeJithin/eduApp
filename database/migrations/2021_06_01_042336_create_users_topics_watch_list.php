<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTopicsWatchList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_topics_watch_list', function (Blueprint $table) {
            $table->bigIncrements("users_topics_watch_list_id");
            $table->uuid("user_id");
            $table->uuid("class_group_syllabus_id");
            $table->uuid("topic_id");
            $table->foreign("user_id")
                ->references("user_id")
                ->on("users");
            $table->foreign("topic_id")
                ->references("topic_id")
                ->on("topics");
            $table->foreign("class_group_syllabus_id")
                ->references("class_group_syllabus_id")
                ->on("class_group_syllabuses");
            $table->unsignedBigInteger("watch_time")->nullable();
            $table->unique(["user_id", "class_group_syllabus_id", "topic_id"],"utwl_ui_cgsi_ti");
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
        Schema::dropIfExists('users_topics_watch_list');
    }
}
