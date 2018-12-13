<?php

namespace Lib\Driver\Database;

use Lib\Driver\Log;

/**
 * Class Database
 * User: qyx
 * Date: 2018/11/28
 * Time: 下午7:09
 * @package Lib\Driver\Database
 */
class Mysql
{
    private static $pool;

    /**
     * @param $config
     * @return null|\PDO
     * @throws \Exception
     */
    private static function connet($config)
    {
        $dsn = "mysql:dbname=%s;host=%s";
        $dsn = sprintf($dsn, $config['dbname'], $config['host']);
        $user = $config['user'];
        $password = $config['password'];
        $timeOut = isset($config['timeout']) ? $config['timeout'] : 3;
        $charset = isset($config['charset']) ? $config['charset'] : 'utf8mb4';
        $mysql = null;
        $ops = [
            \PDO::ATTR_AUTOCOMMIT => true,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_TIMEOUT => $timeOut,
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '" . $charset . "'"
        ];
        try {
            $mysql = new \PDO($dsn, $user, $password, $ops);
        } catch (\PDOException $e) {
            $mysql = null;
            Log::getInstance()->exceptionLog($e, 'pdo_error');
            throw new \Exception('数据库链接错误', 500);
        }
        return $mysql;
    }

    /**
     * @param $config
     * @return null|\PDO
     * @throws \Exception
     */
    public static function getConnect($config)
    {
        //根据数据库配置 返回对应的
        $key = md5(serialize($config));

        if (!isset(self::$pool[$key]) || self::$pool[$key] === null) {
            self::$pool[$key] = self::connet($config);
        }
        return self::$pool[$key];
    }
}