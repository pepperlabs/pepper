<?php

namespace Pepper\Helpers;

trait GraphQLType
{
    /**
     * Generate GraphQL fields with field types.
     *
     * @return array
     */
    public function getTypeFields(): array
    {
        $fields = [];

        // exclude relations
        foreach ($this->getFields(false) as $attribute) {
            $fields[$attribute] = [
                'name' => $attribute,
                'type' => $this->call_field_type($attribute)
            ];
        }

        return $fields;
    }

    /**
     * Get GraphQL Type name.
     *
     * @return string
     */
    public function getTypeName(): string
    {
        $method = 'setTypeName';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName() . 'Type';
        }
    }

    /**
     * Get GraphQL type description.
     *
     * @return string
     */
    public function getTypeDescription(): string
    {
        $method = 'setTypeDescription';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName() . ' type description.';
        }
    }
}
