<?php

declare(strict_types=1);

namespace Pepper\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class LoginType extends GraphQLType
{
    protected $attributes = [
        'name' => 'LoginType',
        'description' => 'Login type',
        'model' => \App\User::class,
    ];

    public function fields(): array
    {
        return [
            'token' => [
                'name' => 'token',
                'type' => Type::string(),
            ],
        ];
    }
}
