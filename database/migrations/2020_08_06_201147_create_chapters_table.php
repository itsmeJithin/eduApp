<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChaptersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chapters', function (Blueprint $table) {
            $table->uuid('chapter_id')->unique();
            $table->primary('chapter_id');
            $table->string('chapter_name');
            $table->string('chapter_code');
            $table->unsignedBigInteger('class_group_syllabus_subject_id');
            $table->string('chapter_description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->foreign('class_group_syllabus_subject_id')
                ->references('class_group_syllabus_subject_id')
                ->on('class_group_syllabus_subjects');
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
        Schema::dropIfExists('chapters');
    }
}
