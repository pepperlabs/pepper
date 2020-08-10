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

    public function getMutationInsertFields()
    {
        return [
            'objects' => [
                'name' => 'objects',
                'type' => Type::listOf(GraphQL::type('UserMutationInput'))
            ]
        ];
    }

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
}
