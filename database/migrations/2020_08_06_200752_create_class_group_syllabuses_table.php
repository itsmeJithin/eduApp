<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassGroupSyllabusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('class_group_syllabuses', function (Blueprint $table) {
            $table->uuid('class_group_syllabus_id')->unique();
            $table->primary('class_group_syllabus_id');
            $table->uuid('class_group_id');
            $table->uuid('syllabus_id');
            $table->boolean('is_active')->default(true);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->foreign('class_group_id')->references('class_group_id')->on('class_groups');
            $table->foreign('syllabus_id')->references('syllabus_id')->on('syllabuses');
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
        Schema::dropIfExists('classes');
    }
}
