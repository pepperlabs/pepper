<?php

namespace Tests\Feature;

use Tests\TestCaseDatabase;

class AuthTest extends TestCaseDatabase
{
    private function createNewUser()
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

        return $this->call('POST', '/graphql', ['query' => $graphql]);
    }

    /**
     * @group sqlite
     * @group mysql
     * @group pgsql
     * @group sqlsrv
     * @test
     */
    public function register()
    {
        $this->createNewUser()
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'register' => [
                        'token'
                    ]
                ],
            ]);
    }

    /**
     * @group sqlite
     * @group mysql
     * @group pgsql
     * @group sqlsrv
     * @test
     */
    public function login(){
        $this->createNewUser();

        $graphql = '
        {
            login(
                email: "amirmasoud@pepper.fake"
                password: "123456789"
            ) {
                token
            }
        }';

        $response = $this->call('GET', '/graphql', [
            'query' => $graphql,
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'login' => [
                        'token'
                    ]
                ],
            ]);
    }
}
