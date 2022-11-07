<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_subscribed_syllabus_months', function (Blueprint $table) {
            $table->foreign("payment_details_id", "fk_ussm_payment_details_id")
                ->on("user_payment_details")
                ->references("user_payment_details_id");
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
            $table->dropForeign("fk_ussm_payment_details_id");
        });
    }
}
