<?php

namespace Pepper\Supports;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

trait MutationSupport
{
    /**
     * Get mutation type.
     *
     * @return Type
     */
    public function getMutationType(): Type
    {
        return Type::listOf(GraphQL::type($this->getTypeName()));
    }

    /**
     * Get delete mutation type.
     *
     * @return Type
     */
    public function getDeleteMutationType(): Type
    {
        return Type::listOf(GraphQL::type($this->getTypeName()));
    }

    /**
     * Get delete by PK mutation type.
     *
     * @return Type
     */
    public function getDeleteByPKMutationType(): Type
    {
        return GraphQL::type($this->getTypeName());
    }

    /**
     * Get mutation update by PK type.
     *
     * @return Type
     */
    public function getMutationUpdateByPkType(): Type
    {
        return GraphQL::type($this->getTypeName());
    }

    /**
     * Get mutation insert one type.
     *
     * @return Type
     */
    public function getMutationInsertOneType(): Type
    {
        return GraphQL::type($this->getTypeName());
    }

    /**
     * Get mutation fields.
     *
     * @return array
     */
    public function getMutationFields(): array
    {
        $fields = [];

        // Get fields excluded relations
        foreach ($this->fieldsArray(false) as $attribute) {
            $fields[$attribute] = [
                'name' => $attribute,
                'type' => $this->callGraphQLType($attribute),
            ];
        }

        return $fields;
    }

    /**
     * Get delete by PK mutation fields.
     *
     * @return array
     */
    public function getDeleteByPkMutationFields(): array
    {
        // @todo replace ID
        return [
            'id' => [
                'name' => 'id',
                'type' => $this->callGraphQLType('id'),
            ],
        ];
    }

    /**
     * Get delete mutation fields.
     *
     * @return array
     */
    public function getDeleteMutationFields(): array
    {
        return [
            'where' => ['type' => GraphQL::type($this->getInputName())],
        ];
    }

    /**
     * Update by PK mutation.
     *
     * @param  object $root
     * @param  array $args
     * @param  object $context
     * @param  ResolveInfo $resolveInfo
     * @param  Closure $getSelectFields
     * @return object
     */
    public function updateByPkMutation($root, $args, $context, $resolveInfo, $getSelectFields)
    {
        $pk = $this->model()->getKeyName();

        $builder = $this->modelClass()::where($pk, $args['pk_columns'][$pk]);

        $builder->update($args['_set']);

        return $this->getQueryResolve($builder, $args, $context, $resolveInfo, $getSelectFields)
                    ->first();
    }

    /**
     * Update mutation.
     *
     * @param  object $root
     * @param  array $args
     * @param  object $context
     * @param  ResolveInfo $resolveInfo
     * @param  Closure $getSelectFields
     * @return object
     */
    public function updateMutation($root, $args, $context, $resolveInfo, $getSelectFields)
    {
        $models = $this->getQueryResolve($this->model(), $args, $context, $resolveInfo, $getSelectFields);
        foreach ($models->get() as $model) {
            $model->update($args['_set']);
        }

        $root = $models;

        return $this->getQueryResolve($root, $args, $context, $resolveInfo, $getSelectFields)->get();
    }

    /**
     * Delete by PK mutation.
     *
     * @param  object $root
     * @param  array $args
     * @param  object $context
     * @param  ResolveInfo $resolveInfo
     * @param  Closure $getSelectFields
     * @return object
     */
    public function deleteByPkMutation($root, $args, $context, $resolveInfo, $getSelectFields)
    {
        $pk = $this->model()->getKeyName();

        $builder = $this->modelClass()::where($pk, $args[$pk]);

        $resolve = $this->getQueryResolve($builder, $args, $context, $resolveInfo, $getSelectFields)
                    ->first();

        $builder->delete();

        return $resolve;
    }

    /**
     * Delete mutation.
     *
     * @param  object $root
     * @param  array $args
     * @param  object $context
     * @param  ResolveInfo $resolveInfo
     * @param  Closure $getSelectFields
     * @return object
     */
    public function deleteMutation($root, $args, $context, $resolveInfo, $getSelectFields)
    {
        $models = $this->getQueryResolve($this->model(), $args, $context, $resolveInfo, $getSelectFields);

        $results = $models->get();

        $models->delete();

        return $results;
    }

    /**
     * Get mutation update by PK fields.
     *
     * @return array
     */
    public function getMutationUpdateByPkFields(): array
    {
        return [
            'pk_columns' => [
                'name' => 'pk_columns',
                'type' => GraphQL::type($this->getMutationInputName()),
            ],
            '_set' => [
                'name' => '_set',
                'type' => GraphQL::type($this->getMutationInputName()),
            ],
        ];
    }

    /**
     * Get mutation update fields.
     *
     * @return array
     */
    public function getMutationUpdateFields(): array
    {
        return [
            'where' => ['type' => GraphQL::type($this->getInputName())],
            '_set' => ['type' => GraphQL::type($this->getMutationInputName())],
        ];
    }

    /**
     * Get mutation insert one fields.
     *
     * @return array
     */
    public function getMutationInsertOneFields(): array
    {
        return [
            'object' => [
                'name' => 'object',
                'type' => GraphQL::type($this->getMutationInputName()),
            ],
        ];
    }

    /**
     * Resolve mutation insert one.
     *
     * @param  object $root
     * @param  array $args
     * @param  object $context
     * @param  ResolveInfo $resolveInfo
     * @param  Closure $getSelectFields
     * @return object
     */
    public function resolveMutationInsertOne($root, $args, $context, $resolveInfo, $getSelectFields)
    {
        $id = $this->modelClass()::create($args['object'])->id;

        $root = $this->model()->whereIn('id', [$id]);

        return $this->getQueryResolve($root, $args, $context, $resolveInfo, $getSelectFields)
                    ->first();
    }

    /**
     * Resolve mutation insert.
     *
     * @param  object $root
     * @param  array $args
     * @param  object $context
     * @param  ResolveInfo $resolveInfo
     * @param  Closure $getSelectFields
     * @return object
     */
    public function resolveMutationInsert($root, $args, $context, $resolveInfo, $getSelectFields)
    {
        $ids = [];
        foreach ($args['objects'] as $obj) {
            $ids[] = $this->modelClass()::create($obj)->id;
        }

        $root = $this->model()->whereIn('id', $ids);

        return $this->getQueryResolve($root, $args, $context, $resolveInfo, $getSelectFields)->get();
    }

    /**
     * Get mutation insert fields.
     *
     * @return array
     */
    public function getMutationInsertFields(): array
    {
        return [
            'objects' => [
                'name' => 'objects',
                'type' => Type::listOf(GraphQL::type($this->getMutationInputName())),
            ],
        ];
    }

    /**
     * Resolve mutation.
     *
     * @param  object $root
     * @param  array $args
     * @param  object $context
     * @param  ResolveInfo $resolveInfo
     * @param  Closure $getSelectFields
     * @return object
     */
    public function resolveMutation($root, $args, $context, $resolveInfo, $getSelectFields)
    {
        return [$root->updateOrCreate(['id' => $args['id'] ?? -1], $args)];
    }
}
