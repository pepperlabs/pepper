<?php

return [
    'available' => [
        'mutation' => [
            'register' => \Pepper\Extra\Auth\GraphQL\Mutations\RegisterMutation::class,
        ],
        'query' => [
            'login' => \Pepper\Extra\Auth\GraphQL\Queries\LoginQuery::class,
        ],
    ],

    'global' => [
        'JWTType' => \Pepper\Extra\Auth\GraphQL\Types\JWTType::class,
    ],
];
