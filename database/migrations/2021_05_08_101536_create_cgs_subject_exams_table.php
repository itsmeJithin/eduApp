<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCgsSubjectExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cgs_subject_exams', function (Blueprint $table) {
            $table->bigIncrements("cgs_subject_exam_id");
            $table->unsignedBigInteger("cgs_subject_id");
            $table->uuid("exam_id");
            $table->boolean("is_chapter_wise")->default(false);
            $table->uuid("chapter_id")->nullable();
            $table->boolean("is_topic_wise")->default(false);
            $table->uuid("topic_id")->nullable();
            $table->boolean("is_weekly")->default(false);
            $table->boolean("is_monthly")->default(false);
            $table->uuid("subscription_month_id")->nullable();
            $table->boolean("is_mock_test")->default(false);
            $table->dateTime("start_date")->nullable();
            $table->dateTime("end_date")->nullable();
            $table->foreign("subscription_month_id", "cgsse_sm_id")
                ->on("subscription_months")
                ->references("subscription_month_id");
            $table->foreign("topic_id", "cgsse_topic_id")
                ->on("topics")
                ->references("topic_id");
            $table->foreign("chapter_id", "cgsse_chapter_id")
                ->on("chapters")
                ->references("chapter_id");
            $table->foreign("exam_id")
                ->on("exams")
                ->references("exam_id");
            $table->foreign("cgs_subject_id", "cgs_subject_id")
                ->on("class_group_syllabus_subjects")
                ->references("class_group_syllabus_subject_id");
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
        Schema::dropIfExists('cgs_subject_exams');
    }
}
