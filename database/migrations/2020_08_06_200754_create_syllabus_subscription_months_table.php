<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSyllabusSubscriptionMonthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('syllabus_subscription_months', function (Blueprint $table) {
            $table->bigIncrements('syllabus_subscription_month_id');
            $table->uuid('class_group_syllabus_id');
            $table->uuid('subscription_month_id');
            $table->decimal('price', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->foreign('class_group_syllabus_id')
                ->references('class_group_syllabus_id')
                ->on('class_group_syllabuses');
            $table->foreign('subscription_month_id')
                ->references('subscription_month_id')
                ->on('subscription_months');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('syllabus_subscription_months');
    }
}
