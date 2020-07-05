<?php

namespace Amirmasoud\Pepper;

use Rebing\GraphQL\Support\Type as GraphQLType;

class ResourceType extends GraphQLType
{
    protected $attributes = [
        'name'          => 'NAME @TODO',
        'description'   => 'DESCRIPTION @TODO',
    ];

    public function __construct($model)
    {
        $this->attributes['model'] = $model;
    }

    public function fields(): array
    {
        return call_user_func_array([new $this->model, 'getFields'], []);
    }
}
