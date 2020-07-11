<?php

namespace Amirmasoud\Pepper\Commands;

use Amirmasoud\Pepper\Helpers\ResourceQueryCreator;
use Illuminate\Filesystem\Filesystem;

class MetadataCommand extends BaseCommand
{
    /** @var string */
    protected $signature = 'pepper:metadata';

    /** @var string */
    protected $description = 'Generate GraphQL metadata.';

    public function handle()
    {
        $fs = new Filesystem();
        $rq = new ResourceQueryCreator($fs);
        $rq->create('UserQuery', 'users', 'User query description', 'Type::listOf(GraphQL::type(\'user\'))', '[]', 'return \App\User::all();');
    }
}
