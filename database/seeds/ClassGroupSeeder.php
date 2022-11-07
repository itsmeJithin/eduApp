<?php

use App\Models\Classes;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class ClassGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $class = DB::table('classes')
            ->select('class_id')
            ->where('class_code', '=', 'PLUS_ONE')
            ->first();
        $classId = $class->class_id;
        DB::table('class_groups')->insert([
            'class_group_id' => Uuid::uuid4(),
            'class_group_name' => "Computer Science",
            'class_group_code' => "PLUS_ONE_COMPUTER_SCIENCE",
            'class_group_description' => "Computer Science Group",
            'class_id' => $classId,
        ]);

        DB::table('class_groups')->insert([
            'class_group_id' => Uuid::uuid4(),
            'class_group_name' => "Commerce",
            'class_group_code' => "PLUS_ONE_COMMERCE",
            'class_group_description' => "Commerce Group",
            'class_id' => $classId,
        ]);

        DB::table('class_groups')->insert([
            'class_group_id' => Uuid::uuid4(),
            'class_group_name' => "Humanities",
            'class_group_code' => "PLUS_ONE_HUMANITIES",
            'class_group_description' => "Humanities Group",
            'class_id' => $classId,
        ]);

        $class = DB::table('classes')
            ->select('class_id')
            ->where('class_code', '=', 'PLUS_TWO')
            ->first();
        $classId = $class->class_id;
        DB::table('class_groups')->insert([
            'class_group_id' => Uuid::uuid4(),
            'class_group_name' => "Computer Science",
            'class_group_code' => "PLUS_TWO_COMPUTER_SCIENCE",
            'class_group_description' => "Computer Science Group",
            'class_id' => $classId,
        ]);

        DB::table('class_groups')->insert([
            'class_group_id' => Uuid::uuid4(),
            'class_group_name' => "Commerce",
            'class_group_code' => "PLUS_TWO_COMMERCE",
            'class_group_description' => "Commerce Group",
            'class_id' => $classId,
        ]);

        DB::table('class_groups')->insert([
            'class_group_id' => Uuid::uuid4(),
            'class_group_name' => "Humanities",
            'class_group_code' => "PLUS_TWO_HUMANITIES",
            'class_group_description' => "Humanities Group",
            'class_id' => $classId,
        ]);

        $class = DB::table('classes')
            ->select('class_id')
            ->where('class_code', '=', 'VHSE_PLUS_ONE')
            ->first();
        $classId = $class->class_id;
        DB::table('class_groups')->insert([
            'class_group_id' => Uuid::uuid4(),
            'class_group_name' => "Computer Science",
            'class_group_code' => "VHSE_PLUS_ONE_COMPUTER_SCIENCE",
            'class_group_description' => "Computer Science Group",
            'class_id' => $classId,
        ]);

        DB::table('class_groups')->insert([
            'class_group_id' => Uuid::uuid4(),
            'class_group_name' => "Commerce",
            'class_group_code' => "VHSE_PLUS_ONE_COMMERCE",
            'class_group_description' => "Commerce Group",
            'class_id' => $classId,
        ]);

        DB::table('class_groups')->insert([
            'class_group_id' => Uuid::uuid4(),
            'class_group_name' => "Humanities",
            'class_group_code' => "VHSE_PLUS_ONE_HUMANITIES",
            'class_group_description' => "Humanities Group",
            'class_id' => $classId,
        ]);

        $class = DB::table('classes')
            ->select('class_id')
            ->where('class_code', '=', 'VHSE_PLUS_TWO')
            ->first();
        $classId = $class->class_id;
        DB::table('class_groups')->insert([
            'class_group_id' => Uuid::uuid4(),
            'class_group_name' => "Computer Science",
            'class_group_code' => "VHSE_PLUS_TWO_COMPUTER_SCIENCE",
            'class_group_description' => "Computer Science Group",
            'class_id' => $classId,
        ]);

        DB::table('class_groups')->insert([
            'class_group_id' => Uuid::uuid4(),
            'class_group_name' => "Commerce",
            'class_group_code' => "VHSE_PLUS_TWO_COMMERCE",
            'class_group_description' => "Commerce Group",
            'class_id' => $classId,
        ]);

        DB::table('class_groups')->insert([
            'class_group_id' => Uuid::uuid4(),
            'class_group_name' => "Humanities",
            'class_group_code' => "VHSE_PLUS_TWO_HUMANITIES",
            'class_group_description' => "Humanities Group",
            'class_id' => $classId,
        ]);
    }
}
