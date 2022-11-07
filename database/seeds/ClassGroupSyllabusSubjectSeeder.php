<?php

use App\Models\ClassGroups;
use App\Models\ClassGroupSyllabuses;
use App\Models\Subjects;
use App\Models\Syllabuses;
use Illuminate\Database\Seeder;

class ClassGroupSyllabusSubjectSeeder extends Seeder
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
        $subjectId = Subjects::select('subject_id')->where("subject_code", "=", "COMPUTER_SCIENCE")->value("subject_id");
        $this->insertData($classGroupSyllabusId, $subjectId);

        $subjectId = Subjects::select('subject_id')->where("subject_code", "=", "PHYSICS")->value("subject_id");
        $this->insertData($classGroupSyllabusId, $subjectId);

        $subjectId = Subjects::select('subject_id')->where("subject_code", "=", "CHEMISTRY")->value("subject_id");
        $this->insertData($classGroupSyllabusId, $subjectId);

        $subjectId = Subjects::select('subject_id')->where("subject_code", "=", "MATHEMATICS")->value("subject_id");
        $this->insertData($classGroupSyllabusId, $subjectId);

        $subjectId = Subjects::select('subject_id')->where("subject_code", "=", "ENGLISH")->value("subject_id");
        $this->insertData($classGroupSyllabusId, $subjectId);

        /**
         * =========================================><=====================
         */
        $classGroupId = ClassGroups::select("class_group_id")->where("class_group_code", "=", "PLUS_ONE_COMMERCE")->value("class_group_id");
        $syllabusId = Syllabuses::select("syllabus_id")->where("start_year", "=", "2020")->value("syllabus_id");
        $classGroupSyllabusId = $this->getClassGroupSyllabusId($classGroupId, $syllabusId);
        $subjectId = Subjects::select('subject_id')->where("subject_code", "=", "MALAYALAM")->value("subject_id");
        $this->insertData($classGroupSyllabusId, $subjectId);

        $subjectId = Subjects::select('subject_id')->where("subject_code", "=", "ACCOUNTANCY")->value("subject_id");
        $this->insertData($classGroupSyllabusId, $subjectId);

        $subjectId = Subjects::select('subject_id')->where("subject_code", "=", "ECONOMICS")->value("subject_id");
        $this->insertData($classGroupSyllabusId, $subjectId);

        $subjectId = Subjects::select('subject_id')->where("subject_code", "=", "BUSINESS_STUDIES")->value("subject_id");
        $this->insertData($classGroupSyllabusId, $subjectId);

        $subjectId = Subjects::select('subject_id')->where("subject_code", "=", "ENGLISH")->value("subject_id");
        $this->insertData($classGroupSyllabusId, $subjectId);

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
     */
    public function insertData($classGroupSyllabusId, $subjectId)
    {
        DB::table('class_group_syllabus_subjects')->insert([
            'class_group_syllabus_id' => $classGroupSyllabusId,
            'subject_id' => $subjectId,
        ]);
    }
}
