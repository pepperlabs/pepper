<?php

declare(strict_types=1);

namespace Pepper\GraphQL;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class AggregateType extends GraphQLType
{
    protected $attributes = [
        'name' => 'AggregateType',
        'description' => 'A type'
    ];

    public function fields(): array
    {
        return [
            'count' => [
                'type' => Type::int(),
                'selectable' => false,
                'resolve' => function ($root, $args) {
                    return 999;
                }
            ]
        ];
    }
}
