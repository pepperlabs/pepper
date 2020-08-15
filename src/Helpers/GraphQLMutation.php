<?php

namespace Pepper\Helpers;

use Rebing\GraphQL\Support\Facades\GraphQL;
use GraphQL\Type\Definition\Type;

trait GraphQLMutation
{
    /**
     * Get GraphQL Mutation name.
     *
     * @return string
     */
    public function getMutationName(): string
    {
        $method = 'setMutationName';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName() . 'Mutation';
        }
    }

    public function getMutationDescription(): string
    {
        $method = 'setMutationDescription';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName() . ' mutation description.';
        }
    }

    public function getMutationType(): Type
    {
        return Type::listOf(GraphQL::type($this->getTypeName()));
    }

    public function getMutationFields(): array
    {
        $fields = [];

        // Get fields excluded relations
        foreach ($this->getFields(false) as $attribute) {
            $fields[$attribute] = [
                'name' => $attribute,
                'type' => $this->call_field_type($attribute)
            ];
        }

        return $fields;
    }

    // public function getMutationInsertFields()
    // {
    //     return [
    //         'objects' => [
    //             'name' => 'objects',
    //             'type' => Type::listOf(GraphQL::type('UserMutationInput'))
    //         ]
    //     ];
    // }

    public function getInputMutationName()
    {
        $method = 'setInputMutationName';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName() . 'InputMutation';
        }
    }

    public function getInputMutationDescription(): string
    {
        $method = 'setInputMutationDescription';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName() . ' input mutation description.';
        }
    }

    public function getInsertMutationName()
    {
        $method = 'setInsertMutationName';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName() . 'InsertMutation';
        }
    }

    public function getInsertMutationDescription(): string
    {
        $method = 'setInsertMutationDescription';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName() . ' insert mutation description.';
        }
    }

    public function getInsertOneMutationName()
    {
        $method = 'setInsertOneMutationName';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName() . 'InsertOneMutation';
        }
    }

    public function getInsertOneMutationDescription(): string
    {
        $method = 'setInsertOneMutationDescription';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName() . ' insert mutation description.';
        }
    }

    public function getUpdateByPkMutationName()
    {
        $method = 'setUpdateByPkMutationName';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName() . 'UpdateByPkMutation';
        }
    }

    public function getUpdateByPkMutationDescription(): string
    {
        $method = 'setUpdateByPkMutationDescription';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName() . ' insert mutation description.';
        }
    }

    public function updateByPkMutation($root, $args, $context, $resolveInfo, $getSelectFields)
    {
        // @todo: Not everyone are lucky enough to have a shiny id column.
        $models = $this->getModel()::where($args['pk_columns']);
        foreach ($models->get() as $model) {
            $model->update($args['_set']);
        }

        // Let the new born out in the wild.
        $root = $models;

        // return types are satisfied when they are iterable enough.
        return $this->getQueryResolve($root, $args, $context, $resolveInfo, $getSelectFields)->get();
    }

    public function getUpdateMutationName()
    {
        $method = 'setUpdateMutationName';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName() . 'UpdateMutation';
        }
    }

    public function getUpdateMutationDescription(): string
    {
        $method = 'setUpdateMutationDescription';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName() . ' update mutation description.';
        }
    }

    public function updateMutation($root, $args, $context, $resolveInfo, $getSelectFields)
    {
        // @todo: Not everyone are lucky enough to have a shiny id column.
        $models = $this->getQueryResolve($this->newModel(), $args, $context, $resolveInfo, $getSelectFields);
        foreach ($models->get() as $model) {
            $model->update($args['_set']);
        }

        // Let the new born out in the wild.
        $root = $models;

        // return types are satisfied when they are iterable enough.
        return $this->getQueryResolve($root, $args, $context, $resolveInfo, $getSelectFields)->get();
    }

    public function getDeleteByPkMutationName()
    {
        $method = 'setDeleteByPkMutationName';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName() . 'DeleteByPkMutation';
        }
    }

    public function getDeleteByPkMutationDescription(): string
    {
        $method = 'setDeleteByPkMutationDescription';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName() . ' DeleteByPk mutation description.';
        }
    }

    public function deleteByPkMutation($root, $args, $context, $resolveInfo, $getSelectFields)
    {
        $models = $this->newModel()::where($args);

        $models->delete();

        $root = $models;

        return $this->getQueryResolve($root, $args, $context, $resolveInfo, $getSelectFields)->get();
    }

    public function getDeleteMutationName()
    {
        $method = 'setDeleteMutationName';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName() . 'DeleteMutation';
        }
    }

    public function getDeleteMutationDescription(): string
    {
        $method = 'setDeleteMutationDescription';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName() . ' Delete mutation description.';
        }
    }

    public function deleteMutation($root, $args, $context, $resolveInfo, $getSelectFields)
    {
        // @todo: Not everyone are lucky enough to have a shiny id column.
        $models = $this->getQueryResolve($this->newModel(), $args, $context, $resolveInfo, $getSelectFields);

        $models->delete();

        // Let the new born out in the wild.
        $root = $models;

        // return types are satisfied when they are iterable enough.
        return $this->getQueryResolve($root, $args, $context, $resolveInfo, $getSelectFields)->get();
    }

    public function getMutationUpdateByPkFields(): array
    {
        return [
            'pk_columns' => [
                'name' => 'pk_columns',
                'type' => GraphQL::type('{{ name }}InputMutation')
            ],
            '_set' => [
                'name' => '_set',
                'type' => GraphQL::type('{{ name }}InputMutation')
            ]
        ];
    }


    public function getMutationUpdateFields(): array
    {
        return [
            'where' => ['type' => GraphQL::type($this->instance->getInputName())],
            '_set' => ['type' => GraphQL::type('{{ name }}InputMutation')]
        ];
    }

    public function getMutationInsertOneFields(): array
    {
        return [
            'object' => [
                'name' => 'object',
                'type' => GraphQL::type('{{ name }}InputMutation')
            ]
        ];
    }

    public function resolveMutationInsertOne($root, $args, $context, $resolveInfo, $getSelectFields)
    {
        // @todo: Not everyone are lucky enough to have a shiny id column.
        $id = $this->instance->getModel()::create($args['object'])->id;

        // Let the new born out in the wild.
        $root = $this->instance->newModel()->whereIn('id', [$id]);

        // return types are satisfied when they are iterable enough.
        return $this->instance->getQueryResolve($root, $args, $context, $resolveInfo, $getSelectFields)->get();
    }

    public function resolveMutationInsert($root, $args, $context, $resolveInfo, $getSelectFields)
    {
        // @todo: Not everyone are lucky enough to have a shiny id column.
        $ids = [];
        foreach ($args['objects'] as $obj) {
            $ids[] = $this->instance->getModel()::create($obj)->id;
        }

        // Let the new born out in the wild.
        $root = $this->instance->newModel()->whereIn('id', $ids);

        // return types are satisfied when they are iterable enough.
        return $this->instance->getQueryResolve($root, $args, $context, $resolveInfo, $getSelectFields)->get();
    }

    public function getMutationInsertFields(): array
    {
        return [
            'objects' => [
                'name' => 'objects',
                'type' => Type::listOf(GraphQL::type('{{ name }}InputMutation'))
            ]
        ];
    }

    public function resolveMutation($root, $args, $context, $resolveInfo, $getSelectFields)
    {
        // return type need to be iterable.
        return [$root->updateOrCreate(['id' => $args['id'] ?? -1], $args)];
    }
}
