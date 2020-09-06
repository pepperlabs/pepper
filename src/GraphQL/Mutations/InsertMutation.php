<?php

namespace Pepper\Mutations;

use GraphQL\Type\Definition\Type;
use Pepper\Concerns\Resolve;
use Pepper\GraphQL as PepperGraphQL;
use Rebing\GraphQL\Support\Facades\GraphQL;

class InsertMutation extends PepperGraphQL
{
    use Resolve;

    /**
     * Get insert mutation name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name().'InsertMutation';
    }

    /**
     * Get insert mutation description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->name().' insert mutation description.';
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
     * Get mutation insert fields.
     *
     * @return array
     */
    public function getArgs(): array
    {
        return [
            'objects' => [
                'name' => 'objects',
                'type' => Type::listOf(GraphQL::type($this->getInputMutationName())),
            ],
        ];
    }

    /**
     * Resolve mutation insert.
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
        $ids = [];
        foreach ($args['objects'] as $obj) {
            $ids[] = $this->model()::create($obj)->id;
        }

        $root = $this->model()->whereIn('id', $ids);

        return $this->getQueryResolve($root, $args, $context, $resolveInfo, $getSelectFields)
                    ->get();
    }
}
