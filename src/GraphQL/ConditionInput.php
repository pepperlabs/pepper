<?php

declare(strict_types=1);

namespace Pepper\GraphQL;

use App\GraphQL\Unions\AnyUnion;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;
use Rebing\GraphQL\Support\Facades\GraphQL;

class ConditionInput extends InputType
{
    protected $attributes = [
        'name' => 'ConditionInput',
        'description' => 'Available conditions',
    ];

    public function fields(): array
    {
        return [
            '_eq' => [
                'type' => GraphQL::type('AnyScalar'),
                'description' => 'A test field',
            ],
            '_neq' => [
                'type' => GraphQL::type('AnyScalar'),
                'description' => '...',
            ],
            '_gt' => [
                'type' => GraphQL::type('AnyScalar'),
                'description' => '...',
            ],
            '_lt' => [
                'type' => GraphQL::type('AnyScalar'),
                'description' => '...',
            ],
            '_gte' => [
                'type' => GraphQL::type('AnyScalar'),
                'description' => '...',
            ],
            '_lte' => [
                'type' => GraphQL::type('AnyScalar'),
                'description' => '...',
            ],
            '_in' => [
                'type' => Type::listOf(GraphQL::type('AnyScalar')),
                'description' => '...',
            ],
            '_nin' => [
                'type' => Type::listOf(GraphQL::type('AnyScalar')),
                'description' => '...',
            ],
            '_like' => [
                'type' => GraphQL::type('AnyScalar'),
                'description' => '...',
            ],
            '_nlike' => [
                'type' => GraphQL::type('AnyScalar'),
                'description' => '...',
            ],
            '_ilike' => [
                'type' => GraphQL::type('AnyScalar'),
                'description' => '...',
            ],
            '_nilike' => [
                'type' => GraphQL::type('AnyScalar'),
                'description' => '...',
            ],
            '_is_null' => [
                'type' => Type::boolean(),
                'description' => 'Checking for null values',
            ],
            '_date' => [
                'type' => GraphQL::type('AnyScalar'),
                'description' => 'Checking for date values',
            ],
            '_month' => [
                'type' => GraphQL::type('AnyScalar'),
                'description' => 'Checking for month values',
            ],
            '_day' => [
                'type' => GraphQL::type('AnyScalar'),
                'description' => 'Checking for day values',
            ],
            '_year' => [
                'type' => GraphQL::type('AnyScalar'),
                'description' => 'Checking for year values',
            ],
            '_time' => [
                'type' => GraphQL::type('AnyScalar'),
                'description' => 'Checking for time values',
            ]
        ];
    }
}
