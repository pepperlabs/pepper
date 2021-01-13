<?php

namespace Pepper\Extra\Auth\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ForgotPasswordStatusType extends GraphQLType
{
    protected $attributes = [
        'name' => 'ForgotPasswordStatusType',
        'description' => 'Forgot Password Status Type',
    ];

    public function __construct()
    {
        $this->attributes['model'] = config('pepper.auth.model');
    }

    public function fields(): array
    {
        return [
            'status' => [
                'name' => 'status',
                'type' => Type::string(),
            ],
        ];
    }
}
