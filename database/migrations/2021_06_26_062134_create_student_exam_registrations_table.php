<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentExamRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_exam_registrations', function (Blueprint $table) {
            $table->uuid('student_exam_registration_id');
            $table->primary("student_exam_registration_id");
            $table->uuid("user_id");
            $table->uuid('cgs_subject_exam_id');
            $table->enum("status", ["PASSED", "FAILED", "IN_PROGRESS"]);
            $table->boolean("is_completed")->default(false);
            $table->foreign("user_id")->references("user_id")->on('users');
            $table->foreign("cgs_subject_exam_id")
                ->references("cgs_subject_exam_id")
                ->on('cgs_subject_exams');
            $table->dateTime("exam_started_at")->nullable();
            $table->dateTime("exam_completed_at")->nullable();
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
        Schema::dropIfExists('student_exam_registrations');
    }
}
