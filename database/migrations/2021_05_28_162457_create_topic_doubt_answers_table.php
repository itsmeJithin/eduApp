<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopicDoubtAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topic_doubt_answers', function (Blueprint $table) {
            $table->bigIncrements("topic_doubt_answer_id");
            $table->uuid("topic_doubt_id");
            $table->text("answer");
            $table->uuid("answered_by")->nullable();
            $table->uuid("updated_by");
            $table->timestamps();
            $table->foreign("topic_doubt_id")
                ->references("topic_doubt_id")
                ->on("topic_doubts");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('topic_doubt_answers');
    }
}
