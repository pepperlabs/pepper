<?php

namespace Pepper\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /** @var string */
    protected $configPath = __DIR__ . '/Stubs/graphql.stub';

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Hello graphql config file.
        copy($this->configPath, $this->configPath . '.php');
    }

    /**
     * Teardown the test environment.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        // Good bye graphql config file.
        unlink($this->configPath . '.php');
    }
}
