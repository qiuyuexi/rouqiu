<?php

namespace Lib\Driver;

use Lib\Common\Init;

class Config
{
    private static $config = [];

    public static function getConfig($envFile, $key = '')
    {
        $envFile = Init::getEnvPath() . '/' . $envFile;
        $config = isset(self::$config[$envFile]) ? self::$config[$envFile] : [];
        if (empty($config) && is_file($envFile)) {
            $config = include($envFile);
            self::$config[$envFile] = $config;
        }

        if (isset($config[$key])) {
            return $config[$key];
        }

        return $config;
    }
}
