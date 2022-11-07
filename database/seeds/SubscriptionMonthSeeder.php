<?php

use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class SubscriptionMonthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('subscription_months')->insert([
            'subscription_month_id' => Uuid::uuid4(),
            'subscription_month_name' => "January",
            'subscription_month_code' => "JAN",
            'subscription_month_order' => 1,
            'subscription_month_days' => 31
        ]);

        DB::table('subscription_months')->insert([
            'subscription_month_id' => Uuid::uuid4(),
            'subscription_month_name' => "February",
            'subscription_month_code' => "FEB",
            'subscription_month_order' => 2,
            'subscription_month_days' => 28
        ]);

        DB::table('subscription_months')->insert([
            'subscription_month_id' => Uuid::uuid4(),
            'subscription_month_name' => "March",
            'subscription_month_code' => "MAR",
            'subscription_month_order' => 3,
            'subscription_month_days' => 31
        ]);

        DB::table('subscription_months')->insert([
            'subscription_month_id' => Uuid::uuid4(),
            'subscription_month_name' => "April",
            'subscription_month_code' => "APR",
            'subscription_month_order' => 4,
            'subscription_month_days' => 30
        ]);

        DB::table('subscription_months')->insert([
            'subscription_month_id' => Uuid::uuid4(),
            'subscription_month_name' => "May",
            'subscription_month_code' => "MAY",
            'subscription_month_order' => 5,
            'subscription_month_days' => 31
        ]);

        DB::table('subscription_months')->insert([
            'subscription_month_id' => Uuid::uuid4(),
            'subscription_month_name' => "June",
            'subscription_month_code' => "JUN",
            'subscription_month_order' => 6,
            'subscription_month_days' => 30
        ]);

        DB::table('subscription_months')->insert([
            'subscription_month_id' => Uuid::uuid4(),
            'subscription_month_name' => "July",
            'subscription_month_code' => "JUL",
            'subscription_month_order' => 7,
            'subscription_month_days' => 31
        ]);

        DB::table('subscription_months')->insert([
            'subscription_month_id' => Uuid::uuid4(),
            'subscription_month_name' => "August",
            'subscription_month_code' => "AUG",
            'subscription_month_order' => 8,
            'subscription_month_days' => 31
        ]);

        DB::table('subscription_months')->insert([
            'subscription_month_id' => Uuid::uuid4(),
            'subscription_month_name' => "September",
            'subscription_month_code' => "SEP",
            'subscription_month_order' => 9,
            'subscription_month_days' => 30
        ]);

        DB::table('subscription_months')->insert([
            'subscription_month_id' => Uuid::uuid4(),
            'subscription_month_name' => "October",
            'subscription_month_code' => "OCT",
            'subscription_month_order' => 10,
            'subscription_month_days' => 31
        ]);

        DB::table('subscription_months')->insert([
            'subscription_month_id' => Uuid::uuid4(),
            'subscription_month_name' => "November",
            'subscription_month_code' => "NOV",
            'subscription_month_order' => 11,
            'subscription_month_days' => 30
        ]);

        DB::table('subscription_months')->insert([
            'subscription_month_id' => Uuid::uuid4(),
            'subscription_month_name' => "December",
            'subscription_month_code' => "DEC",
            'subscription_month_order' => 12,
            'subscription_month_days' => 31
        ]);
    }
}
