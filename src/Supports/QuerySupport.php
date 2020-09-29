<?php

namespace Pepper\Supports;

use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

trait QuerySupport
{
    /**
     * Get GraphQL Query resolve.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|null  $root
     * @param  array  $args
     * @param  object  $context
     * @param  ResolveInfo  $resolveInfo
     * @param  Closure  $getSelectFields
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getQueryResolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        if (is_null($root)) {
            $model = $this->model();
        } else {
            $model = $root;
        }

        return $model
            ->tap(function ($query) use ($getSelectFields) {
                $fields = $getSelectFields();
                $select = is_null($fields) ? '*' : $fields->getSelect();

                return is_null($fields)
                    ? $query->select($select)
                    : $query->select($select)->with($fields->getRelations());
            })
            ->when(isset($args['where']), function ($query) use (&$args) {
                foreach ($args['where'] as $field => $criteria) {
                    if ($field == '_or') {
                        foreach ($criteria as $f => $c) {
                            foreach ($c as $operation => $value) {
                                $query = $this->runCondition($query, $operation, $f, $value, 'or');
                            }
                        }
                    } elseif ($field == '_and') {
                        foreach ($criteria as $f => $c) {
                            foreach ($c as $operation => $value) {
                                $query = $this->runCondition($query, $operation, $f, $value, 'and');
                            }
                        }
                    } elseif ($field == '_not') {
                        $notQuery = clone $query;
                        foreach ($criteria as $f => $c) {
                            foreach ($c as $operation => $value) {
                                $notQuery = $this->runCondition($notQuery, $operation, $f, $value, 'and');
                            }
                        }
                        $query = $query->whereNotIn('id', $notQuery->pluck('id')->toArray());
                    } else {
                        foreach ($criteria as $operation => $value) {
                            $query = $this->runCondition($query, $operation, $field, $value);
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
            })
            ->when(isset($args['order_by']), function ($query) use (&$args) {
                foreach ($args['order_by'] as $column => $direction) {
                    $query = $query->orderBy($column, $direction);
                }

                return $query;
            });
    }

    /**
     * Run conditions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $operation
     * @param  string  $field
     * @param  string|array  $value
     * @param  string  $exp
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function runCondition($query, $operation, $field, $value, $exp = 'and')
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
                        $this->runCondition($q, $nestedOperation, $operation, $nestedValue, $exp);
                    }
                });
        }
    }

    /**
     * Available query arguments.
     *
     * @return array
     */
    public function getQueryArgs()
    {
        return [
            // Condition
            'where' => ['type' => GraphQL::type($this->getInputName())],

            'distinct' => ['name' => 'distinct', 'type' => Type::boolean()],

            // Order
            'order_by' => ['type' => GraphQL::type($this->getOrderInputName())],

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
     * @return \GraphQL\Type\Definition\Type
     */
    public function getQueryType(): Type
    {
        return Type::listOf(GraphQL::type($this->getTypeName()));
    }

    /**
     * Get query by primary key type.
     *
     * @return \GraphQL\Type\Definition\Type
     */
    public function getQueryByPkType(): Type
    {
        return GraphQL::type($this->getTypeName());
    }

    /**
     * Get query by primary key fields.
     *
     * @return array
     */
    public function getQueryByPkFields(): array
    {
        $model = $this->model();
        $pk = $model->getKeyName();

        return [
            $pk => [
                'name' => $pk,
                'type' => $this->callGraphQLType($pk),
            ],
        ];
    }

    /**
     * Resolve query by PK.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function queryByPk($root, $args, $context, $resolveInfo, $getSelectFields)
    {
        $model = $this->model();
        $pk = $model->getKeyName();

        $root = $this->modelClass()::where($pk, $args[$pk]);

        return $this->getQueryResolve($root, $args, $context, $resolveInfo, $getSelectFields)->first();
    }
}
