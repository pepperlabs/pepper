<?php

namespace Pepper\Helpers;

use Rebing\GraphQL\Support\Facades\GraphQL;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Closure;

trait GraphQLQuery
{
    /**
     * Get GraphQL Query name.
     *
     * @return string
     */
    public function getQueryName(): string
    {
        $method = 'setQueryName';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName() . 'Query';
        }
    }

    /**
     * Get GraphQL Query description.
     *
     * @return string
     */
    public function getQueryDescription(): string
    {
        $method = 'setQueryDescription';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName() . ' query description.';
        }
    }

    /**
     * Get GraphQL Query resolve.
     *
     * @param  object $root
     * @param  array $args
     * @param  object $context
     * @param  ResolveInfo $resolveInfo
     * @param  Closure $getSelectFields
     * @return object
     */
    public function getQueryResolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): object
    {
        if (is_null($root)) {
            $model = $this->newModel();
        } else {
            $model = $root;
        }
        return $model->when(isset($args['where']), function ($query) use (&$args) {
            foreach ($args['where'] as $field => $criteria) {
                if ($field == '_or') {
                    foreach ($criteria as $f => $c) {
                        foreach ($c as $operation => $value) {
                            $query = $this->executeCondition($query, $operation, $f, $value, 'or');
                        }
                    }
                } elseif ($field == '_and') {
                    foreach ($criteria as $f => $c) {
                        foreach ($c as $operation => $value) {
                            $query = $this->executeCondition($query, $operation, $f, $value, 'and');
                        }
                    }
                } elseif ($field == '_not') {
                    foreach ($criteria as $f => $c) {
                        foreach ($c as $operation => $value) {
                            $query = $this->executeCondition($query, $operation, $f, $value, 'and');
                            print_r($query->get(['id']));
                            die();
                            // $query->whereNotIn($query->)
                        }
                    }
                } else {
                    foreach ($criteria as $operation => $value) {
                        $query = $this->executeCondition($query, $operation, $field, $value);
                    }
                }
            }
            return $query;
        })
            ->when(isset($args['limit']), function ($query) use (&$args) {
                return $query->limit($args['limit']);
            })
            ->when(isset($args['offset']), function ($query) use (&$args) {
                return $query->offset($args['offset']);
            })
            ->when(isset($args['skip']), function ($query) use (&$args) {
                return $query->skip($args['skip']);
            })
            ->when(isset($args['take']), function ($query) use (&$args) {
                return $query->take($args['take']);
            })->when(isset($args['order_by']), function ($query) use (&$args) {
                foreach ($args['order_by'] as $column => $direction) {
                    $query = $query->orderBy($column, $direction);
                }
                return $query;
            });
    }

    private function executeCondition($query, $operation, $field, $value, $exp = 'AND')
    {
        switch ($operation) {
            case '_eq':
                return $query->where($field, '=', $value, $exp);

            case '_neq':
                return $query->where($field, '!=', $value);

            case '_gt':
                return $query->where($field, '>', $value);

            case '_lt':
                return $query->where($field, '<', $value);

            case '_gte':
                return $query->where($field, '>=', $value);

            case '_lte':
                return $query->where($field, '<=', $value);

            case '_in':
                return $query->whereIn($field, $value);

            case '_nin':
                return $query->whereNotIn($field, $value);

            case '_like':
                return $query->where($field, 'like', $value);

            case '_nlike':
                return $query->where($field, 'not like', $value);

            case '_ilike':
                return $query->where($field, 'ilike', $value);

            case '_nilike':
                return $query->where($field, 'not ilike', $value);

            case '_is_null':
                return $value ? $query->whereNull($field) : $query->whereNotNull($field);

            case '_date':
                return $query->whereDate($field, $value);

            case '_month':
                return $query->whereMonth($field, $value);

            case '_day':
                return $query->whereDay($field, $value);

            case '_year':
                return $query->whereYear($field, $value);

            case '_time':
                return $query->whereTime($field, $value);

            default:
                return $query->whereHas($field, function ($q) use ($value, $operation) {
                    foreach ($value as $nestedOperation => $nestedValue) {
                        $this->executeCondition($q, $nestedOperation, $operation, $nestedValue);
                    }
                });
        }
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

    /**
     * Get GraphQL query type.
     *
     * @return void
     */
    public function getQueryType(): Type
    {
        return Type::listOf(GraphQL::type($this->getTypeName()));
    }
}
