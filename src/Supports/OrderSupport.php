<?php

namespace Pepper\Supports;

use Rebing\GraphQL\Support\Facades\GraphQL;

trait OrderSupport
{
    /**
     * Fields that can be ordered by. Here we have an Enum type to cover all
     * every types without explicitly typing them and defining their types.
     *
     * @return array
     */
    public function getOrderFields(): array
    {
        $fields = [];

        foreach ($this->fieldsArray() as $attribute) {
            $fields[$attribute] = [
                'name' => $attribute,
                'type' => GraphQL::type('OrderByEnum'),
            ];
        }

        return $fields;
    }
}
