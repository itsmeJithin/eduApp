<?php

use App\Models\ClassGroupSyllabuses;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class SubjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $computerScienceId = Uuid::uuid4();
        DB::table('subjects')->insert([
            'subject_id' => $computerScienceId,
            'subject_name' => "Computer Science",
            'subject_code' => "COMPUTER_SCIENCE",
            'subject_description' => "Computer Science",
        ]);

        $physicsId = Uuid::uuid4();
        DB::table('subjects')->insert([
            'subject_id' => $physicsId,
            'subject_name' => "Physics",
            'subject_code' => "PHYSICS",
            'subject_description' => "Physics",
        ]);

        $chemistryId = Uuid::uuid4();
        DB::table('subjects')->insert([
            'subject_id' => $chemistryId,
            'subject_name' => "Chemistry",
            'subject_code' => "CHEMISTRY",
            'subject_description' => "Chemistry",
        ]);

        $mathematicsId = Uuid::uuid4();
        DB::table('subjects')->insert([
            'subject_id' => $mathematicsId,
            'subject_name' => "Mathematics",
            'subject_code' => "MATHEMATICS",
            'subject_description' => "Mathematics",
        ]);

        $englishId = Uuid::uuid4();
        DB::table('subjects')->insert([
            'subject_id' => $englishId,
            'subject_name' => "English",
            'subject_code' => "ENGLISH",
            'subject_description' => "English",
        ]);

        $malayalamId = Uuid::uuid4();
        DB::table('subjects')->insert([
            'subject_id' => $malayalamId,
            'subject_name' => "Malayalam",
            'subject_code' => "MALAYALAM",
            'subject_description' => "Malayalam",
        ]);

        $accountancyId = Uuid::uuid4();
        DB::table('subjects')->insert([
            'subject_id' => $accountancyId,
            'subject_name' => "Accountancy",
            'subject_code' => "ACCOUNTANCY",
            'subject_description' => "Accountancy",
        ]);

        $economicsId = Uuid::uuid4();
        DB::table('subjects')->insert([
            'subject_id' => $economicsId,
            'subject_name' => "Economics",
            'subject_code' => "ECONOMICS",
            'subject_description' => "Economics",
        ]);

        $businessId = Uuid::uuid4();
        DB::table('subjects')->insert([
            'subject_id' => $businessId,
            'subject_name' => "Business Studies",
            'subject_code' => "BUSINESS_STUDIES",
            'subject_description' => "Business Studies",
        ]);

        $historyId = Uuid::uuid4();
        DB::table('subjects')->insert([
            'subject_id' => $historyId,
            'subject_name' => "History",
            'subject_code' => "HISTORY",
            'subject_description' => "History",
        ]);

        $politicalScienceId = Uuid::uuid4();
        DB::table('subjects')->insert([
            'subject_id' => $politicalScienceId,
            'subject_name' => "Political Science",
            'subject_code' => "POLITICAL_SCIENCE",
            'subject_description' => "Political Science",
        ]);

        $hindiId = Uuid::uuid4();
        DB::table('subjects')->insert([
            'subject_id' => $hindiId,
            'subject_name' => "Hindi",
            'subject_code' => "HINDI",
            'subject_description' => "Hindi",
        ]);
    }
}
