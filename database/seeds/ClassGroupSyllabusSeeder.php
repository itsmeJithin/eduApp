<?php

use App\Models\ClassGroups;
use App\Models\Syllabuses;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class ClassGroupSyllabusSeeder extends Seeder
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
        $id = Uuid::uuid4();
        DB::table('class_group_syllabuses')->insert([
            'class_group_syllabus_id' => $id,
            'class_group_id' => $classGroupId,
            'syllabus_id' => $syllabusId,
        ]);

        $classGroupId = ClassGroups::select("class_group_id")->where("class_group_code", "=", "PLUS_ONE_COMMERCE")->value("class_group_id");
        $syllabusId = Syllabuses::select("syllabus_id")->where("start_year", "=", "2020")->value("syllabus_id");
        $id = Uuid::uuid4();
        DB::table('class_group_syllabuses')->insert([
            'class_group_syllabus_id' => $id,
            'class_group_id' => $classGroupId,
            'syllabus_id' => $syllabusId,
        ]);

        $classGroupId = ClassGroups::select("class_group_id")->where("class_group_code", "=", "PLUS_TWO_COMPUTER_SCIENCE")->value("class_group_id");
        $syllabusId = Syllabuses::select("syllabus_id")->where("start_year", "=", "2020")->value("syllabus_id");
        $id = Uuid::uuid4();
        DB::table('class_group_syllabuses')->insert([
            'class_group_syllabus_id' => $id,
            'class_group_id' => $classGroupId,
            'syllabus_id' => $syllabusId,
        ]);

        $classGroupId = ClassGroups::select("class_group_id")->where("class_group_code", "=", "PLUS_TWO_COMMERCE")->value("class_group_id");
        $syllabusId = Syllabuses::select("syllabus_id")->where("start_year", "=", "2020")->value("syllabus_id");
        $id = Uuid::uuid4();
        DB::table('class_group_syllabuses')->insert([
            'class_group_syllabus_id' => $id,
            'class_group_id' => $classGroupId,
            'syllabus_id' => $syllabusId,
        ]);
    }
}
