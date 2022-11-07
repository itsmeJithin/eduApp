<?php

use App\Models\ClassGroups;
use App\Models\ClassGroupSyllabuses;
use App\Models\ClassGroupSyllabusSubjects;
use App\Models\Subjects;
use App\Models\Syllabuses;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class ChapterSeeder extends Seeder
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
        $classGroupSyllabusSubjectId = $this->getClassGroupSyllabusSubjectId($classGroupSyllabusId, $subjectId);
        for ($i = 1; $i <= 4; $i++) {
            $this->insertData($classGroupSyllabusSubjectId, "Computer Science" . $i);
        }
        $subjectId = Subjects::select('subject_id')->where("subject_code", "=", "PHYSICS")->value("subject_id");
        $classGroupSyllabusSubjectId = $this->getClassGroupSyllabusSubjectId($classGroupSyllabusId, $subjectId);
        for ($i = 1; $i <= 4; $i++) {
            $this->insertData($classGroupSyllabusSubjectId, "Physics" . $i);
        }

        $subjectId = Subjects::select('subject_id')->where("subject_code", "=", "CHEMISTRY")->value("subject_id");
        $classGroupSyllabusSubjectId = $this->getClassGroupSyllabusSubjectId($classGroupSyllabusId, $subjectId);
        for ($i = 1; $i <= 4; $i++) {
            $this->insertData($classGroupSyllabusSubjectId, "Chemistry" . $i);
        }

        $subjectId = Subjects::select('subject_id')->where("subject_code", "=", "MATHEMATICS")->value("subject_id");
        $classGroupSyllabusSubjectId = $this->getClassGroupSyllabusSubjectId($classGroupSyllabusId, $subjectId);
        for ($i = 1; $i <= 4; $i++) {
            $this->insertData($classGroupSyllabusSubjectId, "Mathematics" . $i);
        }

        $subjectId = Subjects::select('subject_id')->where("subject_code", "=", "ENGLISH")->value("subject_id");
        $classGroupSyllabusSubjectId = $this->getClassGroupSyllabusSubjectId($classGroupSyllabusId, $subjectId);
        for ($i = 1; $i <= 4; $i++) {
            $this->insertData($classGroupSyllabusSubjectId, "English" . $i);
        }
        //<<======================***===========================>
        $classGroupId = ClassGroups::select("class_group_id")->where("class_group_code", "=", "PLUS_ONE_COMMERCE")->value("class_group_id");
        $classGroupSyllabusId = $this->getClassGroupSyllabusId($classGroupId, $syllabusId);

        $subjectId = Subjects::select('subject_id')->where("subject_code", "=", "MALAYALAM")->value("subject_id");
        $classGroupSyllabusSubjectId = $this->getClassGroupSyllabusSubjectId($classGroupSyllabusId, $subjectId);
        for ($i = 1; $i <= 4; $i++) {
            $this->insertData($classGroupSyllabusSubjectId, "Malayalam" . $i);
        }

        $subjectId = Subjects::select('subject_id')->where("subject_code", "=", "ACCOUNTANCY")->value("subject_id");
        $classGroupSyllabusSubjectId = $this->getClassGroupSyllabusSubjectId($classGroupSyllabusId, $subjectId);
        for ($i = 1; $i <= 4; $i++) {
            $this->insertData($classGroupSyllabusSubjectId, "Accountancy" . $i);
        }

        $subjectId = Subjects::select('subject_id')->where("subject_code", "=", "ECONOMICS")->value("subject_id");
        $classGroupSyllabusSubjectId = $this->getClassGroupSyllabusSubjectId($classGroupSyllabusId, $subjectId);
        for ($i = 1; $i <= 4; $i++) {
            $this->insertData($classGroupSyllabusSubjectId, "Economics" . $i);
        }

        $subjectId = Subjects::select('subject_id')->where("subject_code", "=", "BUSINESS_STUDIES")->value("subject_id");
        $classGroupSyllabusSubjectId = $this->getClassGroupSyllabusSubjectId($classGroupSyllabusId, $subjectId);
        for ($i = 1; $i <= 4; $i++) {
            $this->insertData($classGroupSyllabusSubjectId, "Business Studies" . $i);
        }

        $subjectId = Subjects::select('subject_id')->where("subject_code", "=", "ENGLISH")->value("subject_id");
        $classGroupSyllabusSubjectId = $this->getClassGroupSyllabusSubjectId($classGroupSyllabusId, $subjectId);
        for ($i = 1; $i <= 4; $i++) {
            $this->insertData($classGroupSyllabusSubjectId, "English" . $i);
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

    public function getClassGroupSyllabusSubjectId($classGroupSyllabusId, $subjectId)
    {
        return ClassGroupSyllabusSubjects::select('class_group_syllabus_subject_id')
            ->where('class_group_syllabus_id', "=", $classGroupSyllabusId)
            ->where("subject_id", "=", $subjectId)
            ->value("class_group_syllabus_subject_id");
    }

    /**
     * @param $classGroupSyllabusId
     * @param $name
     */
    public function insertData($classGroupSyllabusId, $name)
    {
        $primaryKey = Uuid::uuid4();
        DB::table('chapters')->insert([
            'chapter_id' => $primaryKey,
            'chapter_name' => $name,
            'chapter_code' => str_replace(" ", "_", strtoupper($name)),
            'class_group_syllabus_subject_id' => $classGroupSyllabusId,
            'chapter_description' => "This is chapter " . $name
        ]);
    }
}
