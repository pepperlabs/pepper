<?php

namespace Pepper\Helpers;

class ConfigHelper
{
    protected $config;

    public function __construct()
    {
        $this->config = config_path('graphql.php');
    }

    protected function updateType($name)
    {
    }

    protected function updateQuery($key, $class)
    {
    }

    protected function updateMutation($name)
    {
    }

    protected function exists($key)
    {
        return file_exists($this->config) && !is_null(config($key));
    }
}
