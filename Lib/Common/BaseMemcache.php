<?php

namespace Lib\Common;

use Lib\Driver\Config;
use Lib\Driver\Log;
use Lib\Driver\Memcache;

class BaseMemcache
{
    const envFile = 'mc.php';
    const EXPIRE_TIME = '3600';
    const PREFIX = '';
    static $configList = [];

    private static function getConnect()
    {
        $config = self::getConfig();
        $memcache = new Memcache();
        return $memcache->getConnect($config);
    }

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
        } catch (\Exception $e) {
            Log::errorLog($e->getMessage(), 'mc.set_error');
            return false;
        }
    }


    public static function get($key)
    {
        try {
            $key = self::getKey($key);
            $data = self::getConnect()->get($key);
            return $data;
        } catch (\Exception $e) {
            Log::errorLog($e->getMessage(), 'mc.get_error');
            return false;
        }
    }

    public static function delete($key)
    {
        try {
            $key = self::getKey($key);
            $result = self::getConnect()->delete($key);
            if ($result === false && self::getConnect()->getResultCode() != \Memcached::RES_NOTFOUND) {
                self::logError($key, 'mc.del_error');
            }
            return $result;
        } catch (\Exception $e) {
            Log::errorLog($e->getMessage(), 'mc.del_error');
            return false;
        }
    }

    private static function logError($data, $fileName)
    {
        $errorInfo = [
            'code' => self::getConnect()->getResultCode(),
            'msg' => self::getConnect()->getResultMessage(),
            'data' => $data
        ];
        Log::errorLog($errorInfo, $fileName);
    }

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