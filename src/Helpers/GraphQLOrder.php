<?php

namespace Pepper\Helpers;

use Rebing\GraphQL\Support\Facades\GraphQL;

trait GraphQLOrder
{
    /**
     * Get GraphQL ordering fields.
     *
     * @return array
     */
    public function getOrderFields(): array
    {
        $fields = [];

        foreach ($this->getFields() as $attribute) {
            $fields[$attribute] = [
                'name' => $attribute,
                'type' => GraphQL::type('OrderByEnum')
            ];
        }

        return $fields;
    }

    /**
     * Get GraphQL Order name.
     *
     * @return string
     */
    public function getOrderName(): string
    {
        $method = 'setOrderName';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName() . 'Order';
        }
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
            return $this->getName() . ' order description.';
        }
    }
}
