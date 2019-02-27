<?php

namespace Rq\Driver\Cache;

use Rq\Driver\Log;
use Rq\Driver\Traits\Singleton;

/**
 * Class Memcached
 * User: qyx
 * Date: 2018/11/29
 * Time: 下午2:13
 * @package src\Driver\Cache
 */
class Memcached
{
    private static $connPoll;

    use Singleton;

    /**
     * @param $config
     * @return \Memcached|null
     */
    public function getConnect(array $config)
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
    private function connect(array $config)
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
        } catch (\Exception $e) {
            $memcached = null;
            Log::getInstance()->exceptionLog($e, 'mc_connect_error');
        }
        return $memcached;
    }
}