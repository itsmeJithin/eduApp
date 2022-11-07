<?php

use Illuminate\Database\Seeder;

class AddExamModes extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('exam_modes')->insert([
            'exam_mode_name' => "Easy",
            'exam_mode_code' => "EASY",
            'exam_mode_description' => "Easy Exams",
        ]);
        DB::table('exam_modes')->insert([
            'exam_mode_name' => "Medium",
            'exam_mode_code' => "MEDIUM",
            'exam_mode_description' => "Medium Exams",
        ]);
        DB::table('exam_modes')->insert([
            'exam_mode_name' => "Hard",
            'exam_mode_code' => "HARD",
            'exam_mode_description' => "Hard Exams",
        ]);
    }
}
