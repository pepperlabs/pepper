<?php

namespace Tests\Feature;

use Tests\TestCaseDatabase;

class AuthTest extends TestCaseDatabase
{
    /**
     * @group sqlite
     * @group mysql
     * @group pgsql
     * @group sqlsrv
     * @test
     */
    public function register()
    {
        $graphql = <<<'GQL'
            mutation {
                register(
                name: "Amirmasoud"
                email: "amirmasoud@pepper.fake"
                password: "123456789"
                password_confirmation: "123456789"
                ) {
                    token
                }
            }
GQL;

        $response = $this->call('POST', '/graphql', [
            'query' => $graphql,
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'register' => [
                        'token'
                    ]
                ],
            ]);
    }
}
