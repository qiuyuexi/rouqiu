<?php

namespace Lib\Driver;

class Memcache
{
    private static $connPoll;

    /**
     * @param $config
     * @return mixed
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
     * @return \Memcached
     */
    private function connect($config)
    {
        $memcached = new \Memcached();
        $options = [
            \Memcached::OPT_DISTRIBUTION => \Memcached::DISTRIBUTION_CONSISTENT,
            \Memcached::OPT_LIBKETAMA_COMPATIBLE => true,
            \Memcached::OPT_NO_BLOCK => true,
            \Memcached::OPT_CONNECT_TIMEOUT => 1000
        ];
        $memcached->setOptions($options);
        $memcached->addServers($config);
        return $memcached;
    }
}