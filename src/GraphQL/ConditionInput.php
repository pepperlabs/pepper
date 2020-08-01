<?php

declare(strict_types=1);

namespace Amirmasoud\Pepper\GraphQL;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class ConditionInput extends InputType
{
    protected $attributes = [
        'name' => 'ConditionInput',
        'description' => 'An example input',
    ];

    public function fields(): array
    {
        return [
            // generate this file
            '_eq' => [
                'type' => Type::string(),
                'description' => 'A test field',
            ],
            '_neq' => [
                'type' => Type::string(),
                'description' => '...',
            ],
            '_gt' => [
                'type' => Type::string(),
                'description' => '...',
            ],
            '_lt' => [
                'type' => Type::string(),
                'description' => '...',
            ],
            '_gte' => [
                'type' => Type::string(),
                'description' => '...',
            ],
            '_lte' => [
                'type' => Type::string(),
                'description' => '...',
            ],
            '_in' => [
                'type' => Type::listOf(Type::int()),
                'description' => '...',
            ],
            '_nin' => [
                'type' => Type::listOf(Type::int()),
                'description' => '...',
            ],
            '_like' => [
                'type' => Type::string(),
                'description' => '...',
            ],
            '_nlike' => [
                'type' => Type::string(),
                'description' => '...',
            ],
            '_ilike' => [
                'type' => Type::string(),
                'description' => '...',
            ],
            '_nilike' => [
                'type' => Type::string(),
                'description' => '...',
            ],
            '_is_null' => [
                'type' => Type::boolean(),
                'description' => 'Checking for null values',
            ],
            '_date' => [
                'type' => Type::string(),
                'description' => 'Checking for date values',
            ],
            '_month' => [
                'type' => Type::string(),
                'description' => 'Checking for month values',
            ],
            '_day' => [
                'type' => Type::string(),
                'description' => 'Checking for day values',
            ],
            '_year' => [
                'type' => Type::string(),
                'description' => 'Checking for year values',
            ],
            '_time' => [
                'type' => Type::string(),
                'description' => 'Checking for time values',
            ]
        ];
    }
}
