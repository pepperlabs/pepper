<?php

declare(strict_types=1);

namespace Pepper;

use HaydenPierce\ClassFinder\ClassFinder;
use Rebing\GraphQL\Support\Type as GraphQLType;

class Type extends GraphQLType
{
    protected $attributes = [];

    protected $instance;

    public function __construct($instance)
    {
        $this->instance = new $instance;
        $this->attributes['name'] = $this->instance->getTypeName();
        $this->attributes['name'] = $this->instance->getTypeDescription() . 'FUUUUUUCK';
    }

    public function fields(): array
    {
        return $this->instance->getTypeFields();
    }

    public static function boot()
    {
        $types = [];
        foreach (ClassFinder::getClassesInNamespace('App\Http\Pepper\\') as $type) {
            $name = class_basename($type);
            $types[$name . 'Type'] = forward_static_call_array(['self', 'init'], [$name]);
        }
        return $types;
    }

    public static function init($class)
    {
        return new static('App\Http\Pepper\\' . $class);
    }
}
