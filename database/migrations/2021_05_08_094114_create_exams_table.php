<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->uuid("exam_id");
            $table->primary("exam_id");
            $table->string("exam_name");
            $table->text("exam_description")->nullable();
            $table->integer("total_marks")->nullable();
            $table->boolean("is_published")->default(false);
            $table->uuid("created_by")->nullable();
            $table->uuid("updated_by")->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('exam');
    }
}
