<?php

namespace Amirmasoud\Pepper\Commands;

class MetadataCommand extends BaseCommand
{
    /** @var string */
    protected $signature = 'pepper:metadata';

    /** @var string */
    protected $description = 'Generate GraphQL metadata.';

    public function handle()
    {
    }
}
