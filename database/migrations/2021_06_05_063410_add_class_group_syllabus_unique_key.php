<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClassGroupSyllabusUniqueKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('class_group_syllabuses', function (Blueprint $table) {
            $table->unique(["class_group_id", "syllabus_id"], "uq_cgs_cg_syllabus_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('class_group_syllabuses', function (Blueprint $table) {
            $table->dropUnique("uq_cgs_cg_syllabus_id");
        });
    }
}
