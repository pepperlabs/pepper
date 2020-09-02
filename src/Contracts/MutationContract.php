<?php

namespace Pepper\Contracts;

interface MutationContract
{
    public function setInstance();

    public function setType();

    public function setArgs();

    public function setResolve();
}
