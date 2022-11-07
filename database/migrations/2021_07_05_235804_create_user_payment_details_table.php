<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_payment_details', function (Blueprint $table) {
            $table->uuid("user_payment_details_id");
            $table->primary("user_payment_details_id");
            $table->integer("receipt_no")->unique();
            $table->uuid("user_id");
            $table->foreign("user_id")->references("user_id")->on("users");
            $table->string("order_id")->unique();
            $table->decimal("amount", 10, 2);
            $table->enum("status", ['SUCCESS', "FAILED", "PENDING", "CANCELLED"]);
            $table->json("payment_gateway_response");
            $table->uuid("class_group_syllabus_id");
            $table->boolean("is_annual_fee_payment")->default(false);
            $table->unsignedBigInteger("course_annual_fee_id")->nullable();
            $table->foreign("course_annual_fee_id")->references("course_annual_fee_id")
                ->on("course_annual_fees");
            $table->unsignedBigInteger("syllabus_subscription_month_id")->nullable();
            $table->foreign("syllabus_subscription_month_id")->references("syllabus_subscription_month_id")
                ->on("syllabus_subscription_months");
            $table->foreign("class_group_syllabus_id")->references("class_group_syllabus_id")
                ->on("class_group_syllabuses");
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
        Schema::dropIfExists('user_payment_details');
    }
}
