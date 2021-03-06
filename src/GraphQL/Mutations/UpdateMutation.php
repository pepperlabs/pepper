<?php

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

    public function authorize($root, array $args, $ctx, ResolveInfo $resolveInfo = null, Closure $getSelectFields = null): bool
    {
        return $this->instance->getUpdateMutationAuthorize($root, $args, $ctx, $resolveInfo, $getSelectFields);
    }

    public function getAuthorizationMessage(): string
    {
        return $this->instance->getUpdateMutationAuthorizationMessage();
    }

    protected function rules(array $args = []): array
    {
        return $this->instance->getUpdateMutationRules();
    }
}
