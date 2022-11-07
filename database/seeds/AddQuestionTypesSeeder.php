<?php

use Illuminate\Database\Seeder;

class AddQuestionTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\QuestionTypes::create([
            "question_type_id" => 1,
            "question_type_name" => "MCQ",
            "question_type_description" => "MCQ"
        ]);
    }
}
