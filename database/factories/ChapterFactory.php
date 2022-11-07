<?php


/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Chapters;
use App\Models\ClassGroupSyllabusSubjects;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Chapters::class, function (Faker $faker) {
    $classGroupSyllabusSubjects = ClassGroupSyllabusSubjects::pluck('class_group_syllabus_subject_id')->toArray();
    return [
        'chapter_id' => $faker->uuid,
        'chapter_name' => $faker->text(15),
        'chapter_code' => $faker->text(15),
        'chapter_description' => $faker->text(15),
        'class_group_syllabus_subject_id' => $faker->randomElement($classGroupSyllabusSubjects),
    ];
});

