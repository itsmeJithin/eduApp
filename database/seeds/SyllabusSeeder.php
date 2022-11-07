<?php

use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class SyllabusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('syllabuses')->insert([
            'syllabus_id' => Uuid::uuid4(),
            'syllabus_name' => "2020-2021",
            'start_year' => "2020",
            'end_year' => "2021",
        ]);
    }
}
