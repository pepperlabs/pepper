<?php

namespace Tests\Unit;

use Tests\Support\GraphQL\test_graphql;
use Tests\Support\GraphQL\TestGraphQL;
use Tests\TestCase;

class GraphQLBaseTest extends TestCase
{
    private $test_1;
    private $test_2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->test_1 = new TestGraphQL();
        $this->test_2 = new test_graphql();
    }

    /** @test */
    public function base_classname()
    {
        $this->assertEquals($this->test_1->name(), 'TestGraphQL');
        $this->assertEquals($this->test_2->name(), 'test_graphql');
    }

    /** @test */
    public function base_classname_studly()
    {
        $this->assertEquals($this->test_1->studly(), 'TestGraphQL');
        $this->assertEquals($this->test_2->studly(), 'TestGraphql');
    }
}
