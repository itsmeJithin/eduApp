<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentErAnswers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_er_answers', function (Blueprint $table) {
            $table->bigIncrements("student_er_answers_id");
            $table->uuid("student_exam_registration_id");
            $table->foreign("student_exam_registration_id")
                ->references("student_exam_registration_id")
                ->on("student_exam_registrations");
            $table->json("question_answers");
            $table->decimal("marks_obtained", 5, 2)->nullable();
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
        Schema::dropIfExists('student_er_answers');
    }
}
