<?php
/**
 * @Date 09/08/20
 */

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Chapters;
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

$factory->define(Topics::class, function (Faker $faker) {
    $chapters = Chapters::pluck('chapter_id')->toArray();
    return [
        'topic_id' => $faker->uuid,
        'topic_name' => $faker->text(15),
        'topic_code' => strtoupper($faker->word()),
        'topic_description' => $faker->text(15),
        'video_url' => $faker->url,
        'is_published' => 1,
        'chapter_id' => $faker->randomElement($chapters)
    ];
});
