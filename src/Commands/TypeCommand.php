<?php

namespace Pepper\Commands;

use Pepper\HasEndpoint;
use Pepper\Helpers\ResourceQueryCreator;
use Pepper\Helpers\ResourceTypeCreator;
use HaydenPierce\ClassFinder\ClassFinder;
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

        $classes = ClassFinder::getClassesInNamespace(config('pepper.namespace'));
        foreach ($classes as $model) {
            if (isset(class_implements($model)[HasEndpoint::class]) || array_key_exists(HasEndpoint::class, class_implements($model))) {
                $modelInstance = new $model;
                $rq->create($modelInstance->getTypeName() . 'Type', $modelInstance->getTypeName(), $modelInstance->getDescription(), $model);
            }
        }
    }
}
