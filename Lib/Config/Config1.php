<?php

namespace Lib\Config;

use Lib\Common\Init;

class Config
{
    static $envFile = '';

    public static function getConfig($key)
    {
        $config = [];
        $envFile = Init::getEnvPath() . '/' . static::$envFile;

        if (is_file($envFile)) {
            $config = include($envFile);
        }

        if (isset($config[$key])) {
            return $config[$key];
        }

        return $config;
    }
}
