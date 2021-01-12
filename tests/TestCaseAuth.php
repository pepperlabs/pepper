<?php

namespace Tests;

abstract class TestCaseAuth extends TestCaseDatabase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('pepper.auth.disabled', false);

        $this->clearCache();
    }
}
