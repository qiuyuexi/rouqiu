<?php

namespace Rq\Driver;

use Rq\Common\Init;

/**
 * 配置读取，待优化
 * Class Config
 * User: qyx
 * Date: 2018/12/18
 * Time: 上午11:39
 * @package src\Driver
 */
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
