<?php

namespace Amirmasoud\Pepper\Commands;

use App;
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
        foreach (config('pepper.models', []) as $model) {
            $modelInstance = new $model;
            $rq->create($modelInstance->getQueryName() . 'Query', $modelInstance->getQueryName(), $modelInstance->getQueryDescription(), $model);
        }
    }
}
