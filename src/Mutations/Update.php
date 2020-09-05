<?php

namespace Pepper\Mutations;

use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Pepper\GraphQL as PepperGraphQL;
use Rebing\GraphQL\Support\Facades\GraphQL;

class Update extends PepperGraphQL
{
    /**
     * Get update mutation name.
     *
     * @return string
     */
    public function getUpdateMutationName(): string
    {
        return $this->getName().'UpdateMutation';
    }

    /**
       * Get update mutation description.
       *
       * @return string
       */
    public function getUpdateMutationDescription(): string
    {
        return $this->getName().' update mutation description.';
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
     * Get mutation update fields.
     *
     * @return array
     */
    public function getMutationUpdateFields(): array
    {
        return [
            'where' => ['type' => GraphQL::type($this->getInputName())],
            '_set' => ['type' => GraphQL::type($this->getInputMutationName())],
        ];
    }

    /**
     * Get mutation type.
     *
     * @return Type
     */
    public function getUpdateMutationType(): Type
    {
        return Type::listOf(GraphQL::type($this->getTypeName()));
    }
}
