<?php

namespace Tests\Feature;

use GraphQL\Error\Error;
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

    /**
     * @group sqlite
     * @group mysql
     * @group pgsql
     * @group sqlsrv
     * @test
     */
    public function forgot_password_success(){
        $this->createNewUser();

        $graphql = '
        mutation {
            forgot_password(
                email: "amirmasoud@pepper.fake"
            ) {
                status
            }
        }';

        $response = $this->call('POST', '/graphql', [
            'query' => $graphql,
        ]);

        $expectedResult = [
            'data' => [
                'forgot_password' => [
                    'status' => 'We have emailed your password reset link!',
                ],
            ],
        ];

        $response->assertOk();
        $this->assertEquals($expectedResult, $response->json());
    }

    /**
     * @group sqlite
     * @group mysql
     * @group pgsql
     * @group sqlsrv
     * @test
     */
    public function forgot_password_fail(){
        $this->createNewUser();

        $graphql = '
        mutation {
            forgot_password(
                email: "amirmasoud_not_exists@pepper.fake"
            ) {
                status
            }
        }';

        // $this->expectException(Error::class);

        $response = $this->call('POST', '/graphql', [
            'query' => $graphql,
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'errors' => [
                    [
                        'message',
                    ],
                ],
                'data' => [
                    'forgot_password'
                ],
            ]);
    }
}
