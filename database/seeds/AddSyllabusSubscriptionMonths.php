<?php

use App\Models\ClassGroupSyllabuses;
use App\Models\ClassGroupSyllabusSubjects;
use App\Models\SubscriptionMonths;
use Illuminate\Database\Seeder;

class AddSyllabusSubscriptionMonths extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $subscriptionMonths = SubscriptionMonths::pluck("subscription_month_id")->toArray();
        $classGroupSyllabuses = ClassGroupSyllabuses::pluck("class_group_syllabus_id")->toArray();
        foreach ($classGroupSyllabuses as $syllabus) {
            foreach ($subscriptionMonths as $subscriptionMonth) {
                DB::table('syllabus_subscription_months')->insert([
                    'class_group_syllabus_id' => $syllabus,
                    'subscription_month_id' => $subscriptionMonth,
                    'price' => mt_rand(10, 400) / 10
                ]);
            }
        }
    }
}
