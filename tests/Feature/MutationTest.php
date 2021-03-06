<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Support\Models\User;
use Tests\TestCaseDatabase;

class MutationTest extends TestCaseDatabase
{
    /**
     * @group sqlite
     * @group mysql
     * @group pgsql
     * @group sqlsrv
     * @test
     */
    public function simple_update_by_pk()
    {
        $user = factory(User::class)->create([
            'name' => 'Old Name',
        ]);

        $graphql = <<<GQL
mutation {
    update_user_by_pk(
        pk_columns: {
            id: $user->id
        },
        _set: {
            name: "New Name"
        }
    ) {
        id
        name
    }
}
GQL;

        $response = $this->call('POST', '/graphql', [
            'query' => $graphql,
        ]);

        $expectedResult = [
            'data' => [
                'update_user_by_pk' => [
                    'id' => $user->id,
                    'name' => 'New Name',
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
    public function simple_update()
    {
        $user = factory(User::class)->create([
            'name' => 'Old Name',
        ]);

        $graphql = <<<GQL
mutation {
    update_user(
        where: {
            id: { _eq: $user->id }
        },
        _set: {
            name: "New Name"
        }
    ) {
        id
        name
    }
}
GQL;

        $response = $this->call('POST', '/graphql', [
            'query' => $graphql,
        ]);

        $expectedResult = [
            'data' => [
                'update_user' => [
                    [
                        'id' => $user->id,
                        'name' => 'New Name',
                    ],
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
    public function simple_insert_one()
    {
        $graphql = <<<'GQL'
mutation {
    insert_user_one(
        object: {
            name: "Name"
        }
    ) {
        id
        name
    }
}
GQL;

        $response = $this->call('POST', '/graphql', [
            'query' => $graphql,
        ]);

        $expectedResult = [
            'data' => [
                'insert_user_one' => [
                    'id' => 1,
                    'name' => 'Name',
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
    public function simple_insert()
    {
        $graphql = <<<'GQL'
mutation {
    insert_user(
        objects: [{ name: "name #1" }, { name: "name #2" }]
    ) {
        id
        name
    }
}
GQL;

        $response = $this->call('POST', '/graphql', [
            'query' => $graphql,
        ]);

        $expectedResult = [
            'data' => [
                'insert_user' => [
                    [
                        'id' => 1,
                        'name' => 'name #1',
                    ],
                    [
                        'id' => 2,
                        'name' => 'name #2',
                    ],
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
    public function simple_delete_by_pk()
    {
        $user = factory(User::class)->create([
            'name' => 'Name',
        ]);

        $graphql = <<<GQL
mutation {
    delete_user_by_pk(
        id: $user->id
    ) {
        id
        name
    }
}
GQL;

        $response = $this->call('POST', '/graphql', [
            'query' => $graphql,
        ]);

        $expectedResult = [
            'data' => [
                'delete_user_by_pk' => [
                    'id' => $user->id,
                    'name' => 'Name',
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
    public function simple_delete()
    {
        $user_1 = factory(User::class)->create([
            'name' => 'Name #1',
        ]);

        $user_2 = factory(User::class)->create([
            'name' => 'Name #2',
        ]);

        $graphql = <<<GQL
mutation {
    delete_user(
        where: {
            id: { _lte: $user_2->id }
        }
    ) {
        id
        name
    }
}
GQL;

        $response = $this->call('POST', '/graphql', [
            'query' => $graphql,
        ]);

        $expectedResult = [
            'data' => [
                'delete_user' => [
                    [
                        'id' => $user_1->id,
                        'name' => 'Name #1',
                    ],
                    [
                        'id' => $user_2->id,
                        'name' => 'Name #2',
                    ],
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
    public function upload()
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->create('cover.jpg');

        $graphql = <<<'GQL'
mutation($cover: Upload!) {
    insert_post(
        objects: [{ cover: $cover, title: "Upload Sample" }]
    ) {
        id
        cover_url
    }
}
GQL;

        $response = $this->call(
            'POST',
            '/graphql',
            [
                'operations' => json_encode([
                    'query' => $graphql,
                    'variables' => [
                        'cover' => null,
                    ],
                ]),
                'map' => json_encode([
                    '0' => ['variables.cover'],
                ]),
            ],
            [],
            [
                '0' => $file,
            ],
            [
                'CONTENT_TYPE' => 'multipart/form-data',
            ]
        );

        $expectedResult = [
            'data' => [
                'insert_post' => [
                    [
                        'id' => 1,
                        'cover_url' => 'posts/'.$file->hashName(),
                    ],
                ],
            ],
        ];

        $response->assertOk();
        Storage::disk('local')->assertExists('posts/'.$file->hashName());
        $this->assertEquals($expectedResult, $response->json());
    }
}
