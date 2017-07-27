<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'id' => $faker->randomNumber(),
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(Ultraware\Roles\Models\Role::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->words(2, true),
        'slug' => $faker->slug(2),
        'description' => '',
    ];
});

$factory->define(Ultraware\Roles\Models\Permission::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->words(2, true),
        'slug' => $faker->slug(2),
        'description' => '',
        'model' => $faker->words(1, true),
    ];
});
