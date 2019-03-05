<?php

namespace Rq\Driver\Traits;

/**
 * 单例模式
 * Trait Singleton
 * @package Rq\Driver\Traits
 */
trait Singleton
{
    private static $instance;

    /**
     * 单例模式 构造函数为private
     * Singleton constructor.
     */
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