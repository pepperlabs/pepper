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

    /**
     * Get GraphQL order description.
     *
     * @return string
     */
    public function getOrderDescription(): string
    {
        $method = 'setOrderDescription';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName().' order description.';
        }
    }
}
