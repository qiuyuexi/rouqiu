<?php

namespace Lib\Driver\Cache;

use Lib\Driver\Log;

/**
 * Class Memcached
 * User: qyx
 * Date: 2018/11/29
 * Time: 下午2:13
 * @package Lib\Driver\Cache
 */
class Memcached
{
    private static $connPoll;

    /**
     * @param $config
     * @return \Memcached|null
     */
    public function getConnect($config)
    {
        $key = md5(serialize($config));
        if (!isset(self::$connPoll[$key])) {
            self::$connPoll[$key] = $this->connect($config);
        }
        return self::$connPoll[$key];
    }

    /**
     * @param $config
     * @return \Memcached|null
     */
    private function connect($config)
    {
        try {
            $memcached = new \Memcached();
            $options = [
                \Memcached::OPT_DISTRIBUTION => \Memcached::DISTRIBUTION_CONSISTENT, //一致性分布算法(基于libketama).
                \Memcached::OPT_LIBKETAMA_COMPATIBLE => true,
                \Memcached::OPT_NO_BLOCK => true,
                \Memcached::OPT_CONNECT_TIMEOUT => 1000
            ];
            $memcached->setOptions($options);
            $memcached->addServers($config);
        } catch (\MemcachedException $e) {
            $memcached = null;
            Log::getInstance()->exceptionLog($e, 'mc_connect_error');
        }
        return $memcached;
    }
}