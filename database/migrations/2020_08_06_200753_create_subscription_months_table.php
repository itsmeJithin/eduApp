<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionMonthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription_months', function (Blueprint $table) {
            $table->uuid('subscription_month_id');
            $table->primary('subscription_month_id');
            $table->string('subscription_month_name', '50')->unique();
            $table->string('subscription_month_code', '50')->unique();
            $table->integer('subscription_month_days')->default(30);
            $table->integer('subscription_month_order');
            $table->boolean('is_active')->default(true);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
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
        Schema::dropIfExists('subscription_months');
    }
}
