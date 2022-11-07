<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAptuResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aptu_resources', function (Blueprint $table) {
            $table->uuid("aptu_resource_id");
            $table->string("file_name");
            $table->json("storage_object")->nullable();
            $table->text("file_url")->nullable();
            $table->uuid("created_by")->nullable();
            $table->text("updated_by")->nullable();
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
        Schema::dropIfExists('aptu_resources');
    }
}
