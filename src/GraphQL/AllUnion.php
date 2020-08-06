<?php

declare(strict_types=1);

namespace Pepper\GraphQL;

use Rebing\GraphQL\Support\UnionType;
use HaydenPierce\ClassFinder\ClassFinder;
use Rebing\GraphQL\Support\Facades\GraphQL;

class AllUnion extends UnionType
{
    protected $attributes = [
        'name' => 'AllUnion',
        'description' => 'An example union',
    ];

    public function types(): array
    {
        /**
         * @todo I shall give you your own config play environement.
         */
        $classes = ClassFinder::getClassesInNamespace('App\Http\Pepper');
        $types = [];
        foreach ($classes as $pepper) {
            $instance = new $pepper;
            $types[] = GraphQL::type($instance->getResultAggregateName());
        }
        return $types;
    }

    public function resolveType($root)
    {
        return GraphQL::type($root['__type']);
    }
}
