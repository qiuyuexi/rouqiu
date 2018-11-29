<?php

namespace Lib\Common;

use Lib\Driver\Controller;
use Lib\Driver\Log;

class Init
{
    static $mode = 'dev';
    static $envDir = 'local';
    private static $root = '';

    public static function init()
    {
        self::setEnvironment();
        set_error_handler("Lib\Common\Init::errorHandle");
        set_exception_handler("Lib\Common\Init::exceptionHandler");
    }

    public static function dispatch($prefix, $pathInfo = '')
    {
        try {
            $pathInfo = $pathInfo ? $pathInfo : self::getPathInfo();
            $className = $prefix . '/Controller/' . $pathInfo;
            $className = ucwords($className, '/');
            $className = str_replace('/', '\\', $className);
            if (class_exists($className)) {

                if (method_exists($className, 'run')) {
                    $controller = new $className;
                    $controller->run();
                } else {
                    throw new \Exception('方法不存在', 500);
                }
            } else {
                $controller = new Controller();
                $controller->output(404);
            }
        } catch (\Exception $ex) {
            $controller = new Controller();
            $controller->output(500);

        }
    }

    /**
     * 设置环境变量 主要是当前环境 及配置文件路径
     */
    private static function setEnvironment()
    {
        $configPath = self::getRoot() . '/env/config.php';
        if (is_file($configPath)) {
            $config = require_once($configPath);
        }

        if (isset($config['mode'])) {
            self::$mode = $config['mode'];
        }
        if (isset($config[self::$mode])) {
            self::$envDir = $config[self::$mode];
        }
    }

    private static function getPathInfo()
    {
        $requestUri = $_SERVER['REQUEST_URI'];
        $requestUri = parse_url($requestUri);
        $pathInfo = $requestUri['path'];
        $pathInfo = trim($pathInfo, '/');
        $pathInfo = ucwords($pathInfo, '/');
        return $pathInfo;
    }

    /**
     * 获得配置文件目录
     * @return string
     */
    public static function getEnvPath()
    {
        return self::getRoot() . '/env/' . self::$envDir;
    }

    /**
     * 获取root目录路径
     * @return string
     */
    public static function getRoot()
    {
        if (empty(self::$root)) {
            $root = dirname(dirname(__DIR__));
            self::$root = $root;
        }
        return self::$root;
    }

    public static function errorHandle($errno, $errstr, $errfile, $errline)
    {
        $errInfo = [
            'error_msg' => $errstr,
            'error_file' => $errfile,
            'error_line' => $errline
        ];
        Log::getInstance()->errorLog($errInfo, $errno);
    }


    public static function exceptionHandler($exception)
    {
        Log::getInstance()->exceptionLog($exception, 'uncatch_exception');
    }
}