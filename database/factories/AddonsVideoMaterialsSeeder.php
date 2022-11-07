<?php
/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\AddonsVideoMaterials;
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

$factory->define(AddonsVideoMaterials::class, function (Faker $faker) {
    $pdfFiles = [
        "https://www.youtube.com/watch?v=ju2pqsXOVJI",
        "https://www.youtube.com/watch?v=9AMPsDXGAxY",
        "https://www.youtube.com/watch?v=W8ykZNSLDqE",
        "https://www.youtube.com/watch?v=LiqZq1iLuQI",
        "https://www.youtube.com/watch?v=ObZwFExwzOo",
        "https://www.youtube.com/watch?v=HvjYxuU6LHk",
        "https://www.youtube.com/watch?v=r1MXwyiGi_U",
        "https://www.youtube.com/watch?v=0oBi8OmjLIg"
    ];
    return [
        'avm_id' => $faker->uuid,
        'avm_name' => $faker->text(15),
        'avm_description' => $faker->text(15),
        "avm_url" => $faker->randomElement($pdfFiles)
    ];
});
