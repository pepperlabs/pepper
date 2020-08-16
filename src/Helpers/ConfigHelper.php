<?php

namespace Pepper\Helpers;

class ConfigHelper
{
    protected $config;

    public function __construct()
    {
        $this->config = config_path('graphql.php');
    }

    protected function updateType($key, $class)
    {
        $pattern = '/[^\/]{2,}\s*["\']types["\']\s*=>\s*\[\s*/';

        if ($this->exists('graphql.types.' . $key)) {
            $class = strval('App\GraphQL\Types\Pepper\\' . $class . '::class');
            $update = preg_replace($pattern, "$0 '$key' => $class,\n      ", file_get_contents($this->config));
            file_put_contents($this->config, $update);
        }
    }

    protected function updateQuery($key, $class)
    {
        $pattern = '/\s*["\']schemas["\']\s*=>\s*\[\s*["\']default["\']\s*=>\s*\[\s*["\']query["\']\s*=>\s*\[\s*/';

        if ($this->exists('graphql.schemas.default.query.' . $key)) {
            $class = strval('App\GraphQL\Queries\Pepper\\' . $class . '::class');
            $replace = "$0 '$key' => $class,\n              ";
            $update = preg_replace($pattern, $replace, file_get_contents($this->config));
            file_put_contents($this->config, $update);
        }
    }

    protected function updateMutation($key, $class)
    {
        $pattern = '/(\s*["\']schemas["\']\s*=>\s*\[\s*["\']default["\']\s*=>\s*\[\s*["\']query["\']\s*=>\s*\[\s*[^"]+?(?=["\']mutation["\'])["\']mutation["\']\s*=>\s*\[\s*)/';

        if ($this->exists('graphql.schemas.default.mutation.' . $key)) {
            $class = strval('App\GraphQL\Mutations\Pepper\\' . $class . '::class');
            $replace = "$0 '$key' => $class,\n              ";
            $update = preg_replace($pattern, $replace, file_get_contents($this->config));
            file_put_contents($this->config, $update);
        }
    }

    protected function exists($key)
    {
        return file_exists($this->config) && !is_null(config($key));
    }
}
