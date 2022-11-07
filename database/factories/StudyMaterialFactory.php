<?php
/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Chapters;
use App\Models\StudyMaterials;
use App\Models\Topics;
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

$factory->define(StudyMaterials::class, function (Faker $faker) {
    $topics = Topics::pluck('topic_id')->toArray();
    return [
        'study_material_id' => $faker->uuid,
        'study_material_name' => $faker->text(15),
        'study_material_description' => $faker->text(15),
        'topic_id' => $faker->randomElement($topics),
        "study_material_url" => "https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf"
    ];
});
