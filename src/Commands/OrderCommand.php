<?php

namespace Pepper\Commands;

use Pepper\HasEndpoint;
use Pepper\Helpers\ResourceOrderCreator;
use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Filesystem\Filesystem;

class OrderCommand extends BaseCommand
{
    /** @var string */
    protected $signature = 'pepper:orders';

    /** @var string */
    protected $description = 'Generate GraphQL orders.';

    public function handle()
    {
        $fs = new Filesystem();
        $rq = new ResourceOrderCreator($fs);

        $classes = ClassFinder::getClassesInNamespace(config('pepper.namespace'));
        foreach ($classes as $model) {
            if (isset(class_implements($model)[HasEndpoint::class]) || array_key_exists(HasEndpoint::class, class_implements($model))) {
                $modelInstance = new $model;
                $rq->create($modelInstance->getTypeName() . 'Order', $modelInstance->getTypeName(), $modelInstance->getDescription(), $model);
            }
        }
    }
}
