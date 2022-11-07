<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassGroupSyllabusSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('class_group_syllabus_subjects', function (Blueprint $table) {
            $table->bigIncrements('class_group_syllabus_subject_id');
            $table->uuid('class_group_syllabus_id');
            $table->uuid('subject_id');
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->foreign('class_group_syllabus_id')->references('class_group_syllabus_id')->on('class_group_syllabuses');
            $table->foreign('subject_id')->references('subject_id')->on('subjects');
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
        Schema::dropIfExists('class_group_syllabus_subjects');
    }
}
