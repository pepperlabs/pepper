<?php

namespace Pepper\Supports;

trait MutationSupport
{
    /**
     * Get input mutation name.
     *
     * @return string
     */
    public function getInputMutationName()
    {
        $method = 'setInputMutationName';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName().'MutationInput';
        }
    }

    /**
     * Get input mutation description.
     *
     * @return string
     */
    public function getInputMutationDescription(): string
    {
        $method = 'setInputMutationDescription';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName().' input mutation description.';
        }
    }
}
