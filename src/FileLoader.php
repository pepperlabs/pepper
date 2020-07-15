<?php

namespace Amirmasoud\Pepper;

use Illuminate\Config\Repository;
use Illuminate\Support\Facades\File;

class FileLoader extends Repository
{
    public function save($file)
    {
        $path = config_path($file);

        if (is_null($path)) {
            return;
        }

        File::put($path, '<?php return ' . var_export(config('graphql'), true) . ';');
    }
}
