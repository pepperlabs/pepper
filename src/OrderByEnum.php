<?php

declare(strict_types=1);

namespace Pepper;

use Rebing\GraphQL\Support\EnumType;

class OrderByEnum extends EnumType
{
    protected $attributes = [
        'name' => 'OrderByEnum',
        'description' => 'Order by enum.',
        'values' => [
            'asc' => [
                'value' => 'asc',
                'description' => 'Sort results in ascending (ASC) order',
            ],
            'desc' => [
                'value' => 'desc',
                'description' => 'Sort results in descending (DESC) order'
            ]
        ],
    ];
}
