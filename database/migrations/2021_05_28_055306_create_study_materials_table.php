<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudyMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('study_materials', function (Blueprint $table) {
            $table->uuid("study_material_id");
            $table->primary("study_material_id");
            $table->string("study_material_name");
            $table->string("study_material_description")->nullable();
            $table->uuid("topic_id")->nullable();
            $table->foreign("topic_id")
                ->references("topic_id")
                ->on("topics");
            $table->text("study_material_url");
            $table->uuid("created_by")->nullable();
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
        Schema::dropIfExists('study_materials');
    }
}
