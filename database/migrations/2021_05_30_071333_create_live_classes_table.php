<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLiveClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('live_classes', function (Blueprint $table) {
            $table->uuid("live_class_id");
            $table->primary("live_class_id");
            $table->unsignedBigInteger("class_group_syllabus_subject_id")->nullable();
            $table->uuid("subscription_month_id")->nullable();
            $table->string("live_class_description")->nullable();
            $table->text("live_class_url");
            $table->dateTime("live_class_start_on")->nullable();
            $table->dateTime("live_class_end_on")->nullable();
            $table->uuid("created_by")->nullable();
            $table->uuid("updated_by")->nullable();
            $table->timestamps();
            $table->foreign("class_group_syllabus_subject_id")
                ->references("class_group_syllabus_subject_id")
                ->on("class_group_syllabus_subjects");
            $table->foreign("subscription_month_id")
                ->references("subscription_month_id")
                ->on("subscription_months");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('live_classes');
    }
}
