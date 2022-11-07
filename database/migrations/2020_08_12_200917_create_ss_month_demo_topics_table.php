<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSsMonthDemoTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ss_month_demo_topics', function (Blueprint $table) {
            $table->bigIncrements('ss_month_demo_topic_id');
            $table->string('demo_topic_name');
            $table->text('demo_video_url');
            $table->unsignedBigInteger('class_group_syllabus_subject_id');
            $table->text('description')->nullable();
            $table->boolean('is_active');
            $table->uuid('created_by');
            $table->uuid('updated_by');
            $table->timestamps();
            $table->foreign('class_group_syllabus_subject_id', 'ssmdt_cgs_subject_id')
                ->references('class_group_syllabus_subject_id')
                ->on('class_group_syllabus_subjects');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ss_month_demo_topics');
    }
}
