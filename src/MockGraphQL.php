<?php

namespace Pepper;

/**
 * This class is mimicking dynamic inherence.
 */
class MockGraphQL
{
    private $parent;

    /**
     * Dependencies are resolved via IoC.
     *
     * @param  string  $pepper
     * @param  string  $parent
     */
    public function __construct($pepper, $parent)
    {
        $this->parent = new $parent($pepper);
    }

    /**
     * This class is calling $parent method which supposed to be extended by the
     * callee class.
     *
     * @param  string  $name
     * @param  array  $arguments
     * @return mixed
     */
    public function __call(string $method, array $params)
    {
        return call_user_func_array([$this->parent, $method], $params);
    }

    /**
     * We don't have any static method on the parent classes. but we have added
     * this class just in case for the futrue.
     *
     * @param  string  $name
     * @param  array  $arguments
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments)
    {
        return forward_static_call_array([self::$parent, $name], $arguments);
    }

    /**
     * Getting parent attribute.
     *
     * @param  string  $key
     * @return void
     */
    public function __get(string $key)
    {
        $attributes = $this->parent->getAttributes();

        return $attributes[$key] ?? null;
    }

    /**
     * Setting parent value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function __set(string $key, $value): void
    {
        $this->parent->__set($key, $value);
    }

    /**
     * Create an anonymous class that extends this class which would direct
     * every call to parent class.
     *
     * @param  string  $pepper
     * @param  string  $parent
     * @return MockGraphQL
     */
    public static function graphQL(string $pepper, string $parent): MockGraphQL
    {
        return new class($pepper, $parent) extends MockGraphQL {
        };
    }
}
