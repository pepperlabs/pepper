<?php

namespace Pepper\Contracts;

interface TypeContract
{
    public function setClassName();

    public function getClassName();

    public function setName();

    public function getName();

    public function setDescription();

    public function getDescription();

    public function setModel();

    public function getModel();

    public function setArgs();

    public function getArgs();

    public function setResolve();

    public function getResolve();
}
