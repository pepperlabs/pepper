<?php

declare(strict_types=1);

namespace Pepper\GraphQL\Unions;

use HaydenPierce\ClassFinder\ClassFinder;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\UnionType;

class AllUnion extends UnionType
{
    protected $attributes = [
        'name' => 'AllUnion',
        'description' => 'A Union of all types',
    ];

    public function types(): array
    {
        $classes = ClassFinder::getClassesInNamespace('App\Http\Pepper');
        $types = [];
        foreach ($classes as $pepper) {
            $instance = new $pepper;
            $types[] = GraphQL::type($instance->getResultAggregateTypeName());
        }

        return $types;
    }

    public function resolveType($root)
    {
        return GraphQL::type($root['__Union_Type']);
    }
}
