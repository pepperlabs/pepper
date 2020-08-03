<?php

declare(strict_types=1);

namespace Pepper\GraphQL;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class AggregateType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Aggregate',
        'description' => 'A type'
    ];

    public function fields(): array
    {
        return [
            'count' => [
                'type' => Type::int(),
                'selectable' => false,
            ],
            'sum' => [
                'type' => Type::listOf(\Rebing\GraphQL\Support\Facades\GraphQL::type('User')),
                'selectable' => false,
            ]
        ];
    }

    protected function resolveCountField($root, $args)
    {
        dd(1);
    }
}
