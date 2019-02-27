<?php

namespace Rq\Driver\Cache;

use Rq\Driver\Log;
use Rq\Driver\Traits\Singleton;

/**
 * Class Redis
 * User: qyx
 * Date: 2018/11/21
 * Time: 下午7:44
 * @package src\Driver
 */
class Redis
{

    private static $connPoll;

    use Singleton;

    /**
     * @param array $config
     * @return mixed
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
     * @param array $config
     * @return null|\Redis
     */
    private function connect(array $config)
    {
        try {
            $redis = new \Redis();
            $host = isset($config['host']) ? $config['host'] : '';
            $port = isset($config['port']) ? $config['port'] : 6379;
            $timeout = isset($config['timeout']) ? $config['timeout'] : 5;
            $pconnect = isset($config['pconnect']) ? $config['pconnect'] : false;
            $password = isset($config['password']) ? $config['password'] : '';
            if(empty($host)){
                throw new \RedisException("host is null",500);
            }
            if ($pconnect) {
                $connectResult = $redis->pconnect($host, $port, $timeout);
            } else {
                $connectResult = $redis->connect($host, $port, $timeout);
            }
            if (!$connectResult) {
                Log::getInstance()->errorLog($redis->getLastError(), __METHOD__);
            }
            if ($connectResult && $password) {
                $redis->auth($password);
            }
            return $redis;
        } catch (\Exception $e) {
            $redis = null;
            Log::getInstance()->exceptionLog($e, 'mc_connect_error');
        }
        return $redis;
    }
}
