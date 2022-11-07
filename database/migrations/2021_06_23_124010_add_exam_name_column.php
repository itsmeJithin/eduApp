<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExamNameColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cgs_subject_exams', function (Blueprint $table) {
            $table->string("subject_exam_name")->after("cgs_subject_exam_id");
            $table->string("subject_exam_description")->nullable()->after("subject_exam_name");
            $table->decimal("maximum_marks")->nullable()->after("subject_exam_description");
            $table->integer("maximum_time")->nullable()->after("maximum_marks");
            $table->unsignedBigInteger("exam_mode_id")->nullable()->change();
            $table->boolean("is_published")->default(false)->after("end_date");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cgs_subject_exams', function (Blueprint $table) {
            //
        });
    }
}
