<?php

namespace Pepper\Query;

use GraphQL\Type\Definition\Type;
use Pepper\GraphQL as PepperGraphQL;
use Rebing\GraphQL\Support\Facades\GraphQL;

class Query extends PepperGraphQL
{
    /**
     * Get GraphQL Query name.
     *
     * @return string
     */
    public function getQueryName(): string
    {
        return $this->getName().'Query';
    }

    /**
     * Get GraphQL Query description.
     *
     * @return string
     */
    public function getQueryDescription(): string
    {
        return $this->getName().' query description.';
    }

    /**
     * Get GraphQL query type.
     *
     * @return void
     */
    public function getQueryType(): Type
    {
        return Type::listOf(GraphQL::type($this->getTypeName()));
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
