<?php

namespace Pepper\Supports;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

trait MutationSupport
{
    /**
     * Get GraphQL Mutation name.
     *
     * @return string
     */
    public function getMutationName() : string
    {
        $method = 'setMutationName';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName().'Mutation';
        }
    }

    /**
     * Get mutation description.
     *
     * @return string
     */
    public function getMutationDescription() : string
    {
        $method = 'setMutationDescription';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName().' mutation description.';
        }
    }

    /**
     * Get mutation type.
     *
     * @return Type
     */
    public function getMutationType() : Type
    {
        return Type::listOf(GraphQL::type($this->getTypeName()));
    }

    /**
     * Get mutation update by PK type.
     *
     * @return Type
     */
    public function getMutationUpdateByPkType() : Type
    {
        return GraphQL::type($this->getTypeName());
    }

    /**
     * Get mutation fields.
     *
     * @return array
     */
    public function getMutationFields() : array
    {
        $fields = [];

        // Get fields excluded relations
        foreach ($this->getFields(false) as $attribute) {
            $fields[$attribute] = [
                'name' => $attribute,
                'type' => $this->call_field_type($attribute),
            ];
        }

        return $fields;
    }

    /**
     * Get input mutation name.
     *
     * @return string
     */
    public function getInputMutationName()
    {
        $method = 'setInputMutationName';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName().'MutationInput';
        }
    }

    /**
     * Get input mutation description.
     *
     * @return string
     */
    public function getInputMutationDescription() : string
    {
        $method = 'setInputMutationDescription';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName().' input mutation description.';
        }
    }

    /**
     * Get insert mutation name.
     *
     * @return string
     */
    public function getInsertMutationName() : string
    {
        $method = 'setInsertMutationName';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName().'InsertMutation';
        }
    }

    /**
     * Get insert mutation description.
     *
     * @return string
     */
    public function getInsertMutationDescription() : string
    {
        $method = 'setInsertMutationDescription';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName().' insert mutation description.';
        }
    }

    /**
     * Get insert one mutation name.
     *
     * @return string
     */
    public function getInsertOneMutationName() : string
    {
        $method = 'setInsertOneMutationName';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName().'InsertOneMutation';
        }
    }

    /**
     * Get insert one mutation description.
     *
     * @return string
     */
    public function getInsertOneMutationDescription() : string
    {
        $method = 'setInsertOneMutationDescription';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName().' insert mutation description.';
        }
    }

    /**
     * Get update by PK mutation name.
     *
     * @return string
     */
    public function getUpdateByPkMutationName() : string
    {
        $method = 'setUpdateByPkMutationName';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName().'UpdateByPkMutation';
        }
    }

    /**
     * Get update by PK mutation description.
     *
     * @return string
     */
    public function getUpdateByPkMutationDescription() : string
    {
        $method = 'setUpdateByPkMutationDescription';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName().' insert mutation description.';
        }
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
        $pk = $this->newModel()->getKeyName();

        $builder = $this->getModel()::where($pk, $args['pk_columns'][$pk]);

        $builder->update($args['_set']);

        return $this->getQueryResolve($builder, $args, $context, $resolveInfo, $getSelectFields)
                    ->first();
    }

    /**
     * Get update mutation name.
     *
     * @return string
     */
    public function getUpdateMutationName() : string
    {
        $method = 'setUpdateMutationName';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName().'UpdateMutation';
        }
    }

    /**
     * Get update mutation description.
     *
     * @return string
     */
    public function getUpdateMutationDescription() : string
    {
        $method = 'setUpdateMutationDescription';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName().' update mutation description.';
        }
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
        $models = $this->getQueryResolve($this->newModel(), $args, $context, $resolveInfo, $getSelectFields);
        foreach ($models->get() as $model) {
            $model->update($args['_set']);
        }

        $root = $models;

        return $this->getQueryResolve($root, $args, $context, $resolveInfo, $getSelectFields)->get();
    }

    /**
     * Get delete by PK mutation name.
     *
     * @return string
     */
    public function getDeleteByPkMutationName() : string
    {
        $method = 'setDeleteByPkMutationName';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName().'DeleteByPkMutation';
        }
    }

    /**
     * Get delete by PK mutation description.
     *
     * @return string
     */
    public function getDeleteByPkMutationDescription() : string
    {
        $method = 'setDeleteByPkMutationDescription';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName().' DeleteByPk mutation description.';
        }
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
        $models = $this->newModel()::where($args);

        $models->delete();

        $root = $models;

        return $this->getQueryResolve($root, $args, $context, $resolveInfo, $getSelectFields)->get();
    }

    /**
     * Get delete mutation name.
     *
     * @return string
     */
    public function getDeleteMutationName() : string
    {
        $method = 'setDeleteMutationName';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName().'DeleteMutation';
        }
    }

    /**
     * Get delete mutation description.
     *
     * @return string
     */
    public function getDeleteMutationDescription() : string
    {
        $method = 'setDeleteMutationDescription';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName().' Delete mutation description.';
        }
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
        $models = $this->getQueryResolve($this->newModel(), $args, $context, $resolveInfo, $getSelectFields);

        $models->delete();

        $root = $models;

        return $this->getQueryResolve($root, $args, $context, $resolveInfo, $getSelectFields)->get();
    }

    /**
     * Get mutation update by PK fields.
     *
     * @return array
     */
    public function getMutationUpdateByPkFields() : array
    {
        return [
            'pk_columns' => [
                'name' => 'pk_columns',
                'type' => GraphQL::type($this->getInputMutationName()),
            ],
            '_set' => [
                'name' => '_set',
                'type' => GraphQL::type($this->getInputMutationName()),
            ],
        ];
    }

    /**
     * Get mutation update fields.
     *
     * @return array
     */
    public function getMutationUpdateFields() : array
    {
        return [
            'where' => ['type' => GraphQL::type($this->getInputName())],
            '_set' => ['type' => GraphQL::type($this->getInputMutationName())],
        ];
    }

    /**
     * Get mutation insert one fields.
     *
     * @return array
     */
    public function getMutationInsertOneFields() : array
    {
        return [
            'object' => [
                'name' => 'object',
                'type' => GraphQL::type($this->getInputMutationName()),
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
        $id = $this->getModel()::create($args['object'])->id;

        $root = $this->newModel()->whereIn('id', [$id]);

        return $this->getQueryResolve($root, $args, $context, $resolveInfo, $getSelectFields)->get();
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
            $ids[] = $this->getModel()::create($obj)->id;
        }

        $root = $this->newModel()->whereIn('id', $ids);

        return $this->getQueryResolve($root, $args, $context, $resolveInfo, $getSelectFields)->get();
    }

    /**
     * Get mutation insert fields.
     *
     * @return array
     */
    public function getMutationInsertFields() : array
    {
        return [
            'objects' => [
                'name' => 'objects',
                'type' => Type::listOf(GraphQL::type($this->getInputMutationName())),
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
