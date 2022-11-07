<?php

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class ClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('classes')->insert([
            'class_id' => Uuid::uuid4(),
            'class_name' => "Plus One",
            'class_code' => 'PLUS_ONE',
            'class_description' => 'Plus one',
            'course_id' => 1,
        ]);
        DB::table('classes')->insert([
            'class_id' => Uuid::uuid4(),
            'class_name' => "Plus Two",
            'class_code' => 'PLUS_TWO',
            'class_description' => 'Plus Two',
            'course_id' => 1,
        ]);
        DB::table('classes')->insert([
            'class_id' => Uuid::uuid4(),
            'class_name' => "Plus One",
            'class_code' => 'VHSE_PLUS_ONE',
            'class_description' => 'Plus one',
            'course_id' => 2,
        ]);
        DB::table('classes')->insert([
            'class_id' => Uuid::uuid4(),
            'class_name' => "Plus Two",
            'class_code' => 'VHSE_PLUS_TWO',
            'class_description' => 'Plus Two',
            'course_id' => 2,
        ]);
    }
}
