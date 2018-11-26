<?php

namespace Lib\Driver;

use Lib\Common\Init;

class Config
{

    public static function getConfig($envFile, $key = '')
    {
        $config = [];
        $envFile = Init::getEnvPath() . '/' . $envFile;

        if (is_file($envFile)) {
            $config = include($envFile);
        }

        if (isset($config[$key])) {
            return $config[$key];
        }

        return $config;
    }
}
