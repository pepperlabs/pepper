<?php

namespace Tests\Feature;

use Tests\TestCaseDatabase;

class MutationTest extends TestCaseDatabase
{
    /** @test */
    public function simple_insert()
    {
        $graphql = <<<GQL
        mutation insert_example {
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
