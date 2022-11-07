<?php
/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\LatestNews;
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

$factory->define(LatestNews::class, function (Faker $faker) {
    return [
        'latest_news_title' => $faker->realText(50),
        'latest_news_description' => $faker->realText(200),
        "latest_news_image_url" => "https://picsum.photos/300/100"
    ];
});
