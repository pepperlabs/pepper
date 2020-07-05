<?php

namespace Amirmasoud\Pepper;

use Rebing\GraphQL\Support\Type as GraphQLType;

class ResourceType extends GraphQLType
{
    // protected $model;

    protected $attributes = [
        'name'          => 'NAME @TODO',
        'description'   => 'DESCRIPTION @TODO'
    ];

    public function __construct($model)
    {
        // $this->model = $model;
        $this->attributes['model'] = $model;
    }

    public function fields(): array
    {
        return $this->model->getFields();
    }
}
