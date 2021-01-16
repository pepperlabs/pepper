<?php

namespace Tests\Feature;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Tests\Support\Models\User;
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
                        'token',
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
    public function login()
    {
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
                        'token',
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
    public function forgot_password_success()
    {
        Notification::fake();
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
    public function forgot_password_fail()
    {
        Notification::fake();
        $this->createNewUser();

        $graphql = '
        mutation {
            forgot_password(
                email: "amirmasoud_not_exists@pepper.fake"
            ) {
                status
            }
        }';

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
                    'forgot_password',
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
    public function reset_password_success()
    {
        Notification::fake();

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

        $user = User::first();
        $resetToken = '';
        Notification::assertSentTo(
            $user,
            function (ResetPassword $notification, $channels) use (&$resetToken) {
                $resetToken = $notification->token;
                return true;
            }
        );

        $graphql = <<<GQL
        mutation {
            reset_password(
                email: "amirmasoud@pepper.fake"
                token: "$resetToken"
                password: "1234567890"
                password_confirmation: "1234567890"
            ) {
                status
            }
        }
GQL;

        $response = $this->call('POST', '/graphql', [
            'query' => $graphql,
        ]);

        $expectedResult = [
            'data' => [
                'reset_password' => [
                    'status' => 'Your password has been reset!',
                ],
            ],
        ];

        $response->assertOk();
        $this->assertEquals($expectedResult, $response->json());
    }
}
