<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropCgsExamForeignKeyRelation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cgs_subject_exam_questions', function (Blueprint $table) {
            $table->dropForeign("cgs_seq_subject_exam_id");
            $table->dropColumn("cgs_subject_exam_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
