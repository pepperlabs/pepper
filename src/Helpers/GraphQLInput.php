<?php

namespace Pepper\Helpers;

use Rebing\GraphQL\Support\Facades\GraphQL;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Str;

trait GraphQLInput
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
