<?php

namespace Pepper\Commands;

use Pepper\HasEndpoint;
use App;
use Pepper\Helpers\ResourceQueryCreator;
use Illuminate\Filesystem\Filesystem;
use HaydenPierce\ClassFinder\ClassFinder;

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
        $classes = ClassFinder::getClassesInNamespace(config('pepper.namespace'));
        foreach ($classes as $model) {
            if (isset(class_implements($model)[HasEndpoint::class]) || array_key_exists(HasEndpoint::class, class_implements($model))) {
                $modelInstance = new $model;
                $rq->create($modelInstance->getQueryName() . 'Query', $modelInstance->getQueryName(), $modelInstance->getQueryDescription(), $model);
            }
        }
    }
}
