<?php

use Faker\Generator as Faker;
use Tests\Support\Models\User;

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});
