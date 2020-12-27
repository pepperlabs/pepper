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
     * Find a model by Primary Key delete that single model instance if it has
     * been found. Before deleteing the model, it will get the model query
     * builder on expected to be deleted model and return it for later.
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
     * Delete given model(s) after running through query resolver. It will run
     * `delete` method on the model instance(s) and soft delete can be in
     * effect and models are not hard deleted in this method.
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
     * Get the `pk_columns` and `_set` fields for the update by PK mutation.
     * pk_columns is the where condition on the Primary Key and _set is
     * the new values that the model instance should be updated to.
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
     * Get the `where` and `_set` fields for the update mutation. where is the
     * conditions for the models to be updated and _set is the new values for
     * the model to be updated.
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
     * Get the `object` fields for adding fields of the model to be inserted
     * into the dataset under the defined model.
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
     * Resolve inserting a object of model into the database. Here the assumtion
     * is that, there is an ID column which is the primary key and can be the
     * returned after a object of the model has been successfully insetered.
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
     * Resolve inserting objects of model into the database. Here the assumtion
     * is that, there is an ID column which is the primary key and can be the
     * returned after objects of the model has been successfully insetered.
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

        return $this->getQueryResolve($root, $args, $context, $resolveInfo, $getSelectFields)
            ->get();
    }

    /**
     * Get the `objects` fields for adding a list of fields to be inserted into
     * the dataset under the defined model.
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
}
