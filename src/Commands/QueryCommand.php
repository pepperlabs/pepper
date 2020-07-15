<?php

namespace Amirmasoud\Pepper\Commands;

use Amirmasoud\Pepper\Helpers\ResourceQueryCreator;
use Illuminate\Filesystem\Filesystem;

class QueryCommand extends BaseCommand
{
    /** @var string */
    protected $signature = 'pepper:queries';

    /** @var string */
    protected $description = 'Generate GraphQL queries.';

    public function handle()
    {
        $fs = new Filesystem();
        $rq = new ResourceQueryCreator($fs);
        $rq->create('UsersQuery', 'users', 'User query description', 'Type::listOf(GraphQL::type(\'user\'))', '[]', 'return \App\User::all();');
    }
}
