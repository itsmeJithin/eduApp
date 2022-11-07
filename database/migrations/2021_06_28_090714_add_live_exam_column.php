<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLiveExamColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cgs_subject_exams', function (Blueprint $table) {
            $table->boolean("is_live_exam")->default(false)->after("is_chapter_wise");
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
