<?php

namespace Tests\Feature;

use Tests\Support\Models\User;
use Tests\TestCaseDatabase;

class MutationTest extends TestCaseDatabase
{
    /** @test */
    public function simple_update_by_pk()
    {
        $user = factory(User::class)->create([
            'name' => 'Old Name'
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

    /** @test */
    public function simple_insert()
    {
        $graphql = <<<GQL
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
                    ]
                ],
            ],
        ];

        $response->assertOk();
        $this->assertEquals($expectedResult, $response->json());
    }
}
