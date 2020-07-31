<?php

namespace Amirmasoud\Pepper\Commands;

use Amirmasoud\Pepper\HasEndpoint;
use Amirmasoud\Pepper\Helpers\ResourceInputCreator;
use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Filesystem\Filesystem;

class InputCommand extends BaseCommand
{
    /** @var string */
    protected $signature = 'pepper:inputs';

    /** @var string */
    protected $description = 'Generate GraphQL inputs.';

    public function handle()
    {
        $fs = new Filesystem();
        $rq = new ResourceInputCreator($fs);

        $classes = ClassFinder::getClassesInNamespace(config('pepper.namespace'));
        foreach ($classes as $model) {
            if (isset(class_implements($model)[HasEndpoint::class]) || array_key_exists(HasEndpoint::class, class_implements($model))) {
                $modelInstance = new $model;
                $rq->create($modelInstance->getTypeName() . 'Input', $modelInstance->getTypeName(), $modelInstance->getDescription(), $model);
            }
        }
    }
}
