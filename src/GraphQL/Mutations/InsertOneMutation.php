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
        $this->attributes['name'] = $this->instance->getInsertOneMutationName();
        $this->attributes['description'] = $this->instance->getInsertOneMutationDescription();
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
        return $this->instance->overrideMethod(
            'resolveMutationInsertOne',
            [$this, 'resolveMutationInsertOne'],
            $root,
            $args,
            $context,
            $resolveInfo,
            $getSelectFields
        );
    }

    public function authorize($root, array $args, $ctx, ResolveInfo $resolveInfo = null, Closure $getSelectFields = null): bool
    {
        return $this->instance->getInsertOneMutationAuthorize($root, $args, $ctx, $resolveInfo, $getSelectFields);
    }

    public function getAuthorizationMessage(): string
    {
        return $this->instance->getInsertOneMutationAuthorizationMessage();
    }

    protected function rules(array $args = []): array
    {
        return $this->instance->getInsertOneMutationRules();
    }
}
