<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCgsSubjectExamWithExamModes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("cgs_subject_exams", function (Blueprint $table) {
            $table->unsignedBigInteger("exam_mode_id")->nullable()->after("exam_id");
            $table->foreign("exam_mode_id")
                ->references("exam_mode_id")
                ->on("exam_modes");
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
