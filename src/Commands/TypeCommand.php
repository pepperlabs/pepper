<?php

namespace Amirmasoud\Pepper\Commands;

use Amirmasoud\Pepper\Helpers\ResourceQueryCreator;
use Amirmasoud\Pepper\Helpers\ResourceTypeCreator;
use Illuminate\Filesystem\Filesystem;

class TypeCommand extends BaseCommand
{
    /** @var string */
    protected $signature = 'pepper:types';

    /** @var string */
    protected $description = 'Generate GraphQL types.';

    public function handle()
    {
        $fs = new Filesystem();
        $rq = new ResourceTypeCreator($fs);
        $rq->create('UserType', 'user', 'User query description');
    }
}
