<?php

namespace Rq\Common;

use Rq\Driver\Container;
use Rq\Driver\Controller;
use Rq\Driver\Log;

/**
 * Class Init
 * User: qyx
 * Date: 2018/12/10
 * Time: 下午5:36
 * @package src\Common
 */
class Init
{
    static $mode = 'dev';
    static $envDir = 'local';
    private static $root = '';
    private static $includeFiles = [];
    static $execTime = 0;

    /**
     * @param string $rootPath
     */
    public static function init($rootPath = '')
    {
        self::containRegister();
        self::setRoot($rootPath);
        set_error_handler("Rq\Common\Init::errorHandle");
        set_exception_handler("Rq\Common\Init::exceptionHandler");
        spl_autoload_register("Rq\Common\Init::autoload");
        self::setEnvironment();
    }

    /**
     * @param $prefix
     * @param string $pathInfo
     * @return int|mixed
     */
    public static function dispatch($prefix, $pathInfo = '')
    {
        $startExecTime = microtime(true);
        try {
            $pathInfo = $pathInfo ? $pathInfo : self::getPathInfo();
            $className = $prefix . '/Controller/' . $pathInfo;
            $className = ucwords($className, '/');
            $className = str_replace('/', '\\', $className);
            if (class_exists($className)) {
                if (method_exists($className, 'run')) {
                    $controller = self::getClass($className);
                    $controller->run();
                } else {
                    throw new \Exception('方法不存在', 500);
                }
            } else {
                $controller = new Controller();
                $controller->output(404);
            }
        } catch (\Throwable $ex) {
            Log::getInstance()->exceptionLog($ex, 'system_error');
            $controller = new Controller();
            $controller->output(500);
        }
        self::$execTime = microtime(true) - $startExecTime;
        return self::$execTime;
    }

    /**
     * 设置环境变量 主要是当前环境 及配置文件路径
     */
    private static function setEnvironment()
    {
        $configPath = self::getRoot() . '/env/config.php';
        $config = [];
        if (is_file($configPath)) {
            $config = include($configPath);
        }

        if (isset($config['mode'])) {
            self::$mode = $config['mode'];
        }
        if (isset($config[self::$mode])) {
            self::$envDir = $config[self::$mode];
        }
    }

    /**
     * @return string
     */
    private static function getPathInfo()
    {
        if (PHP_SAPI == 'cli') {
            $uri = getopt('', ['uri:']);
            $pathInfo = isset($uri['uri']) ? $uri['uri'] : '';
            $pathInfo = trim($pathInfo, '/');
            $pathInfo = ucwords($pathInfo, '/');
        } else {
            $requestUri = $_SERVER['REQUEST_URI'];
            $requestUri = parse_url($requestUri);
            $pathInfo = $requestUri['path'];
            $pathInfo = trim($pathInfo, '/');
            $pathInfo = ucwords($pathInfo, '/');
        }
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

    public static function setRoot($rootPath)
    {
        self::$root = $rootPath;
    }

    /**
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     */
    public static function errorHandle($errno, $errstr, $errfile, $errline)
    {
        $errInfo = [
            'error_msg' => $errstr,
            'error_file' => $errfile,
            'error_line' => $errline
        ];
        Log::getInstance()->errorLog($errInfo, $errno);
    }

    /**
     * 异常捕获
     * @param $exception
     */
    public static function exceptionHandler($exception)
    {
        Log::getInstance()->exceptionLog($exception, 'uncatch_exception');
    }

    /**
     * 自动加载
     * @param $className
     */
    public static function autoload($className)
    {
        $path = self::getRoot();
        $className = str_replace('\\', '/', $className);
        $className = $path . '/' . $className . '.php';
        if (is_file($className) && !in_array($className, self::$includeFiles)) {
            require($className);
            self::$includeFiles[] = $className;
        }
    }

    public static function containRegister()
    {
        Container::getInstance('init')->bind('log', function () {
            return Log::getInstance();
        });
    }

    /**
     * @param $className
     * @return mixed
     * @throws \ReflectionException
     */
    public static function getClass($className)
    {
        if (!class_exists($className)) {
            throw new \Exception($className . '不存在', 500);
        }
        $reflection = new \ReflectionClass($className);
        if (is_null($reflection->getConstructor())) {
            return new $className();
        }
        $params = $reflection->getConstructor()->getParameters();
        $paramsClass = [];
        foreach ($params AS $param) {
            $class = $param->getClass()->name;
            if ($param->isDefaultValueAvailable()) {
                $paramsClass[] = $param->getDefaultValue();
                continue;
            }
            //非闭包 非类
            if (is_null($class)) {
                throw new \Exception($className . '参数异常:' . $param->getName(), 500);
            }
            //闭包
            if ($class == 'Closure') {
                throw new \Exception($className . '参数异常:' . $param->getName(), 500);
            }
            //普通类
            if (class_exists($class)) {
                $paramsClass[] = self::getClass($class);
            } else {
                throw new \Exception($class . '不存在', 500);
            }
        }
        return new $className(...$paramsClass);
    }
}