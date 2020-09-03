<?php

namespace Tests\Unit;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\Support\GraphQL\Post;
use Tests\Support\GraphQL\test_graphql;
use Tests\Support\GraphQL\TestGraphQL;
use Tests\TestCaseDatabase;

class GraphQLBaseTest extends TestCaseDatabase
{
    private $test_1;
    private $test_2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->test_1 = new TestGraphQL();
        $this->test_2 = new test_graphql();
        $this->post = new Post();
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

    /** @test */
    public function it_can_customize_model()
    {
        // We have changed our namespace.models config value to Tests\Support\Models in setup environment method.
        $this->assertEquals($this->test_1->modelClass(), 'Tests\Support\Models\TestGraphQL');

        /**
         * custom model is set in test_2.
         *
         * tests private method defaultModel as well.
         */
        $this->assertEquals($this->test_2->modelClass(), 'Tests\Support\Models\User');
    }

    /** @test */
    public function it_can_get_instance_of_model()
    {
        $this->assertTrue($this->test_2->model() instanceof \Tests\Support\Models\User);

        /** tests private method modelRelflection */
        $this->expectException(ModelNotFoundException::class);
        $this->assertTrue($this->test_1->model() instanceof App\TestGraphQL);
    }

    /** @test */
    public function it_exposes_correct_fields()
    {
        $columns = [
            'id',
            'title',
            'body',
            'user_id',
            'properties',
            'flag',
            'published_at',
            'created_at',
            'updated_at',
        ];

        $relations = [
            'user',
            'comments',
            'likes',
        ];

        /** When there is no exposed property */
        // Relations + Columns = Relations AND Columns
        $this->assertEqualsCanonicalizing($this->post->exposedFields(true, true), array_merge($columns, $relations));
        // Relations - Columns = Relations
        $this->assertEqualsCanonicalizing($this->post->exposedFields(true, false), $relations);
        // - Relations + Columns = Columns
        $this->assertEqualsCanonicalizing($this->post->exposedFields(false, true), $columns);
        // - Relations - Columns = []
        $this->assertEqualsCanonicalizing($this->post->exposedFields(false, false), []);

        /** When there is exposed property. */
        $exposed = $this->post->exposed = [
            // Columns
            'properties',
            'flag',
            // Relation
            'comments',
        ];
        // Relations + Columns = Relations AND Columns = exposed
        $this->assertEqualsCanonicalizing($this->post->exposedFields(true, true), $exposed);
        // Relations - Columns = Relations = $exposed - columns
        $this->assertEqualsCanonicalizing($this->post->exposedFields(true, false), ['comments']);
        // - Relations + Columns = Columns = $exposed - relations
        $this->assertEqualsCanonicalizing($this->post->exposedFields(false, true), ['properties', 'flag']);
        // - Relations - Columns = []
        $this->assertEqualsCanonicalizing($this->post->exposedFields(false, false), []);
    }

    /** @test */
    public function covered_fields_are_working()
    {
        $this->assertEqualsCanonicalizing($this->post->coveredFields(), []);

        $covered = $this->post->covered = [
            'properties',
            'flag',
        ];
        $this->assertEqualsCanonicalizing($this->post->coveredFields(), $covered);
    }

    /** @test */
    public function it_exclude_and_include_covered_and_exposed_fields_in_fields_array()
    {
        $columns = [
            'id',
            'title',
            'body',
            'user_id',
            'properties',
            'flag',
            'published_at',
            'created_at',
            'updated_at',
        ];

        $relations = [
            'user',
            'comments',
            'likes',
        ];

        $this->assertEqualsCanonicalizing($this->post->fieldsArray(), array_merge($columns, $relations));

        // Cover column
        $covered = $this->post->covered = ['user_id'];
        $this->assertEqualsCanonicalizing($this->post->fieldsArray(), array_diff(array_merge($columns, $relations), $covered));

        // Cover relation
        $covered = $this->post->covered = ['likes'];
        $this->assertEqualsCanonicalizing($this->post->fieldsArray(), array_diff(array_merge($columns, $relations), $covered));

        // Cover relation and column
        $covered = $this->post->covered = ['user_id', 'likes'];
        $this->assertEqualsCanonicalizing($this->post->fieldsArray(), array_diff(array_merge($columns, $relations), $covered));

        $covered = $this->post->covered = [];

        // Expose only column
        $exposed = $this->post->exposed = [
            'id',
            'title',
            'body',
        ];
        $this->assertEqualsCanonicalizing($this->post->fieldsArray(), $exposed);

        // Expose only relations
        $exposed = $this->post->exposed = [
            'comments',
            'likes',
        ];
        $this->assertEqualsCanonicalizing($this->post->fieldsArray(), $exposed);

        // Expose column and relations
        $exposed = $this->post->exposed = [
            'id',
            'title',
            'body',
            'comments',
            'likes',
        ];
        $this->assertEqualsCanonicalizing($this->post->fieldsArray(), $exposed);

        // If user exposed some fields and for some reason also included it in covered へ‿(ツ)‿ㄏ
        $exposed = $this->post->exposed = [
            'id',
            'title',
            'body',
            'comments',
            'likes',
        ];
        $covered = $this->post->covered = [
            'comments',
            'title',
        ];
        $this->assertEqualsCanonicalizing($this->post->fieldsArray(), array_diff($exposed, $covered));
    }
}
