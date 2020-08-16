<?php

namespace Pepper\Supports;

use Illuminate\Support\Str;
use Rebing\GraphQL\Support\Facades\GraphQL;

trait InputSupport
{
    public function getInputFields(): array
    {
        $fields = [];

        $relations = $this->getRelations();
        foreach ($this->getFields() as $attribute) {
            if (in_array($attribute, $relations)) {
                $fields[$attribute] = [
                    'name' => $attribute,
                    /** @todo FIX the type name */
                    'type' => GraphQL::type(Str::of($attribute)->singular()->studly() . 'Input')
                ];
            } else {
                $fields[$attribute] = [
                    'name' => $attribute,
                    'type' => GraphQL::type('ConditionInput')
                ];
            }
        }

        $fields['_and'] = [
            'name' => '_and',
            'type' => GraphQL::type($this->getInputName())
        ];

        $fields['_or'] = [
            'name' => '_or',
            'type' => GraphQL::type($this->getInputName())
        ];

        $fields['_not'] = [
            'name' => '_not',
            'type' => GraphQL::type($this->getInputName())
        ];

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
