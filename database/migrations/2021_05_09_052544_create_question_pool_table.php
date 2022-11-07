<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionPoolTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_pool', function (Blueprint $table) {
            $table->uuid("question_id");
            $table->primary("question_id");
            $table->unsignedBigInteger("question_type_id");
            $table->foreign("question_type_id")
                ->on("question_types")
                ->references("question_type_id");
            $table->text("question");
            $table->json("options")->nullable();
            $table->double("mark");
            $table->unsignedBigInteger("question_time")->nullable();
            $table->uuid("created_by")->nullable();
            $table->uuid("updated_by")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('question_pool');
    }
}
