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

    public function bind($className, \Closure $closure)
    {
        self::$binds[$className] = $closure;
    }

    public function make($className)
    {
        if (isset(self::$instances[$className])) {
            return self::$instances[$className];
        }
        if (isset(self::$binds[$className])) {
            self::$instances[$className] = call_user_func(self::$binds[$className]);
            return self::$instances[$className];
        }
        return null;
    }
}