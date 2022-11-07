<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentDetailsColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_subscribed_syllabus_months', function (Blueprint $table) {
            $table->decimal("paid_amount", 10, 2)->nullable()->after("in_active_remarks");
            $table->enum("paid_through", ["CASH", "ONLINE", "CHALLAN"])->nullable()->after("paid_amount");
            $table->dateTime("paid_on")->nullable()->after("paid_through");
            $table->uuid("paid_by")->nullable()->after("paid_on");
            $table->string("paid_by_user_type")->nullable()->after("paid_by");
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
