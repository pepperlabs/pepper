<?php

declare(strict_types=1);

namespace Pepper;

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
                'description' => '= equivalent',
            ],
            '_neq' => [
                'type' => GraphQL::type('AnyScalar'),
                'description' => '<> or != equivalent',
            ],
            '_gt' => [
                'type' => GraphQL::type('AnyScalar'),
                'description' => '> equivalent',
            ],
            '_lt' => [
                'type' => GraphQL::type('AnyScalar'),
                'description' => '< equivalent',
            ],
            '_gte' => [
                'type' => GraphQL::type('AnyScalar'),
                'description' => '>= equivalent',
            ],
            '_lte' => [
                'type' => GraphQL::type('AnyScalar'),
                'description' => '<= equivalent',
            ],
            '_in' => [
                'type' => Type::listOf(GraphQL::type('AnyScalar')),
                'description' => 'IN equivalent',
            ],
            '_nin' => [
                'type' => Type::listOf(GraphQL::type('AnyScalar')),
                'description' => 'NOT IN equivalent',
            ],
            '_like' => [
                'type' => GraphQL::type('AnyScalar'),
                'description' => 'LIKE equivalent',
            ],
            '_nlike' => [
                'type' => GraphQL::type('AnyScalar'),
                'description' => 'NOT LIKE equivalent',
            ],
            '_ilike' => [
                'type' => GraphQL::type('AnyScalar'),
                'description' => 'ILIKE equivalent',
            ],
            '_nilike' => [
                'type' => GraphQL::type('AnyScalar'),
                'description' => 'NOT ILIKE equivalent',
            ],
            '_is_null' => [
                'type' => Type::boolean(),
                'description' => 'IS NULL equivalent',
            ],
            '_date' => [
                'type' => GraphQL::type('AnyScalar'),
                'description' => 'Laravel whereDate() equivalent',
            ],
            '_month' => [
                'type' => GraphQL::type('AnyScalar'),
                'description' => 'Laravel whereMonth() equivalent',
            ],
            '_day' => [
                'type' => GraphQL::type('AnyScalar'),
                'description' => 'Laravel whereDay() equivalent',
            ],
            '_year' => [
                'type' => GraphQL::type('AnyScalar'),
                'description' => 'Laravel whereYear() equivalent',
            ],
            '_time' => [
                'type' => GraphQL::type('AnyScalar'),
                'description' => 'Laravel whereTime() equivalent',
            ]
        ];
    }
}
