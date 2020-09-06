<?php

namespace Pepper\GraphQL\Queries;

use GraphQL\Type\Definition\Type;
use Pepper\GraphQL\Inputs\Input;
use Pepper\GraphQL\Inputs\OrderInput;
use Pepper\Supports\GraphQL as PepperGraphQL;
use Pepper\Supports\Resolve;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query as GraphQLQuery;

class Query extends GraphQLQuery
{
    use PepperGraphQL, Resolve;

    protected $attributes = [
        'name' => 'DummyName',
        'description' => 'DummyDescription',
    ];

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
        $input = new Input();
        $order = new OrderInput();

        return [
            // Condition
            'where' => ['type' => GraphQL::type($input->getName())],

            'distinct' => ['name' => 'distinct', 'type' => Type::boolean()],

            // Order
            'order_by' => ['type' => GraphQL::type($order->getName())],

            // Paginate
            'limit' => ['name' => 'limit', 'type' => Type::int()],
            'offset' => ['name' => 'offset', 'type' => Type::int()],
            'skip' => ['name' => 'skip', 'type' => Type::int()],
            'take' => ['name' => 'take', 'type' => Type::int()],
        ];
    }
}
