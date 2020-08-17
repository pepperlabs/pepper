<?php

namespace Pepper\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /** @var string */
    protected $config;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->config = include __DIR__ . '/Stubs/graphql.php';
    }
}
