<?php

declare(strict_types=1);

namespace Pepper\GraphQL;

use App\GraphQL\Unions\AnyUnion;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;
use Rebing\GraphQL\Support\Facades\GraphQL;

/**
 * @todo
 */
class ConflictInput extends InputType
{
    protected $attributes = [
        'name' => 'ConflictInput',
        'description' => 'Available conditions',
    ];

    public function fields(): array
    {
        return [
            'constraint' => [
                'name' => 'constraint',
                'type' => GraphQL::type('AnyScalar'),
            ],
            'update_columns' => [
                'type' => Type::listOf(GraphQL::type('AnyScalar')),
            ],
            'where' => [
                'name' => 'where',
                'type' => GraphQL::type('AnyScalar'),
            ]
        ];
    }
}
