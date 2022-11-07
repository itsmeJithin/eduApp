<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentOrderDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_subscribed_syllabus_months', function (Blueprint $table) {
            $table->uuid("payment_details_id")->nullable()->after("paid_on");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_subscribed_syllabus_months', function (Blueprint $table) {
            //
        });
    }
}
