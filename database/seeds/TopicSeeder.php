<?php

use App\Models\Chapters;
use App\Models\ClassGroups;
use App\Models\ClassGroupSyllabuses;
use App\Models\SubscriptionMonths;
use App\Models\Syllabuses;
use App\Models\SyllabusSubscriptionMonths;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class TopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($j = 1; $j <= 4; $j++) {
            $chapterId = Chapters::select("chapter_id")
                ->where("chapter_code", "=", "COMPUTER_SCIENCE" . $j)
                ->value("chapter_id");
            $classGroupSyllabusId = $this->getClassGroupSyllabusId("PLUS_ONE_COMPUTER_SCIENCE", "2020");
            $subscriptionMonths = SubscriptionMonths::where('is_active', 1)
                ->orderBy('subscription_month_order')
                ->get();
            foreach ($subscriptionMonths as $month) {
                $name = strtolower($month->subscription_month_code) . " computer science topic";
                $this->insertData($chapterId, $name, $classGroupSyllabusId, $month->subscription_month_id);
            }

            $chapterId = Chapters::select("chapter_id")
                ->where("chapter_code", "=", "PHYSICS" . $j)
                ->value("chapter_id");
            $classGroupSyllabusId = $this->getClassGroupSyllabusId("PLUS_ONE_COMPUTER_SCIENCE", "2020");
            $subscriptionMonths = SubscriptionMonths::where('is_active', 1)
                ->orderBy('subscription_month_order')
                ->get();
            foreach ($subscriptionMonths as $month) {
                $name = strtolower($month->subscription_month_code) . " physics topic";
                $this->insertData($chapterId, $name, $classGroupSyllabusId, $month->subscription_month_id);
            }

            //<<<<<<<<<===============>>>>>>><<<<<==================>>>>>>

            $chapterId = Chapters::select("chapter_id")
                ->where("chapter_code", "=", "MALAYALAM" . $j)
                ->value("chapter_id");
            $classGroupSyllabusId = $this->getClassGroupSyllabusId("PLUS_ONE_COMMERCE", "2020");
            $subscriptionMonths = SubscriptionMonths::where('is_active', 1)
                ->orderBy('subscription_month_order')
                ->get();
            foreach ($subscriptionMonths as $month) {
                $name = strtolower($month->subscription_month_code) . " malayalam topic";
                $this->insertData($chapterId, $name, $classGroupSyllabusId, $month->subscription_month_id);
            }

            $chapterId = Chapters::select("chapter_id")
                ->where("chapter_code", "=", "ACCOUNTANCY" . $j)
                ->value("chapter_id");
            $classGroupSyllabusId = $this->getClassGroupSyllabusId("PLUS_ONE_COMMERCE", "2020");
            $subscriptionMonths = SubscriptionMonths::where('is_active', 1)
                ->orderBy('subscription_month_order')
                ->get();
            foreach ($subscriptionMonths as $month) {
                $name = strtolower($month->subscription_month_code) . " accountancy topic";
                $this->insertData($chapterId, $name, $classGroupSyllabusId, $month->subscription_month_id);
            }


            $chapterId = Chapters::select("chapter_id")
                ->where("chapter_code", "=", "ECONOMICS" . $j)
                ->value("chapter_id");
            $classGroupSyllabusId = $this->getClassGroupSyllabusId("PLUS_ONE_COMMERCE", "2020");
            $subscriptionMonths = SubscriptionMonths::where('is_active', 1)
                ->orderBy('subscription_month_order')
                ->get();
            foreach ($subscriptionMonths as $month) {
                $name = strtolower($month->subscription_month_code) . " economics topic";
                $this->insertData($chapterId, $name, $classGroupSyllabusId, $month->subscription_month_id);
            }
        }
    }


    public function insertData($chapterId, $topicName, $classGroupSyllabusId, $monthId)
    {
        for ($i = 1; $i <= 4; $i++) {
            $topicId = Uuid::uuid4();
            DB::table("topics")->insert(['topic_id' => $topicId,
                'topic_name' => $topicName . $i,
                'topic_code' => str_replace(" ", "_", strtoupper($topicName)) . $i,
                'topic_description' => $topicName,
                'video_url' => "https://www.youtube.com/watch?v=zzMLg3Ys5vI",
                'is_published' => 1,
                'chapter_id' => $chapterId]);

            $subscriptionMonthId = SyllabusSubscriptionMonths::select('syllabus_subscription_month_id')
                ->where("class_group_syllabus_id", "=", "$classGroupSyllabusId")
                ->where("subscription_month_id", "=", "$monthId")
                ->value("syllabus_subscription_month_id");
            DB::table('ss_month_topics')->insert([
                'syllabus_subscription_month_id' => $subscriptionMonthId,
                'topic_id' => $topicId
            ]);
        }
    }

    /**
     * @param $group
     * @param $syllabusYear
     * @return mixed
     */
    public function getClassGroupSyllabusId($group, $syllabusYear)
    {
        $classGroupId = ClassGroups::select("class_group_id")->where("class_group_code", "=", $group)->value("class_group_id");
        $syllabusId = Syllabuses::select("syllabus_id")->where("start_year", "=", $syllabusYear)->value("syllabus_id");
        return ClassGroupSyllabuses::select("class_group_syllabus_id")
            ->where("class_group_id", "=", $classGroupId)
            ->where("syllabus_id", "=", $syllabusId)
            ->value("class_group_syllabus_id");
    }
}
