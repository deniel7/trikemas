<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'full_name' => $faker->name,
        'username' => $faker->userName,
        'email' => $faker->email,
        'role_id' => 1,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
        'created_by' => 1,
        'updated_by' => 1
    ];
});

$factory->define(App\Example::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'description' => $faker->sentence($nbWords = 6, $variableNbWords = true)
    ];
});