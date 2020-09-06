<?php

namespace Pepper\Query;

use GraphQL\Type\Definition\Type;
use Pepper\Concerns\GraphQL as ConcernsGraphQL;
use Pepper\Concerns\Resolve;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query as GraphQLQuery;

class Query extends GraphQLQuery
{
    use ConcernsGraphQL, Resolve;

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
