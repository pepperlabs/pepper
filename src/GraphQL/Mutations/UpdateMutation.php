<?php

declare(strict_types=1);

namespace Pepper\GraphQL\Mutations;

use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;

class UpdateMutation extends Mutation
{
    protected $attributes = [];

    protected $instance;

    public function __construct($pepper)
    {
        $this->instance = new $pepper;
        $this->attributes['name'] = $this->instance->getUpdateMutationName();
        $this->attributes['description'] = $this->instance->getUpdateMutationDescription();
    }

    public function type(): Type
    {
        return $this->instance->getMutationType();
    }

    public function args(): array
    {
        return $this->instance->getMutationUpdateFields();
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return $this->instance->updateMutation($root, $args, $context, $resolveInfo, $getSelectFields);
    }
}
