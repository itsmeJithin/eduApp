<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeExamForeignKeyType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cgs_subject_exam_questions', function (Blueprint $table) {
            $table->uuid("cgs_subject_exam_id")->after("cgss_exam_question_id");
            $table->foreign("cgs_subject_exam_id", "cgseq_cgs_subject_exam_id")
                ->references("cgs_subject_exam_id")
                ->on("cgs_subject_exams");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
