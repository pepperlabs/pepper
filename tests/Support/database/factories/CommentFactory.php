<?php

declare(strict_types=1);

use Faker\Generator as Faker;
use Tests\Support\Models\Comment;

$factory->define(Comment::class, function (Faker $faker) {
    return [
        'title' => $faker->title,
        'body' => $faker->sentence,
    ];
});
