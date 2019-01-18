<?php

namespace Rq\Driver;

use Rq\Driver\Traits\Singleton;

/**
 * 待完善
 * Class Memcached
 * User: qyx
 * Date: 2018/12/18
 * Time: 下午2:27
 * @package src\Driver
 */
class Memcached
{
    const envFile = 'mc.php';
    const EXPIRE_TIME = 3600;
    const PREFIX = 'mc';
    static $configList = [];
    private $logHandle = '';
    use Singleton;

    private function __construct()
    {
        $this->logHandle = Log::getInstance();
    }

    /**
     * @return \Memcached|null
     */
    private function getConnect()
    {
        $config = self::getConfig();
        $memcache = new \Rq\Driver\Cache\Memcached();
        return $memcache->getConnect($config);
    }

    /**
     * @return mixed
     */
    protected function getConfig()
    {
        if (!isset(self::$configList[static::envFile])) {
            self::$configList[static::envFile] = Config::getConfig(static::envFile);
        }
        return self::$configList[static::envFile];
    }

    /**
     * @param $key
     * @param $data
     * @return bool
     */
    public function set($key, $data)
    {
        try {
            $args = func_get_args();
            $key = $this->getKey($key);
            $result = $this->getConnect()->set($key, $data, static::EXPIRE_TIME);
            if ($result === false) {
                $this->logError($args, 'mc.set_error');
            }
            return $result;
        } catch (\MemcachedException $e) {
            $this->logHandle->exceptionLog($e, 'mc.set_error');
            return false;
        }
    }

    /**
     * @param $key
     * @param callable|null $cacheCb
     * @param null $casToken
     * @return bool|mixed
     */
    public function get($key, callable $cacheCb = null, &$casToken = null)
    {
        try {
            $key = $this->getKey($key);
            if (func_num_args() == 1) {
                $data = $this->getConnect()->get($key);
            } else {
                //一直失败。。memcached -vv 开启调试。
                //http://php.net/manual/zh/memcached.get.php
                if (defined('\Memcached::GET_EXTENDED')) {
                    $flag = \Memcached::GET_EXTENDED;
                    $res = $this->getConnect()->get($key, $cacheCb, $flag);
                    $data = false;
                    if ($res) {
                        $casToken = $res['cas'];
                        $data = $res['value'];
                    }
                } else {
                    $data = $this->getConnect()->get($key, $cacheCb, $casToken);
                }

            }
            return $data;
        } catch (\MemcachedException $e) {
            $this->logHandle->exceptionLog($e, 'mc.get_error');
            return false;
        }
    }

    /**
     * @param $key
     * @return bool
     */
    public function delete($key)
    {
        try {
            $key = $this->getKey($key);
            $result = $this->getConnect()->delete($key);
            if ($result === false && self::getConnect()->getResultCode() != \Memcached::RES_NOTFOUND) {
                self::logError($key, 'mc.del_error');
            }
            return $result;
        } catch (\MemcachedException $e) {
            $this->logHandle->exceptionLog($e, 'mc.del_error');
            return false;
        }
    }

    /**
     * @param $casToken
     * @param $key
     * @param $data
     * @return bool
     */
    public function cas($casToken, $key, $data)
    {
        try {
            $args = func_get_args();
            $key = $this->getKey($key);
            $result = $this->getConnect()->cas($casToken, $key, $data, static::EXPIRE_TIME);
            if ($result === false) {
                $this->logError($args, 'mc.cas_error');
            }
            return $result;
        } catch (\MemcachedException $e) {
            $this->logHandle->exceptionLog($e, 'mc.cas_error');
            return false;
        }
    }

    /**
     * @param $key
     * @param $data
     * @return bool
     */
    public function add($key, $data)
    {
        try {
            $key = $this->getKey($key);
            $result = $this->getConnect()->add($key, $data, static::EXPIRE_TIME);
            return $result;
        } catch (\MemcachedException $e) {
            $this->logHandle->exceptionLog($e, 'mc.add_error');
            return false;
        }
    }

    /**
     * @param $data
     * @param $fileName
     */
    private function logError($data, $fileName)
    {
        $errorInfo = [
            'code' => $this->getConnect()->getResultCode(),
            'msg' => $this->getConnect()->getResultMessage(),
            'data' => $data
        ];
        $this->logHandle->errorLog($errorInfo, $fileName);
    }

    /**
     * @param $key
     * @return string
     */
    protected function getKey($key)
    {
        if (!empty(static::PREFIX)) {
            $key = static::PREFIX . ':' . $key;
        } else {
            $key = md5(get_called_class()) . ':' . $key;
        }
        return $key;
    }


}