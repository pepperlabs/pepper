<?php

namespace Pepper\Mutations;

use GraphQL\Type\Definition\Type;
use Pepper\Contracts\MutationContract;
use Pepper\GraphQL as PepperGraphQL;
use Rebing\GraphQL\Support\Facades\GraphQL;

class InsertOneMutation extends PepperGraphQL implements MutationContract
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->getName().'InsertOneMutation';
    }

    /**
     * Get insert one mutation description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->getName().' insert mutation description.';
    }

    /**
     * Get mutation insert one type.
     *
     * @return Type
     */
    public function getType(): Type
    {
        return GraphQL::type($this->getTypeName());
    }

    /**
     * Get mutation insert one fields.
     *
     * @return array
     */
    public function getArgs(): array
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
     * @param  object  $root
     * @param  array  $args
     * @param  object  $context
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @param  \Closure  $getSelectFields
     * @return object
     */
    public function getResolve($root, $args, $context, $resolveInfo, $getSelectFields)
    {
        $id = $this->model()::create($args['object'])->id;

        $root = $this->model()->whereIn('id', [$id]);

        return $this->getQueryResolve($root, $args, $context, $resolveInfo, $getSelectFields)
                    ->first();
    }
}
