<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCgsSubjectExamQuestions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cgs_subject_exam_questions', function (Blueprint $table) {
            $table->bigIncrements("cgss_exam_question_id");
            $table->uuid("question_id");
            $table->unsignedBigInteger("cgs_subject_exam_id");
            $table->uuid("created_by")->nullable();
            $table->uuid("updated_by")->nullable();
            $table->foreign("question_id")
                ->on("question_pool")
                ->references("question_id");
            $table->foreign("cgs_subject_exam_id", "cgs_seq_subject_exam_id")
                ->on("cgs_subject_exams")
                ->references("cgs_subject_exam_id");
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
        Schema::dropIfExists('cgs_subject_exam_questions');
    }
}
