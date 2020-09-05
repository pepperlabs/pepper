<?php

namespace Pepper\Query;

use GraphQL\Type\Definition\Type;
use Pepper\GraphQL as PepperGraphQL;
use Rebing\GraphQL\Support\Facades\GraphQL;

class ByPkQuery extends PepperGraphQL
{
    public function getQueryByPkName(): string
    {
        $method = 'setQueryByPkName';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName().'ByPkQuery';
        }
    }

    public function getQueryByPkDescription(): string
    {
        $method = 'setQueryByPkDescription';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName().' query by PK description.';
        }
    }

    public function getQueryByPkType(): Type
    {
        return GraphQL::type($this->getTypeName());
    }

    public function getQueryByPkFields(): array
    {
        $model = $this->model();
        $pk = $model->getKeyName();

        return [
            $pk => [
                'name' => $pk,
                'type' => $this->callGraphQLType($pk),
            ],
        ];
    }

    /**
     * Resolve query by PK.
     */
    public function queryByPk($root, $args, $context, $resolveInfo, $getSelectFields)
    {
        $model = $this->model();
        $pk = $this->pk();

        $root = $this->modelClass()::where($pk, $args[$pk]);

        return $this->getQueryResolve($root, $args, $context, $resolveInfo, $getSelectFields)->first();
    }
}
