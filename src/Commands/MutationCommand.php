<?php

namespace Amirmasoud\Pepper\Commands;

use Amirmasoud\Pepper\HasEndpoint;
use App;
use Amirmasoud\Pepper\Helpers\ResourceMutationCreator;
use Illuminate\Filesystem\Filesystem;
use HaydenPierce\ClassFinder\ClassFinder;

class MutationCommand extends BaseCommand
{
    /** @var string */
    protected $signature = 'pepper:mutations';

    /** @var string */
    protected $description = 'Generate GraphQL mutations.';

    public function handle()
    {
        $fs = new Filesystem();
        $rq = new ResourceMutationCreator($fs);
        $classes = ClassFinder::getClassesInNamespace(config('pepper.namespace'));
        foreach ($classes as $model) {
            if (isset(class_implements($model)[HasEndpoint::class]) || array_key_exists(HasEndpoint::class, class_implements($model))) {
                $modelInstance = new $model;
                $rq->create($modelInstance->getQueryName() . 'Mutation', $modelInstance->getQueryName(), $modelInstance->getQueryDescription(), $model);
            }
        }
    }
}
