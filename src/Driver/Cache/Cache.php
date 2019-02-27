<?php

namespace Rq\Driver\Cache;

class Cache
{
    const CACHE_TYPE_MEMCACHED = 'memcached';
    const CACHE_TYPE_REDIS = 'redis';
    const CACHE_TYPE_APCU = 'apcu';//仅作为本地缓存

    /**
     * @param $type
     * @return Memcached|Redis|string
     */
    public static function getCache($type)
    {
        $cache = '';
        switch ($type) {
            case self::CACHE_TYPE_MEMCACHED :
                $cache = Memcached::getInstance();
                break;
            case self::CACHE_TYPE_REDIS:
                $cache = Redis::getInstance();
                break;
        }
        return $cache;
    }
}