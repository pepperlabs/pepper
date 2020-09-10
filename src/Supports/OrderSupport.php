<?php

namespace Pepper\Supports;

use Rebing\GraphQL\Support\Facades\GraphQL;

trait OrderSupport
{
    /**
     * Get GraphQL ordering fields.
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
