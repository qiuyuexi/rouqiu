<?php

namespace Rq\Driver;

use Rq\Driver\Traits\Singleton;

/**
 * 简易版
 * Class Container
 * User: qyx
 * Date: 2018/12/20
 * Time: 上午10:54
 * @package src\Driver
 */
class Container
{
    use Singleton;
    private static $instances = [];
    private static $binds = [];
    private static $belong = '';

    private function __construct($belong = '')
    {
        self::$belong = $belong;
    }

    /**
     * @param $className
     * @param \Closure $closure
     */
    public function bind($className, \Closure $closure)
    {
        if (is_callable($closure)) {
            self::$binds[$className] = $closure;
            $this->make($className);
        }
    }

    /**
     * @param $className
     */
    private function make($className)
    {
        if (!isset(self::$instances[$className]) && isset(self::$binds[$className])) {
            self::$instances[$className] = call_user_func(self::$binds[$className]);
        }
    }

    /**
     * @param $className
     * @return mixed
     */
    public function instance($className)
    {
        if (isset(self::$instances[$className])) {
            return self::$instances[$className];
        }
    }
}