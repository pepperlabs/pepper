<?php

declare(strict_types=1);

namespace Pepper\GraphQL\Mutations;

use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;

class UpdateByPkMutation extends Mutation
{
    protected $attributes = [];

    protected $instance;

    public function __construct($pepper)
    {
        $this->instance = new $pepper;
        $this->attributes['name'] = $this->instance->getUpdateByPkMutationName();
        $this->attributes['description'] = $this->instance->getUpdateByPkMutationDescription();
    }

    public function type(): Type
    {
        return $this->instance->getMutationUpdateByPkType();
    }

    public function args(): array
    {
        return $this->instance->getMutationUpdateByPkFields();
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return $this->instance->updateByPkMutation($root, $args, $context, $resolveInfo, $getSelectFields);
    }
}
