<?php

namespace Pepper\Mutations;

use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Pepper\Contracts\MutationContract;
use Pepper\GraphQL as PepperGraphQL;
use Rebing\GraphQL\Support\Facades\GraphQL;

class Delete extends PepperGraphQL implements MutationContract
{
    /**
     * Get delete mutation name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->getName().'DeleteMutation';
    }

    /**
     * Get delete mutation description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->getName().' delete mutation description.';
    }

    /**
    * Get delete mutation type.
    *
    * @return Type
    */
    public function getType(): Type
    {
        return Type::listOf(GraphQL::type($this->getTypeName()));
    }

    /**
     * Get delete mutation fields.
     *
     * @return array
     */
    public function getArgs(): array
    {
        return [
            'where' => ['type' => GraphQL::type($this->getInputName())],
        ];
    }

    /**
     * Delete mutation.
     *
     * @param  object  $root
     * @param  array  $args
     * @param  object  $context
     * @param  ResolveInfo  $resolveInfo
     * @param  Closure  $getSelectFields
     * @return object
     */
    public function getResolve($root, $args, $context, $resolveInfo, $getSelectFields)
    {
        $models = $this->getQueryResolve($this->model(), $args, $context, $resolveInfo, $getSelectFields);

        $results = $models->get();

        $models->delete();

        return $results;
    }
}
