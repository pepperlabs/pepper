<?php

namespace Pepper\Query;

use GraphQL\Type\Definition\Type;
use Pepper\Concerns\Resolve;
use Pepper\GraphQL as PepperGraphQL;
use Rebing\GraphQL\Support\Facades\GraphQL;

class ByPkQuery extends PepperGraphQL
{
    use Resolve;

    public function getName(): string
    {
        return $this->name().'ByPkQuery';
    }

    public function getDescription(): string
    {
        return $this->name().' query by PK description.';
    }

    public function getType(): Type
    {
        return GraphQL::type($this->getName());
    }

    public function getArgs(): array
    {
        $model = $this->model();
        $pk = $this->pk();

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
