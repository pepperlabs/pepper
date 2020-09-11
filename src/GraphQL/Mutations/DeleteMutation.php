<?php

declare(strict_types=1);

namespace Pepper\GraphQL\Mutations;

use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;

class DeleteMutation extends Mutation
{
    protected $attributes = [];

    protected $instance;

    public function __construct($pepper)
    {
        $this->instance = new $pepper;
        $this->attributes['name'] = $this->instance->getDeleteMutationName();
        $this->attributes['description'] = $this->instance->getDeleteMutationDescription();
    }

    public function type(): Type
    {
        return $this->instance->getDeleteMutationType();
    }

    public function args(): array
    {
        return $this->instance->getDeleteMutationFields();
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return $this->instance->deleteMutation($root, $args, $context, $resolveInfo, $getSelectFields);
    }
}
