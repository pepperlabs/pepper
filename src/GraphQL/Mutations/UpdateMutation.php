<?php

namespace Pepper\Mutations;

use GraphQL\Type\Definition\Type;
use Pepper\Concerns\Resolve;
use Pepper\GraphQL as PepperGraphQL;
use Rebing\GraphQL\Support\Facades\GraphQL;

class UpdateMutation extends PepperGraphQL
{
    use Resolve;

    /**
     * Get update mutation name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name().'UpdateMutation';
    }

    /**
     * Get update mutation description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->name().' update mutation description.';
    }

    /**
     * Get mutation type.
     *
     * @return Type
     */
    public function getType(): Type
    {
        return Type::listOf(GraphQL::type($this->getName()));
    }

    /**
     * Get mutation update fields.
     *
     * @return array
     */
    public function getArgs(): array
    {
        return [
            'where' => ['type' => GraphQL::type($this->getInputName())],
            '_set' => ['type' => GraphQL::type($this->getInputMutationName())],
        ];
    }

    /**
     * update.
     *
     * @param  object  $root
     * @param  array  $args
     * @param  object  $context
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @param  \Closure  $getSelectFields
     * @return object
     */
    public function getResolve($root, $args, $context, $resolveInfo, $getSelectFields)
    {
        $models = $this->getQueryResolve($this->model(), $args, $context, $resolveInfo, $getSelectFields);
        foreach ($models->get() as $model) {
            $model->update($args['_set']);
        }

        $root = $models;

        return $this->getQueryResolve($root, $args, $context, $resolveInfo, $getSelectFields)->get();
    }
}
