<?php

namespace Pepper\Helpers;

class ConfigHelper
{
    /** @var string */
    protected $config;

    public function __construct()
    {
        $this->config = config_path('graphql.php');
    }

    /**
     * Add a new key to types array.
     *
     * @param  string $key
     * @param  string $class
     * @return void
     */
    public function addType(string $key, string $class): void
    {
        if ($this->exists('graphql.types.' . $key)) {
            $pattern = '/[^\/]{2,}\s*["\']types["\']\s*=>\s*\[\s*/';
            $class = strval('App\GraphQL\Types\Pepper\\' . $class . '::class');
            $update = preg_replace($pattern, "$0 '$key' => $class,\n      ", file_get_contents($this->config));
            file_put_contents($this->config, $update);
        }
    }

    /**
     * Add a new key to query array.
     *
     * @param  string $key
     * @param  string $class
     * @return void
     */
    public function addQuery(string $key, string $class): void
    {
        if ($this->exists('graphql.schemas.default.query.' . $key)) {
            $pattern = '/\s*["\']schemas["\']\s*=>\s*\[\s*["\']default["\']\s*=>\s*\[\s*["\']query["\']\s*=>\s*\[\s*/';
            $class = strval('App\GraphQL\Queries\Pepper\\' . $class . '::class');
            $replace = "$0 '$key' => $class,\n              ";
            $update = preg_replace($pattern, $replace, file_get_contents($this->config));
            file_put_contents($this->config, $update);
        }
    }

    /**
     * Add a new key to mutation array.
     *
     * @param  string $key
     * @param  string $class
     * @return void
     */
    public function addMutation(string $key, string $class): void
    {
        if ($this->exists('graphql.schemas.default.mutation.' . $key)) {
            $pattern = '/(\s*["\']schemas["\']\s*=>\s*\[\s*["\']default["\']\s*=>\s*\[\s*["\']query["\']\s*=>\s*\[\s*[^"]+?(?=["\']mutation["\'])["\']mutation["\']\s*=>\s*\[\s*)/';
            $class = strval('App\GraphQL\Mutations\Pepper\\' . $class . '::class');
            $replace = "$0 '$key' => $class,\n              ";
            $update = preg_replace($pattern, $replace, file_get_contents($this->config));
            file_put_contents($this->config, $update);
        }
    }

    /**
     * Checks if config file exists and given key is not null.
     *
     * @param  string $key
     * @return bool
     */
    protected function exists($key): bool
    {
        return file_exists($this->config) && !is_null(config($key));
    }
}
