<?php

declare(strict_types=1);

use Faker\Generator as Faker;
use Rebing\GraphQL\Tests\Support\Models\User;

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});
