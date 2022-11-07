<?php

use App\Models\ClassGroups;
use App\Models\ClassGroupSyllabuses;
use App\Models\ClassGroupSyllabusSubjects;
use App\Models\Subjects;
use App\Models\SubscriptionMonths;
use App\Models\Syllabuses;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class AddLiveClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $classGroupId = ClassGroups::select("class_group_id")->where("class_group_code", "=", "PLUS_ONE_COMPUTER_SCIENCE")->value("class_group_id");
        $syllabusId = Syllabuses::select("syllabus_id")->where("start_year", "=", "2020")->value("syllabus_id");
        $classGroupSyllabusId = $this->getClassGroupSyllabusId($classGroupId, $syllabusId);
        $subscriptionMonths = SubscriptionMonths::pluck("subscription_month_id")->toArray();
        $subjectId = Subjects::select('subject_id')->where("subject_code", "=", "COMPUTER_SCIENCE")->value("subject_id");
        $classGroupSyllabusSubjectId = $this->getClassGroupSyllabusSubjectId($classGroupSyllabusId, $subjectId);
        $this->insertData($subscriptionMonths, $classGroupSyllabusSubjectId, "Computer science live class");

        $subjectId = Subjects::select('subject_id')->where("subject_code", "=", "PHYSICS")->value("subject_id");
        $classGroupSyllabusSubjectId = $this->getClassGroupSyllabusSubjectId($classGroupSyllabusId, $subjectId);
        $this->insertData($subscriptionMonths, $classGroupSyllabusSubjectId, "Physics live class");

    }

    public function insertData($subscriptionMonths, $classGroupSyllabusSubjectId, $description)
    {
        foreach ($subscriptionMonths as $month) {
            $primaryKey = Uuid::uuid4();
            DB::table("live_classes")->insert([
                "live_class_id" => $primaryKey,
                "class_group_syllabus_subject_id" => $classGroupSyllabusSubjectId,
                "subscription_month_id" => $month,
                "live_class_description" => $description,
                "live_class_url" => "https://www.youtube.com/watch?v=AbCTlemwZ1k"
            ]);
        }
    }

    /**
     * @param $classGroupId
     * @param $syllabusId
     * @return mixed
     */
    public function getClassGroupSyllabusId($classGroupId, $syllabusId)
    {
        return ClassGroupSyllabuses::select("class_group_syllabus_id")
            ->where("class_group_id", "=", $classGroupId)
            ->where("syllabus_id", "=", $syllabusId)
            ->value("class_group_syllabus_id");
    }

    /**
     * @param $classGroupSyllabusId
     * @param $subjectId
     * @return mixed
     */
    public function getClassGroupSyllabusSubjectId($classGroupSyllabusId, $subjectId)
    {
        return ClassGroupSyllabusSubjects::select('class_group_syllabus_subject_id')
            ->where('class_group_syllabus_id', "=", $classGroupSyllabusId)
            ->where("subject_id", "=", $subjectId)
            ->value("class_group_syllabus_subject_id");
    }
}
