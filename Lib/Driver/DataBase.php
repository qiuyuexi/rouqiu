<?php

namespace Lib\Driver;

/**
 * Class DataBase
 * User: qyx
 * Date: 2018/11/21
 * Time: 下午7:43
 * @package Lib\Driver
 */
class DataBase
{
    const ENVFILE = '';
    private $pool;

    public static function connet($config)
    {
        $dsn = "mysql:dbname=%s;host=%s";
        $dsn = sprintf($dsn, $config['dbname'], $config['host']);
        $user = $config['user'];
        $password = $config['password'];
        $mysql = null;
        $ops = [
            \PDO::ATTR_AUTOCOMMIT => true,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_TIMEOUT => $config['timeout'],
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '" . $config['charset'] . "'"
        ];
        try {
            $mysql = new \PDO($dsn, $user, $password, $ops);
        } catch (\PDOException $e) {
            Log::errorLog($e->getMessage(), 'Connection failed');
        }
        return $mysql;
    }


    public static function getConnect($isMaster = true)
    {
        //根据数据库配置 返回对应的
    }
}