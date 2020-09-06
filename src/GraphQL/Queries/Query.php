<?php

namespace Pepper\GraphQL\Queries;

use GraphQL\Type\Definition\Type;
use Pepper\GraphQL\Inputs\OrderInput;
use Pepper\Supports\GraphQL as PepperGraphQL;
use Pepper\Supports\Resolve;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query as GraphQLQuery;

class Query extends GraphQLQuery
{
    use PepperGraphQL, Resolve;

    public function setAttributes()
    {
        $this->attributes['name'] = $this->instance->snake();
        $this->attributes['description'] = $this->instance->snake().'Description';
    }

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
    public function type(): Type
    {
        return Type::listOf(GraphQL::type($this->getName()));
    }

    public function args(): array
    {
        $order = new OrderInput();

        return [
            // Condition
            'where' => ['name' => 'where', 'type' => GraphQL::type($this->instance->name().'Input')],

            'distinct' => ['name' => 'distinct', 'type' => Type::boolean()],

            // Order
            'order_by' => ['name' => 'orderBy', 'type' => GraphQL::type($this->instance->name().'OrderInput')],

            // Paginate
            'limit' => ['name' => 'limit', 'type' => Type::int()],
            'offset' => ['name' => 'offset', 'type' => Type::int()],
            'skip' => ['name' => 'skip', 'type' => Type::int()],
            'take' => ['name' => 'take', 'type' => Type::int()],
        ];
    }
}
