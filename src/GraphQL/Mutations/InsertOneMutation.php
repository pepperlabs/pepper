<?php

declare(strict_types=1);

namespace Pepper\GraphQL\Mutations;

use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;

class InsertOneMutation extends Mutation
{
    protected $attributes = [];

    protected $instance;

    public function __construct($pepper)
    {
        $this->instance = new $pepper;
        $this->attributes['name'] = 'insert_'.$this->instance->getQueryName().'_one';
        $this->attributes['description'] = $this->instance->getQueryDescription();
    }

    public function type(): Type
    {
        return $this->instance->getMutationInsertOneType();
    }

    public function args(): array
    {
        return $this->instance->getMutationInsertOneFields();
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return $this->instance->resolveMutationInsertOne($root, $args, $context, $resolveInfo, $getSelectFields);
    }
}
