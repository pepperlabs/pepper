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
                    $notQuery = clone $query;
                    foreach ($criteria as $f => $c) {
                        foreach ($c as $operation => $value) {
                            $notQuery = $this->executeCondition($notQuery, $operation, $f, $value, 'and');
                        }
                    }
                    $query = $query->whereNotIn('id', $notQuery->pluck('id')->toArray());
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

    private function executeCondition($query, $operation, $field, $value, $exp = 'and')
    {
        switch ($operation) {
            case '_eq':
                return $query->where($field, '=', $value, $exp);

            case '_neq':
                return $query->where($field, '!=', $value, $exp);

            case '_gt':
                return $query->where($field, '>', $value, $exp);

            case '_lt':
                return $query->where($field, '<', $value, $exp);

            case '_gte':
                return $query->where($field, '>=', $value, $exp);

            case '_lte':
                return $query->where($field, '<=', $value, $exp);

            case '_in':
                return $query->whereIn($field, $value, $exp);

            case '_nin':
                return $query->whereNotIn($field, $value, $exp);

            case '_like':
                return $query->where($field, 'like', $value, $exp);

            case '_nlike':
                return $query->where($field, 'not like', $value, $exp);

            case '_ilike':
                return $query->where($field, 'ilike', $value, $exp);

            case '_nilike':
                return $query->where($field, 'not ilike', $value, $exp);

            case '_is_null':
                return $value ? $query->whereNull($field, $exp) : $query->whereNotNull($field, $exp);

            case '_date':
                return $query->whereDate($field, '=', $value, $exp);

            case '_month':
                return $query->whereMonth($field, '=', $value, $exp);

            case '_day':
                return $query->whereDay($field, '=', $value, $exp);

            case '_year':
                return $query->whereYear($field, '=', $value, $exp);

            case '_time':
                return $query->whereTime($field, '=', $value, $exp);

            default:
                return $query->whereHas($field, function ($q) use ($value, $operation, $exp) {
                    foreach ($value as $nestedOperation => $nestedValue) {
                        $this->executeCondition($q, $nestedOperation, $operation, $nestedValue, $exp);
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

    public function getQueryByPkType(): Type
    {
        return GraphQL::type($this->getTypeName());
    }

    public function getQueryByPkFields(): array
    {
        $fields = [];

        // Get fields excluded relations
        foreach ($this->getFields(false) as $attribute) {
            $fields[$attribute] = [
                'name' => $attribute,
                'type' => $this->call_field_type($attribute)
            ];
        }

        return $fields;
    }

    public function getQueryByPkName(): string
    {
        $method = 'setQueryByPkName';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName() . 'QueryByPk';
        }
    }

    public function getQueryByPkDescription(): string
    {
        $method = 'setQueryByPkDescription';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName() . ' query by PK description.';
        }
    }

    public function queryByPk($root, $args, $context, $resolveInfo, $getSelectFields)
    {
        $model = $this->newModel();
        $pk = $model->getKeyName();

        // Let the new born out in the wild.
        $root = $this->getModel()::where($pk, $args[$pk]);

        // return types are satisfied when they are iterable enough.
        return $this->getQueryResolve($root, $args, $context, $resolveInfo, $getSelectFields)->first();
    }

    public function resolveQueryAggregate($root, $args, $context, $resolveInfo, $getSelectFields)
    {
        $query = $this->instance->getQueryResolve($root, $args, $context, $resolveInfo, $getSelectFields)->get();
        return [
            'aggregate' => ['root' => $query, 'name' => preg_replace('/_aggregate$/', '', array_reverse($resolveInfo->path)[0])],
            'nodes' => $query
        ];
    }
}
