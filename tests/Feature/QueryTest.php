<?php

namespace Pepper\Tests\Feature;


use Pepper\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class QueryTest extends TestCase
{
    // public function testStatus()
    // {
    //     $response = dd($this->getJson('http://localhost:8000/graphql'));

    //     $response->assertStatus(200);
    // }

    // /**
    //  * A basic test example.
    //  *
    //  * @return void
    //  */
    // public function testSimpleQueryByPk()
    // {
    //     $response = $this->withHeaders([
    //         'Content-Type' => 'application/json',
    //     ])->json('GET', '/graphql', [
    //         'query' => 'query {user_by_pk(id: 1){id}}'
    //     ]);
    //     $response->assertStatus(200)->assertExactJson([
    //         'data' => [
    //             'user_by_pk' => [
    //                 ['id' => '1']
    //             ]
    //         ]
    //     ]);
    // }
}
