<?php

namespace Lib\Driver\Traits;

trait Singleton
{
    private static $instance;

    /**
     * @return static
     */
    public static function getInstance()
    {
        $class = get_called_class();
        $args = func_get_args();
        $key = md5($class . serialize($args));
        if (!isset(self::$instance[$key])) {
            self::$instance[$key] = new $class(...$args);
        }
        return self::$instance[$key];
    }
}