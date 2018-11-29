<?php

namespace Lib\Driver;

use Lib\Driver\Config;
use Lib\Driver\Log;
use Lib\Driver\Traits\Singleton;

/**
 * Class Database
 * User: qyx
 * Date: 2018/11/28
 * Time: 下午7:20
 * @package Lib\Driver\Database
 */
class Mysql
{
    use Singleton;
    const ENV_FILE = 'db.php';
    protected $table = 't';
    protected $shardCount = '1';
    private $whereSql = '';
    private $params = [];
    private $sql = '';
    private static $config = [];

    public function getTable($shardId = '')
    {
        if (empty($shardId)) {
            return $this->table;
        }
        $shardId = crc32($shardId) % $this->shardCount;
        $shardId = str_pad($shardId, strlen($this->shardCount), '0', STR_PAD_LEFT);
        $table = $this->table . '_' . $shardId;
        return $table;
    }


    public function select()
    {

    }

    public function update()
    {

    }

    public function delete()
    {

    }

    public function read($sql, $params = [], $isMaster = false)
    {
        try {
            $sth = self::getConnect($isMaster)->prepare($sql);
            $sth->setFetchMode(\PDO::FETCH_ASSOC);
            $sth->execute($params);

            $result = $sth->fetchAll();
        } catch (\PDOException $e) {
            $result = [];
            Log::getInstance()->exceptionLog($e, 'mysql.error');
        }
        return $result;
    }

    public function write($sql, $params = [], $isMaster = true)
    {
        try {
            $sth = self::getConnect($isMaster)->prepare($sql);
            $sth->execute($params);
            $result = $sth->rowCount();
        } catch (\PDOException $e) {
            $result = false;
            Log::getInstance()->exceptionLog($e, 'mysql.error');
        }
        return $result;
    }


    /**
     * @param bool $isMaster
     * @return null|\PDO
     * @throws \Exception
     */
    private function getConnect($isMaster = true)
    {
        $config = $this->getConfig($isMaster);
        return \Lib\Driver\Database\Mysql::getConnect($config);
    }

    /**
     * @param bool $isMaster
     * @return mixed
     */
    private function getConfig($isMaster = true)
    {
        $key = md5(static::ENV_FILE);
        if (!isset(self::$config[$key])) {
            self::$config[$key] = Config::getConfig(static::ENV_FILE);
        }
        if ($isMaster) {
            $config = self::$config[$key]['master'];
        } else {
            $randKey = array_rand(self::$config[$key]['slave_list']);
            $config = self::$config[$key]['slave_list'][$randKey];
        }
        return $config;
    }
}