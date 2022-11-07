<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSsMonthTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('ss_month_topics', function (Blueprint $table) {
            $table->bigIncrements('ss_month_topic_id');
            $table->unsignedBigInteger('syllabus_subscription_month_id');
            $table->uuid('topic_id');
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->foreign('topic_id', 'ssmt_topic_id')
                ->references('topic_id')
                ->on('topics');
            $table->foreign("syllabus_subscription_month_id", "ssmt_ssmonth_id")
                ->references("syllabus_subscription_month_id")
                ->on("syllabus_subscription_months");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ss_month_topics');
    }
}
