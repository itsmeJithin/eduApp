<?php


/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\StaffUsers;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

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

$factory->define(StaffUsers::class, function (Faker $faker) {
    return [
        'staff_name' => $faker->name,
        'staff_email' => $faker->unique()->safeEmail,
        'staff_code' => $faker->unique()->randomAscii,
        'staff_phone_number' => $faker->unique()->phoneNumber,
        'staff_password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => Str::random(10),
        'activation_token'=>Str::random(20)
    ];
});
