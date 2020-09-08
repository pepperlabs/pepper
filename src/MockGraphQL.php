<?php

namespace Pepper;

class MockGraphQL
{
    private $parent;

    public function __construct($pepper, $parent)
    {
        $this->parent = new $parent($pepper);
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->parent, $name], $arguments);
    }

    public static function __callStatic($name, $arguments)
    {
        return forward_static_call_array([self::$parent, $name], $arguments);
    }

    public function __get($key)
    {
        $attributes = $this->parent->getAttributes();

        return $attributes[$key] ?? null;
    }

    public function __set(string $key, $value): void
    {
        $this->parent->__set($key, $value);
    }

    public static function graphQL($pepper, $parent)
    {
        return new class($pepper, $parent) extends MockGraphQL {
        };
    }
}
