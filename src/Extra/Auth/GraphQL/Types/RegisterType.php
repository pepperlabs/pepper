<?php

declare(strict_types=1);

namespace Pepper\Extra\Auth\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class RegisterType extends GraphQLType
{
    protected $attributes = [
        'name' => 'RegisterType',
        'description' => 'Register type',
        'model' => \App\User::class,
    ];

    public function fields(): array
    {
        // Not used
        return [
            'name' => [
                'name' => 'name',
                'type' => Type::string(),
            ],
            'email' => [
                'name' => 'email',
                'type' => Type::string(),
            ],
            'password' => [
                'name' => 'password',
                'type' => Type::string(),
            ],
            'password_confirmation' => [
                'name' => 'password_confirmation',
                'type' => Type::string(),
            ],
        ];
    }
}
