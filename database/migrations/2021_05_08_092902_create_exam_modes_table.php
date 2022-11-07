<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamModesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_modes', function (Blueprint $table) {
            $table->bigIncrements("exam_mode_id");
            $table->string("exam_mode_name");
            $table->string("exam_mode_code")->unique();
            $table->boolean("is_active")->default(true);
            $table->string("exam_mode_description")->nullable();
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
        Schema::dropIfExists('exam_modes');
    }
}
