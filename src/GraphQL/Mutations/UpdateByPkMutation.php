<?php

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

    public function authorize($root, array $args, $ctx, ResolveInfo $resolveInfo = null, Closure $getSelectFields = null): bool
    {
        return $this->instance->getUpdateByPkMutationAuthorize($root, $args, $ctx, $resolveInfo, $getSelectFields);
    }

    public function getAuthorizationMessage(): string
    {
        return $this->instance->getUpdateByPkMutationAuthorizationMessage();
    }

    protected function rules(array $args = []): array
    {
        return $this->instance->getUpdateByPkMutationRules();
    }
}
