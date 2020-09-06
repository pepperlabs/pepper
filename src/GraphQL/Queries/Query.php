<?php

namespace Pepper\Query;

use GraphQL\Type\Definition\Type;
use Pepper\Concerns\Resolve;
use Pepper\GraphQL as PepperGraphQL;
use Rebing\GraphQL\Support\Facades\GraphQL;

class Query extends PepperGraphQL
{
    use Resolve;

    /**
     * Get GraphQL Query name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name().'Query';
    }

    /**
     * Get GraphQL Query description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->name().' query description.';
    }

    /**
     * Get GraphQL query type.
     *
     * @return void
     */
    public function getQueryType(): Type
    {
        return Type::listOf(GraphQL::type($this->getName()));
    }

    public function getQueryArgs()
    {
        return [
            // Condition
            'where' => ['type' => GraphQL::type($this->getInputName())],

            'distinct' => ['name' => 'distinct', 'type' => Type::boolean()],

            // Order
            'order_by' => ['type' => GraphQL::type($this->getOrderName())],

            // Paginate
            'limit' => ['name' => 'limit', 'type' => Type::int()],
            'offset' => ['name' => 'offset', 'type' => Type::int()],
            'skip' => ['name' => 'skip', 'type' => Type::int()],
            'take' => ['name' => 'take', 'type' => Type::int()],
        ];
    }
}
