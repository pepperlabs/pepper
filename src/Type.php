<?php

declare(strict_types=1);

namespace Pepper;

use HaydenPierce\ClassFinder\ClassFinder;
use Rebing\GraphQL\Support\Type as GraphQLType;

class Type extends GraphQLType
{
    protected $attributes = [];

    protected $instance;

    protected $type;

    public function __construct($instance, $type)
    {
        $this->instance = new $instance;
        $this->type = $type;
        switch ($this->type) {
            case 'base':
                $this->attributes['name'] = $this->instance->getTypeName();
                $this->attributes['description'] = $this->instance->getTypeDescription();
                break;
            case 'input':
                $this->attributes['name'] = $this->instance->getInputName();
                $this->attributes['description'] = $this->instance->getInputDescription();
                break;
            case 'order':
                $this->attributes['name'] = $this->instance->getOrderName();
                $this->attributes['description'] = $this->instance->getOrderDescription();
                break;
            case 'field-aggregate':
                $this->attributes['name'] = $this->instance->getFieldAggregateName();
                $this->attributes['description'] = $this->instance->getFieldAggregateDescription();
                break;
            case 'result-aggregate':
                $this->attributes['name'] = $this->instance->getResultAggregateName();
                $this->attributes['description'] = $this->instance->getResultAggregateDescription();
                break;
            case 'aggregate':
                $this->attributes['name'] = $this->instance->getAggregateName();
                $this->attributes['description'] = $this->instance->getAggregateDescription();
                break;
            case 'aggregate-unresolvable':
                $this->attributes['name'] = $this->instance->getAggregateUnresolvableName();
                $this->attributes['description'] = $this->instance->getAggregateUnresolvableDescription();
                break;
            case 'mutation':
                $this->attributes['name'] = $this->instance->getMutationName();
                $this->attributes['description'] = $this->instance->getMutationDescription();
                break;
            default:
                $this->attributes['name'] = $this->instance->getTypeName();
                $this->attributes['description'] = $this->instance->getTypeDescription();
                break;
        }
    }

    public function fields(): array
    {
        switch ($this->type) {
            case 'base':
                return $this->instance->getTypeFields();
                break;
            case 'input':
                return $this->instance->getInputFields();
                break;
            case 'order':
                return $this->instance->getOrderFields();
                break;
            case 'field-aggregate':
                return $this->instance->getFieldAggregateTypeFields();
                break;
            case 'result-aggregate':
                return $this->instance->getResultAggregateFields();
                break;
            case 'aggregate':
                return $this->instance->getAvailableAggregators();
                break;
            case 'aggregate-unresolvable':
                return $this->instance->getFieldAggregateTypeFields(false);
                break;
            case 'mutation':
                return $this->instance->getMutationType();
                break;
            default:
                return $this->instance->getTypeFields();
                break;
        }
    }

    public static function boot()
    {
        $classes = [];
        foreach (ClassFinder::getClassesInNamespace('App\Http\Pepper\\') as $class) {
            $name = class_basename($class);
            $classes[$name . 'Type'] = forward_static_call_array(['self', 'init'], [$name, 'base']);
            $classes[$name . 'Input'] = forward_static_call_array(['self', 'init'], [$name, 'input']);
            $classes[$name . 'Order'] = forward_static_call_array(['self', 'init'], [$name, 'order']);
            // $classes[$name . 'FieldAggregateType'] = forward_static_call_array(['self', 'init'], [$name, 'field-aggregate']);
            // $classes[$name . 'ResultAggregateType'] = forward_static_call_array(['self', 'init'], [$name, 'result-aggregate']);
            // $classes[$name . 'AggregateType'] = forward_static_call_array(['self', 'init'], [$name, 'aggregate']);
            // $classes[$name . 'FieldAggregateUnresolvableType'] = forward_static_call_array(['self', 'init'], [$name, 'aggregate-unresolvable']);
            // $classes[$name . 'Mutation'] = forward_static_call_array(['self', 'init'], [$name, 'mutation']);
        }
        return $classes;
    }

    public static function init($class, $type)
    {
        return new static('App\Http\Pepper\\' . $class, $type);
    }
}
