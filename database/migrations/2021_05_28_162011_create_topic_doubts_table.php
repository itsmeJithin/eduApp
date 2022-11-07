<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopicDoubtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topic_doubts', function (Blueprint $table) {
            $table->uuid("topic_doubt_id");
            $table->primary("topic_doubt_id");
            $table->text("doubt");
            $table->uuid("topic_id");
            $table->foreign("topic_id")
                ->on("topics")
                ->references("topic_id");
            $table->uuid("created_by");
            $table->uuid("updated_by");
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
        Schema::dropIfExists('topic_doubts');
    }
}
