<?php

declare(strict_types=1);

namespace Pepper\GraphQL;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class FieldAggregateType extends GraphQLType
{
    protected $attributes = [
        'name' => 'FieldAggregateType',
        'description' => 'A type'
    ];

    public function fields(): array
    {
        return [
            'aggregate' => [
                'selectable' => false,
                'type' => GraphQL::type('AggregateType'),
            ],
            // 'nodes' => [
            //     'type' => GraphQL::type('UserType'),
            //     'selectable' => false,
            // ]
        ];
    }
}
