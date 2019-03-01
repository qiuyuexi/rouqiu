<?php

namespace Rq\Driver;

use Rq\Driver\Cache\Cache;
use Rq\Driver\Traits\Singleton;

/**
 * 暂时只列出最简单get set。。。
 * Class Redis
 * User: qyx
 * Date: 2019/2/27
 * Time: 下午5:21
 * @package Rq\Driver
 */
class Redis
{
    const MASTER = 1;
    const SLAVE = 0;
    const envFile = 'redis.php';
    const EXPIRE_TIME = 3600;
    const PREFIX = 'redis';
    static $configList = [];
    private $logHandle = '';
    use Singleton;

    private function __construct(\Rq\Driver\Log\Log $logDriver = null)
    {
        if(is_null($logDriver)){
            $logDriver = Log::class;
        }
        if (method_exists($logDriver, 'getInstance')) {
            $this->logHandle = $logDriver::getInstance();
        } else {
            $this->logHandle = new $logDriver();
        }
    }

    /**
     * @return \Redis
     */
    protected function getConnect($isMaster = true)
    {
        $config = self::getConfig($isMaster);
        $redis = Cache::getCache(Cache::CACHE_TYPE_REDIS);
        return $redis->getConnect($config);
    }

    /**
     * @param bool $isMaster
     * @return array
     */
    protected function getConfig($isMaster = true)
    {
        if (!isset(self::$configList[static::envFile])) {
            self::$configList[static::envFile] = Config::getConfig(static::envFile);
        }
        $config = self::$configList[static::envFile];
        if ($isMaster && isset($config['master'])) {
            return $config['master'];
        } else if (isset($config['slaves'])) {
            $slaveConfig = $config['slaves'];
            $randKey = array_rand($slaveConfig);
            if (is_numeric($randKey)) {
                return $slaveConfig[$randKey];
            }
            return [];
        }
        return $config;
    }

    /**
     * @param $key
     * @param $value
     * @param int $ttl
     * @return bool
     */
    public function set($key, $value, $ttl = 0)
    {
        try {
            $key = $this->getKey($key);
            $ttl = $ttl ? $ttl : static::EXPIRE_TIME;
            $result = $this->getConnect()->set($key, $value, $ttl);
            return $result;
        } catch (\Exception $e) {
            $this->logHandle->exceptionLog($e, __METHOD__);
            return false;
        }
    }

    /**
     * @param $key
     * @param $ttl
     * @param $value
     * @return bool
     */
    public function setex($key, $ttl, $value)
    {
        try {
            $key = $this->getKey($key);
            $result = $this->getConnect()->setex($key, $ttl, $value);
            return $result;
        } catch (\Exception $e) {
            $this->logHandle->exceptionLog($e, __METHOD__);
            return false;
        }
    }

    /**
     * @param $key
     * @param $value
     * @return bool
     */
    public function setnx($key, $value)
    {
        try {
            $key = $this->getKey($key);
            $result = $this->getConnect()->setnx($key, $value);
            return $result;
        } catch (\Exception $e) {
            $this->logHandle->exceptionLog($e, __METHOD__);
            return false;
        }
    }

    /**
     * @param $key
     * @return bool|string
     */
    public function get($key)
    {
        try {
            $key = $this->getKey($key);
            $result = $this->getConnect(self::SLAVE)->get($key);
            return $result;
        } catch (\Exception $e) {
            $this->logHandle->exceptionLog($e, __METHOD__);
            return false;
        }

    }

    /**
     * @param $key
     * @return bool|int
     */
    public function delete($key)
    {
        try {
            $key = $this->getKey($key);
            $result = $this->getConnect()->delete($key);
            return $result;
        } catch (\Exception $e) {
            $this->logHandle->exceptionLog($e, __METHOD__);
            return false;
        }
    }

    /**
     * @param $key
     * @return string
     */
    public function getKey($key)
    {
        if (!empty(static::PREFIX)) {
            $key = static::PREFIX . ':' . $key;
        } else {
            $key = md5(get_called_class()) . ':' . $key;
        }
        return $key;
    }

    /**
     * @param $name
     * @param $arguments
     * @return bool|mixed
     */
    public function __call($name, $arguments)
    {
        try {
            $redis = $this->getConnect();
            if (!method_exists($redis, $name)) {
                return false;
            }
            $result = call_user_func_array([$redis, $name], $arguments);
            return $result;
        } catch (\Exception $e) {
            $this->logHandle->exceptionLog($e, __METHOD__);
            return false;
        }
    }
}