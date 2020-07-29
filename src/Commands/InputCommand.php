<?php

namespace Amirmasoud\Pepper\Commands;

use Amirmasoud\Pepper\HasEndpoint;
use Amirmasoud\Pepper\Helpers\ResourceInputCreator;
use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Filesystem\Filesystem;

class InputCommand extends BaseCommand
{
    /** @var string */
    protected $signature = 'pepper:types';

    /** @var string */
    protected $description = 'Generate GraphQL types.';

    public function handle()
    {
        $fs = new Filesystem();
        $rq = new ResourceTypeCreator($fs);

        $classes = ClassFinder::getClassesInNamespace(config('pepper.namespace'));
        foreach ($classes as $model) {
            if (isset(class_implements($model)[HasEndpoint::class])) {
                $modelInstance = new $model;
                $rq->create($modelInstance->getTypeName() . 'Type', $modelInstance->getTypeName(), $modelInstance->getDescription(), $model);
            }
        }
    }
}
