<?php

namespace Pepper;

class AjiMaji
{
    private $parent;

    public function __construct($pepper, $parent)
    {
        $this->parent = new $parent($pepper);
    }

    public function __call($name, $arguments)
    {
        logger($name);
        return call_user_func_array([$this->parent, $name], $arguments);
    }

    public function __get($key)
    {
        return $this->parent[$key];
    }

    public function __set(string $key, $value): void
    {
        $this->parent->__set($key, $value);
    }
}
