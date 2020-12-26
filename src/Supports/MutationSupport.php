<?php

namespace Pepper\Supports;

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

trait MutationSupport
{
    /**
     * Type of basic mutation by model name. It's going to be a list of the
     * models.
     *
     * @return \GraphQL\Type\Definition\ListOfType
     */
    public function getMutationType(): ListOfType
    {
        return Type::listOf(GraphQL::type($this->getTypeName()));
    }

    /**
     * Type of delete mutation by model name. It's going to be a list of the
     * models.
     *
     * @return \GraphQL\Type\Definition\ListOfType
     */
    public function getDeleteMutationType(): ListOfType
    {
        return Type::listOf(GraphQL::type($this->getTypeName()));
    }

    /**
     * As deleted by PK is unique and would be one or none, the type of the
     * GraphQL mutation type is also single and is not a list of models.
     *
     * @return \GraphQL\Type\Definition\Type
     */
    public function getDeleteByPKMutationType(): Type
    {
        return GraphQL::type($this->getTypeName());
    }

    /**
     * As updated by PK is unique and would be one or none, the type of the
     * GraphQL mutation type is also single and is not a list of models.
     *
     * @return \GraphQL\Type\Definition\Type
     */
    public function getMutationUpdateByPkType(): Type
    {
        return GraphQL::type($this->getTypeName());
    }

    /**
     * As insert only one is unique and would be one or none, the type of the
     * GraphQL mutation type is also single and is not a list of models.
     *
     * @return \GraphQL\Type\Definition\Type
     */
    public function getMutationInsertOneType(): Type
    {
        return GraphQL::type($this->getTypeName());
    }

    /**
     * Get the fields can be used as input for mutation for conditioning on them
     * and query based on them. Currently it is not available to query on the
     * relations and only fields can be queried and reterived by Builder.
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
     * Get primary key for the delete by primary key GraphQL mutation with its
     * repective type. currently mixed primary keys are not supported and
     * only single column primary key is supported.
     *
     * @return array
     */
    public function getDeleteByPkMutationFields(): array
    {
        $pk = $this->model()->getKeyName();

        return [
            $pk => [
                'name' => $pk,
                'type' => $this->callGraphQLType($pk),
            ],
        ];
    }

    /**
     * Get delete GraphQL mutation fields for being quired in input level as
     * the condition(s) to delete multiple resources from the database(s).
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
     * Update a single resource identified by the primary key of the model and
     * and return the updated query as a Builder for handling any post update
     * query requirments filled by the GraphQL mutation request selections.
     *
     * @param  object  $root
     * @param  array  $args
     * @param  object  $context
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @param  Closure  $getSelectFields
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
     * Update multiple resources identified by the conditions of the model and
     * and return the updated query as a Builder for handling any post update
     * query requirments filled by the GraphQL mutation request selections.
     *
     * @param  object  $root
     * @param  array  $args
     * @param  object  $context
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @param  Closure  $getSelectFields
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
     * Delete a single resource identified by primary key.
     *
     * @param  object  $root
     * @param  array  $args
     * @param  object  $context
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @param  Closure  $getSelectFields
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
     * @param  object  $root
     * @param  array  $args
     * @param  object  $context
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @param  Closure  $getSelectFields
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
     * @param  object  $root
     * @param  array  $args
     * @param  object  $context
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @param  Closure  $getSelectFields
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
     * @param  object  $root
     * @param  array  $args
     * @param  object  $context
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @param  Closure  $getSelectFields
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
     * @param  object  $root
     * @param  array  $args
     * @param  object  $context
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @param  Closure  $getSelectFields
     * @return object
     */
    public function resolveMutation($root, $args, $context, $resolveInfo, $getSelectFields)
    {
        return [$root->updateOrCreate(['id' => $args['id'] ?? -1], $args)];
    }
}
