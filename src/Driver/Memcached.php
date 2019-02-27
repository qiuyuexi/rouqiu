<?php

namespace Rq\Driver;

use Rq\Driver\Cache\Cache;
use Rq\Driver\Traits\Singleton;

/**
 * Memcached 基类
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
    protected function getConnect()
    {
        $config = self::getConfig();
        $memcache = Cache::getCache(Cache::CACHE_TYPE_MEMCACHED);
        return $memcache->getConnect($config);
    }

    /**
     * 读取mc配置
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
                $this->logError($args, __METHOD__);
            }
            return $result;
        } catch (\Exception $e) {
            $this->logHandle->exceptionLog($e, __METHOD__);
            return false;
        }
    }

    /**
     * @param array $itmes
     * @return bool
     */
    public function setMulti(array $itmes)
    {
        try {
            $args = func_get_args();
            $newItems = [];
            foreach ($itmes as $key => $value) {
                $newKey = $this->getKey($key);
                $newItems[$newKey] = $value;
            }
            $result = $this->getConnect()->setMulti($newItems, static::EXPIRE_TIME);
            if ($result === false) {
                $this->logError($args, __METHOD__);
            }
            return $result;
        } catch (\Exception $e) {
            $this->logHandle->exceptionLog($e, __METHOD__);
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
        } catch (\Exception $e) {
            $this->logHandle->exceptionLog($e, __METHOD__);
            return false;
        }
    }

    public function getMulti(array $keys, array &$casTokens = null, $flags = null)
    {
        try {
            array_walk_recursive($keys, function (&$item, $key) {
                $item = $this->getKey($item);
            });

            if (func_num_args() == 1) {
                $data = $this->getConnect()->getMulti($keys);
            } else {
                //一直失败。。memcached -vv 开启调试。
                //http://php.net/manual/zh/memcached.get.php
                if (defined('\Memcached::GET_EXTENDED')) {
                    $flags = \Memcached::GET_EXTENDED;
                    $result = $this->getConnect()->getMulti($keys, $flags);
                    $data = [];
                    foreach ($result as $key => $value) {
                        $data[$key] = $value['value'];
                        $casTokens[$key] = $value['cas'];
                    }
                } else {
                    $data = $this->getConnect()->getMulti($keys, $casTokens);
                }
            }
            return $data;
        } catch (\Exception $e) {
            $this->logHandle->exceptionLog($e, __METHOD__);
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
                self::logError($key, __METHOD__);
            }
            return $result;
        } catch (\Exception $e) {
            $this->logHandle->exceptionLog($e, __METHOD__);
            return false;
        }
    }

    /**
     * @param array $keys
     * @param int $time
     * @return bool
     */
    public function deleteMulti(array $keys, $time = 0)
    {
        try {
            array_walk_recursive($keys, function (&$item, $key) {
                $item = $this->getKey($item);
            });
            $result = $this->getConnect()->deleteMulti($keys, $time);
            if ($result === false && self::getConnect()->getResultCode() != \Memcached::RES_NOTFOUND) {
                self::logError($keys, __METHOD__);
            }
            return $result;
        } catch (\Exception $e) {
            $this->logHandle->exceptionLog($e, __METHOD__);
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
                $this->logError($args, __METHOD__);
            }
            return $result;
        } catch (\Exception $e) {
            $this->logHandle->exceptionLog($e, __METHOD__);
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
            $this->logHandle->exceptionLog($e, __METHOD__);
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
     * $initial_value 需要开启二进制协议，处于考虑，先查询，在执行相应的操作。
     * @param $key
     * @param int $offset
     * @return bool|int
     */
    public function increment($key, $offset = 1)
    {
        try {
            if ($this->get($key) === false) {
                $result = $this->set($key, $offset);
                if ($result) {
                    $result = $offset;
                }
            } else {
                $key = $this->getKey($key);
                $result = $this->getConnect()->increment($key, $offset);
            }
            if ($result === false) {
                $this->logError($key, __METHOD__);
            }
            return $result;
        } catch (\Exception $e) {
            $this->logHandle->exceptionLog($e, __METHOD__);
            return false;
        }
    }

    /**
     * @param $key
     * @param int $offset
     * @return bool|int
     */
    public function decrement($key, $offset = 1)
    {
        try {
            if ($this->get($key) === false) {
                $result = $this->set($key, 0);
                if ($result) {
                    $result = 0;
                }
            } else {
                $key = $this->getKey($key);
                $result = $this->getConnect()->decrement($key, $offset);
            }
            if ($result === false) {
                $this->logError($key, __METHOD__);
            }
            return $result;
        } catch (\Exception $e) {
            $this->logHandle->exceptionLog($e, __METHOD__);
            return false;
        }
    }

    /**
     * @param $name
     * @param $arguments
     * @return bool|mixed
     */
    public function __call($name, $arguments)
    {
        try {
            $mc = $this->getConnect();

            if (!method_exists($mc, $name)) {
                $this->logHandle->errorLog([$name, $arguments], 'mc method not exist');
                return false;
            }
            $result = call_user_func_array([$mc, $name], $arguments);
            if ($result === false) {
                $this->logError($arguments, __METHOD__);
            }
            return $result;
        } catch (\Exception $e) {
            $this->logHandle->exceptionLog($e, __METHOD__);
            return false;
        }
    }

}