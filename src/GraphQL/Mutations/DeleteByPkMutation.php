<?php

declare(strict_types=1);

namespace Pepper\GraphQL\Mutations;

use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;

class DeleteByPkMutation extends Mutation
{
    protected $attributes = [];

    protected $instance;

    public function __construct($pepper)
    {
        $this->instance = new $pepper;
        $this->attributes['name'] = $this->instance->getDeleteByPkMutationName();
        $this->attributes['description'] = $this->instance->getDeleteByPkMutationDescription();
    }

    public function type(): Type
    {
        return $this->instance->getDeleteByPKMutationType();
    }

    public function args(): array
    {
        return $this->instance->getDeleteByPkMutationFields();
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return $this->instance->deleteByPkMutation($root, $args, $context, $resolveInfo, $getSelectFields);
    }

    public function authorize($root, array $args, $ctx, ResolveInfo $resolveInfo = null, Closure $getSelectFields = null): bool
    {
        return $this->instance->getDeleteByPkMutationAuthorize($root, $args, $ctx, $resolveInfo, $getSelectFields);
    }

    public function getAuthorizationMessage(): string
    {
        return $this->instance->getDeleteByPkMutationAuthorizationMessage();
    }
}
