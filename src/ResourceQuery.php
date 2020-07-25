<?php

namespace Amirmasoud\Pepper;

use Rebing\GraphQL\Support\Facades\GraphQL;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\InputObjectType;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use GraphQL\Type\Definition\ObjectType;
use ArrayAccess;
use Closure;

class ResourceQuery extends Query implements ArrayAccess
{
    protected $model;

    private static $instance;

    protected $attributes = [
        'name' => 'Users query',
    ];

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function type(): Type
    {
        return Type::listOf(GraphQL::type(([$this->model, 'typeName'])));
    }

    public function args(): array
    {
        return [];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return $this->model->all();
    }

    public function offsetExists($offset): bool
    {
        return isset($this->attributes[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->attributes[$offset] ?? '';
    }

    public function offsetSet($offset, $value)
    {
        $this->attributes[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }

    public function toType()
    {
        return ($this->toArray());
    }

    public function toArray(): array
    {
        return $this->getAttributes();
    }

    public static function getInstance($model)
    {
        if (is_null(self::$instance)) {
            self::$instance = new self($model);
        }
        return self::$instance->toType();
    }
}
