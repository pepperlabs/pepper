<?php

namespace Tests\Feature;

use Tests\TestCaseDatabase;

class IntrospectionTest extends TestCaseDatabase
{
    /**
     * @group sqlite
     * @group mysql
     * @group pgsql
     * @group sqlsrv
     * @test
     */
    public function introspection_status_ok()
    {
        $introspection = new \GraphQL\Type\Introspection;

        $response = $this->call('GET', '/graphql', [
            'query' => $introspection->getIntrospectionQuery(),
        ]);

        $response->assertOk();
    }
}
