<?php

namespace Pepper\Tests\Unit;

use Pepper\Tests\TestCase;
use Pepper\Helpers\ConfigHelper as Config;

class ConfigTest extends TestCase
{
    /** @var Pepper\Helpers\ConfigHelper */
    private $config;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->config = new Config($this->configPath);
    }

    /**
     * Test adding new type to GraphQL config file.
     *
     * @return void
     */
    public function testAddType(): void
    {
        $this->config->addType('TestType', 'TestTypeClass');
        $this->assertTrue($this->config->repository->has('types.TestType'));
        $this->assertEquals($this->config->repository->get('types.TestType'), 'App\GraphQL\Types\Pepper\TestTypeClass');
    }

    /**
     * Test adding new query to GraphQL config file.
     *
     * @return void
     */
    public function testAddQuery(): void
    {
        $this->config->addQuery('test_query', 'TestQueryClass');
        $this->assertTrue($this->config->repository->has('schemas.default.query.test_query'));
        $this->assertEquals($this->config->repository->get('schemas.default.query.test_query'), 'App\GraphQL\Queries\Pepper\TestQueryClass');
    }

    /**
     * Test adding new mutation to GraphQL config file.
     *
     * @return void
     */
    public function testAddMutation(): void
    {
        $this->config->addMutation('test_mutation', 'TestMutationClass');
        $this->assertTrue($this->config->repository->has('schemas.default.mutation.test_mutation'));
        $this->assertEquals($this->config->repository->get('schemas.default.mutation.test_mutation'), 'App\GraphQL\Mutations\Pepper\TestMutationClass');
    }
}
