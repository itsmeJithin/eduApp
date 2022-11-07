<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropExamForeignKeyRelation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cgs_subject_exams', function (Blueprint $table) {
            $table->dropForeign("cgs_subject_exams_exam_id_foreign");
            $table->dropColumn('exam_id');
            $table->uuid("cgs_subject_exam_id")->change();
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
