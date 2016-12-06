<?php

namespace Driver;

/**
 * Class myMongo
 * @package Driver
 * 未修改完毕
 */

class myMongo
{

    private static $conn;

    private $host;//域名

    private $port;//端口号

    private $user;//用户名

    private $pwd;//密码

    private $mongo;//momgo服务

    private $db;//选择的数据库

    private $collection;//选择的集合

    public function __construct ()
    {

        $this->host = 'localhost';

        $this->port = '27018';

        $this->db = 'cloud_log';

        $this->user = '';

        $this->pwd = '';

        $server = "mongodb://{$this->host}:{$this->port}";

        try {

            $this->mongo = new \MongoClient($server);
        } catch (Exception $e) {

            $this->mongo = NULL;
        }

    }


    /**
     * [getConn description]
     * @return [type] [description]
     * @descripe mongodb单例
     */

    public static function init ()
    {

        if (!isset(self::$conn)) {
            self::$conn = new myMongo();
        }

        return self::$conn;
    }

    /**
     * [selectDb description]
     * @param  [type] $db [description]
     * @return [type]     [description]
     * @descripe 选择数据库
     */
    public function selectDb ($db)
    {

        self::$conn->db = self::$conn->mongo->$db;

    }

    /**
     * [selectCollection description]
     * @return [type] [description]
     * @descripe 修改选中的集合
     */

    public function selectCollection ($collection)
    {

        self::$conn->collection = self::$conn->db->$collection;

    }


    /**
     * [insert description]
     * @return [type] [description]
     * @descripe 往指定的集合中插入数据
     */

    public function insert ($data = array())
    {

        $status = self::$conn->collection->insert($data);

        return $status;
    }


    /**
     * [find description]
     * @return [type] [description]
     * @descripe 返回该集合的元素，专门用来计算
     */

    public function find ($query = array(), $limit = 0)
    {

        $data = self::$conn->collection->find($query)->limit($limit);

        $arr = array();

        foreach ($data as $key => $value) {

            unset($value['_id']);

            $arr[] = array(
                'm' => $value['m'] . '',
                't' => $value['t'] . '',
                'v' => $value['v'] . '',
            );
        }

        return $arr;
    }


    /**
     * [count description]
     * @return [type] [description]
     * @descripe 计算该集合的元素个数
     */

    public function count ($query = array())
    {

        return self::$conn->collection->count($query);

    }
}