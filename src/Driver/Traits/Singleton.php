<?php

namespace Rq\Driver\Traits;

trait Singleton
{
    private static $instance;

    private function __construct()
    {
        ;
    }

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