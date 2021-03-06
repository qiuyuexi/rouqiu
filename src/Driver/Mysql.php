<?php

namespace Rq\Driver;

use Rq\Driver\Traits\MysqlBuilder;
use Rq\Driver\Traits\Singleton;

/**
 * Class Database
 * User: qyx
 * Date: 2018/11/28
 * Time: 下午7:20
 * @package src\Driver\Database
 */
class Mysql
{
    use Singleton;
    use MysqlBuilder;
    const ENV_FILE = 'db.php';
    protected $table = 't';
    protected $shardCount = '1';
    private static $config = [];
    private $curConfig = [];
    private $logHandle = null;

    private function __construct()
    {
        $this->logHandle = Log::getInstance();
    }

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

    /**
     * @return mixed
     * @throws \Exception
     */
    public function fetch()
    {
        $list = $this->read($this->getSql(), $this->getParams());
        $data = array_pop($list);
        return $data;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function fetchAll()
    {
        $list = $this->read($this->getSql(), $this->getParams());
        return $list;
    }

    /**
     * @return bool|int
     * @throws \Exception
     */
    public function exec()
    {
        $result = $this->write($this->getSql(), $this->getParams());
        return $result;
    }

    /**
     * 读取
     * @param $sql
     * @param array $params
     * @param bool $isMaster
     * @return array
     * @throws \Exception
     */
    public function read($sql, $params = [], $isMaster = false)
    {
        try {
            $sth = self::getConnect($isMaster)->prepare($sql);
            $sth->setFetchMode(\PDO::FETCH_ASSOC);
            $sth->execute($params);
            $result = $sth->fetchAll();
        } catch (\PDOException $e) {
            if (in_array($e->errorInfo[1], ['2006', '2013'])) {
                $this->reConnect();
                return $this->read($sql, $params, $isMaster);
            }
            $result = [];
            $this->logHandle->exceptionLog($e, 'mysql.error');
        }
        $this->resetBuilder();
        return $result;
    }

    /**
     * 写入
     * @param $sql
     * @param array $params
     * @param bool $isMaster
     * @return bool|int
     * @throws \Exception
     */
    public function write($sql, $params = [], $isMaster = true)
    {
        try {
            $sth = self::getConnect($isMaster)->prepare($sql);
            $sth->execute($params);
            $result = $sth->rowCount();
        } catch (\PDOException $e) {
            if (in_array($e->errorInfo[1], ['2006', '2013'])) {
                $this->reConnect();
                return $this->write($sql, $params, $isMaster);
            }
            $result = false;
            $this->logHandle->exceptionLog($e, 'mysql.error');
        }
        $this->resetBuilder();
        return $result;
    }

    /**
     * 事务
     * @param callable $func
     * @return bool
     * @throws \Exception
     */
    public function transaction(callable $func)
    {
        try {
            self::getConnect(true)->beginTransaction();
            $result = $func();
            self::getConnect(true)->commit();

        } catch (\PDOException $e) {
            $result = false;
            self::getConnect(true)->rollBack();
            if (in_array($e->errorInfo[1], ['2006', '2013'])) {
                $this->reConnect();
                return $this->transaction($func);
            }
        }
        $this->resetBuilder();
        return $result;
    }


    /**
     * @param bool $isMaster
     * @return null|\PDO
     * @throws \Exception
     */
    private function getConnect($isMaster = true)
    {
        $this->curConfig = $this->getConfig($isMaster);
        return \Rq\Driver\Database\Mysql::getConnect($this->curConfig);
    }

    /**
     * @throws \Exception
     */
    private function reConnect()
    {
        \Rq\Driver\Database\Mysql::reConnect($this->curConfig);
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