<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('courses')->insert([
            'course_id' => 1,
            'course_name' => "HSE",
            'course_code' => "HSE",
            'course_description' => "Higher Secondary Education",
        ]);

        DB::table('courses')->insert([
            'course_id' => 2,
            'course_name' => "VHSE",
            'course_code' => "VHSE",
            'course_description' => "Vocational Higher Secondary Education",
        ]);
    }
}
