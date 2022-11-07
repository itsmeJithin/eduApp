<?php
/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\AddonsStudyMaterials;
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

$factory->define(AddonsStudyMaterials::class, function (Faker $faker) {
    $pdfFiles = [
        "http://www.africau.edu/images/default/sample.pdf",
        "https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf",
        "https://en.unesco.org/inclusivepolicylab/sites/default/files/dummy-pdf_2.pdf",
        "https://hwpi.harvard.edu/files/torman/files/sample.pdf?m=1594914296"
    ];
    return [
        'asm_id' => $faker->uuid,
        'asm_name' => $faker->text(15),
        'asm_description' => $faker->text(15),
        "asm_url" => $faker->randomElement($pdfFiles)
    ];
});
