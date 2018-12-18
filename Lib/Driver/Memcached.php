<?php

namespace Lib\Driver;

/**
 * 待完善
 * Class Memcached
 * User: qyx
 * Date: 2018/12/18
 * Time: 下午2:27
 * @package Lib\Driver
 */
class Memcached
{
    const envFile = 'mc.php';
    const EXPIRE_TIME = 3600;
    const PREFIX = 'mc';
    static $configList = [];

    /**
     * @return \Memcached|null
     */
    private static function getConnect()
    {
        $config = self::getConfig();
        $memcache = new \Lib\Driver\Cache\Memcached();
        return $memcache->getConnect($config);
    }

    /**
     * @return mixed
     */
    protected static function getConfig()
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
    public static function set($key, $data)
    {
        try {
            $args = func_get_args();
            $key = self::getKey($key);
            $result = self::getConnect()->set($key, $data, static::EXPIRE_TIME);
            if ($result === false) {
                self::logError($args, 'mc.set_error');
            }
            return $result;
        } catch (\MemcachedException $e) {
            Log::getInstance()->exceptionLog($e, 'mc.set_error');
            return false;
        }
    }

    /**
     * @param $key
     * @param callable|null $cacheCb
     * @param null $casToken
     * @return bool|mixed
     */
    public static function get($key, callable $cacheCb = null, &$casToken = null)
    {
        try {
            $key = self::getKey($key);
            if (func_num_args() == 1) {
                $data = self::getConnect()->get($key);
            } else {
                //一直失败。。memcached -vv 开启调试。
                //http://php.net/manual/zh/memcached.get.php
                if(defined('\Memcached::GET_EXTENDED')){
                    $flag = \Memcached::GET_EXTENDED;
                    $res = self::getConnect()->get($key, $cacheCb, $flag);
                    $data = false;
                    if($res){
                        $casToken = $res['cas'];
                        $data = $res['value'];
                    }
                }else{
                    $data = self::getConnect()->get($key, $cacheCb, $casToken);
                }

            }
            return $data;
        } catch (\MemcachedException $e) {
            Log::getInstance()->exceptionLog($e, 'mc.get_error');
            return false;
        }
    }

    /**
     * @param $key
     * @return bool
     */
    public static function delete($key)
    {
        try {
            $key = self::getKey($key);
            $result = self::getConnect()->delete($key);
            if ($result === false && self::getConnect()->getResultCode() != \Memcached::RES_NOTFOUND) {
                self::logError($key, 'mc.del_error');
            }
            return $result;
        } catch (\MemcachedException $e) {
            Log::getInstance()->exceptionLog($e, 'mc.del_error');
            return false;
        }
    }

    /**
     * @param $casToken
     * @param $key
     * @param $data
     * @return bool
     */
    public static function cas($casToken, $key, $data)
    {
        try {
            $args = func_get_args();
            $key = self::getKey($key);
            $result = self::getConnect()->cas($casToken, $key, $data, static::EXPIRE_TIME);
            if ($result === false) {
                self::logError($args, 'mc.cas_error');
            }
            return $result;
        } catch (\MemcachedException $e) {
            Log::getInstance()->exceptionLog($e, 'mc.cas_error');
            return false;
        }
    }

    /**
     * @param $key
     * @param $data
     * @return bool
     */
    public static function add($key, $data)
    {
        try {
            $key = self::getKey($key);
            $result = self::getConnect()->add($key, $data, static::EXPIRE_TIME);
            return $result;
        } catch (\MemcachedException $e) {
            Log::getInstance()->exceptionLog($e, 'mc.add_error');
            return false;
        }
    }

    /**
     * @param $data
     * @param $fileName
     */
    private static function logError($data, $fileName)
    {
        $errorInfo = [
            'code' => self::getConnect()->getResultCode(),
            'msg' => self::getConnect()->getResultMessage(),
            'data' => $data
        ];
        Log::getInstance()->errorLog($errorInfo, $fileName);
    }

    /**
     * @param $key
     * @return string
     */
    private static function getKey($key)
    {
        if (!empty(static::PREFIX)) {
            $key = static::PREFIX . ':' . $key;
        } else {
            $key = md5(get_called_class()) . ':' . $key;
        }
        return $key;
    }


}