<?php

namespace Pepper\Supports;

use Closure;
use GraphQL\Type\Definition\ResolveInfo;

trait Resolve
{
    /**
     * Get GraphQL Query resolve.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|null $root
     * @param  array  $args
     * @param  object  $context
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @param  \Closure  $getSelectFields
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getQueryResolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        if (is_null($root)) {
            $model = $this->model();
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
}
