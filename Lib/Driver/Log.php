<?php

namespace Lib\Driver;

use Lib\Common\Init;
use Lib\Config\LogConfig;

class Log
{
    private static $errorLog = 'error.log';
    private static $infoLog = 'info.log';
    private static $debug = 'debug.log';
    private static $logDir = '';

    public static function errorLog($info, $infoName = '')
    {
        self::log($info, $infoName, self::$errorLog);
    }

    public static function infoLog($info, $infoName = '')
    {
        self::log($info, $infoName, self::$infoLog);
    }

    public static function debugLog($info, $infoName = '')
    {
        self::log($info, $infoName, self::$debug);
    }

    private static function log($info, $infoName = '', $logType)
    {
        $tpl = [
            'date' => date('Y-m-d', time()),
            'message' => $info,
            'info_name' => $infoName
        ];
        $tpl = json_encode($tpl) . PHP_EOL;
        $file = self::getLogDir() . '/' . $logType;
        file_put_contents($file, $tpl, FILE_APPEND);
    }

    /**
     * 获取日志目录
     * @return array|mixed|string
     */
    private static function getLogDir()
    {
        self::$logDir = self::$logDir ?: LogConfig::getConfig('dir');
        if (empty(self::$logDir)) {
            error_log('log_config_empty');
            self::$logDir = Init::getRoot() . '/log';
            return self::$logDir;
        }
        if (!is_dir(self::$logDir)) {
            mkdir(self::$logDir, 0755, true);
        }
        return self::$logDir;
    }
}