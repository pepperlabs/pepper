<?php

namespace Pepper\GraphQL\Enums;

use Rebing\GraphQL\Support\EnumType;

class OrderByEnum extends EnumType
{
    protected $attributes = [
        'name' => 'OrderByEnum',
        'description' => 'Sort results in ascending or descending order.',
        'values' => [
            'asc' => [
                'value' => 'asc',
                'description' => 'Sort results in ascending (ASC) order',
            ],
            'desc' => [
                'value' => 'desc',
                'description' => 'Sort results in descending (DESC) order',
            ],
        ],
    ];
}
