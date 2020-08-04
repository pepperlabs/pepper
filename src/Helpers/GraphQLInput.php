<?php

namespace Pepper\Helpers;

use Rebing\GraphQL\Support\Facades\GraphQL;

trait GraphQLInput
{
    public function getInputFields(): array
    {
        $fields = [];

        foreach ($this->getFields() as $attribute) {
            $fields[$attribute] = [
                'name' => $attribute,
                'type' => GraphQL::type('ConditionInput')
            ];
        }

        return $fields;
    }

    /**
     * Get GraphQL Input name.
     *
     * @return string
     */
    public function getInputName(): string
    {
        $method = 'setInputName';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName() . 'Input';
        }
    }

    /**
     * Get GraphQL Input description.
     *
     * @return string
     */
    public function getInputDescription(): string
    {
        $method = 'setInputDescription';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName() . ' input description.';
        }
    }
}
