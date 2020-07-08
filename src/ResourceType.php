<?php

namespace Amirmasoud\Pepper;

use Rebing\GraphQL\Support\Type as GraphQLType;
use ArrayAccess;

class ResourceType extends GraphQLType implements ArrayAccess
{
    protected $attributes = [
        'name'          => 'user',
        'description'   => 'description',
    ];

    private static $instance;

    public function __construct($model)
    {
        $this->attributes['model'] = $model;
    }

    public function fields(): array
    {
        return call_user_func_array([new $this->model, 'getFields'], []);
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

    public static function getInstance($model)
    {
        if (is_null(self::$instance)) {
            self::$instance = new self($model);
        }
        return self::$instance;
    }
}
