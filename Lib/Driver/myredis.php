<?php

namespace Driver;

/**
 * Class myRedis
 * @package Driver
 */
class myRedis
{

    private static $conn;

    private $host;

    private $port;

    private $timeout;

    private $redis;

    public function __construct ($host = NULL, $port = NULL, $timeout = NULL)
    {

        $this->host = isset($host) ? $host : '127.0.0.1';

        $this->port = isset($port) ? $port : '6379';

        $this->timeout = isset($timeout) ? $timeout : 2.5;

        $this->redis = new \Redis();

        $this->redis->connect($this->host, $this->port, $this->timeout);
    }


    public function index ()
    {


    }


    /**
     * @param null $host 地址
     * @param null $port 端口号
     * @param null $timeout 过期时间
     * @return myRedis
     */
    public static function init ($host = NULL, $port = NULL, $timeout = NULL)
    {

        if (!isset(self::$conn)) {

            self::$conn = new myRedis($host, $port, $timeout);

        }

        return self::$conn;
    }


    /**
     * @param string $key 键值
     * @param array $data 新增的数据
     * @return boolen action status 执行结果
     * @descripe add data to set
     */

    public function addSetMember ($key = NULL, $data = NULL)
    {

        $status = false;

        if (isset($key) && isset($data)) {

            if (is_array($data)) {

                foreach ($data as $key => $value) {

                    $status = self::$conn->redis->sAdd($key, $data);
                }

            } else {

                $status = self::$conn->redis->sAdd($key, $data);
            }
        }
        return $status;
    }


    /**
     * @param string $key 键值
     * @return array members 数据集合
     * @descripe get members from set where key = $key
     */

    public function getSetMember ($key = NULL)
    {

        $data = NULL;

        if (isset($key) && !empty($key)) {

            $data = self::$conn->redis->sMembers($key);

        }

        return $data;
    }


    /**
     * @param string $key 键值
     * @return array members  要删除的数据
     * @descripe get members from set where key = $key
     */

    public function delSetMember ($key = NULL, $data = NULL)
    {

        $status = true;

        if (!is_null($key) && !is_null($data)) {

            if ($this->isSetMember($key, $data)) {

                $status = self::$conn->redis->sRem($key, $data);

            }
        }

        return $status;
    }


    /**
     * @param string key  键值
     * @param string data 数据
     * @return boolen status 判断结果
     * @descripe data is exist in set where key = $key
     */

    public function isSetMember ($key = NULL, $data = NULL)
    {

        $status = false;

        if (!is_null($key) && !is_null($data)) {

            $status = self::$conn->redis->sIsMember($key, $data);

        }

        return $status;
    }


    /**
     * @param string key
     * @param string data
     * @return len
     * @descripe
     */

    public function addListMember ($key = NULL, $data = NULL)
    {

        $status = false;

        if (!is_null($key) && !is_null($data)) {

            if (is_array($data)) {

                foreach ($data as $key => $value) {

                    $status = self::$conn->redis->lPush($key, $value);

                }
            } else {

                $status = self::$conn->redis->lPush($key, $data);
            }
        }
        return $status;
    }


    /**
     * @param string key
     * @return int len
     * @descripe calc list size
     */

    public function listSize ($key = NULL)
    {

        $len = 0;

        if (!is_null($key)) {

            $len = self::$conn->redis->lSize($key);

        }

        return $len;
    }


    /**
     * @param string key
     * @param int    index start
     * @param int    index end
     */

    public function getListMember ($key = NULL, $start = 0, $end = -1)
    {

        $data = NULL;

        if (!is_null($key)) {

            $data = self::$conn->redis->lRange($key, $start, $end);

        }

        return $data;
    }


    /**
     * [getListPopMember description]
     * @param  [type] $key [description]
     * @return [type]      [description]
     * @descripe 获取队列的头元素
     */

    public function getListFstMember ($key = NULL)
    {

        $data = NULL;

        if (!is_null($key) && $this->listSize($key)) {

            $data = self::$conn->redis->LPOP($key);
        }

        return $data;

    }

    /**
     * @param string key
     * @param string value
     * @return boolen
     */

    public function addString ($key = NULL, $value = NULL)
    {

        $status = false;

        if (!is_null($key) && !is_null($value)) {

            if (!is_array($value)) {

                $status = self::$conn->redis->set($key, $value);
            }
        }

        return $status;
    }


    /**
     * @param string key
     * @return int
     * @descripe get value from stirng where key = $key
     */

    public function getString ($key = NULL)
    {

        $value = NULL;

        if ($this->existString($key)) {

            $value = self::$conn->redis->get($key);
            if ($value == 'null' || $value == 'NULL') {
                $value = NULL;
            }
        }

        return $value;
    }


    /**
     * @param string key
     * @return boolen status
     * @descripe check key is exist in string
     */

    public function existString ($key = NULL)
    {

        $status = false;

        if (!is_null($key)) {

            $status = self::$conn->redis->exists($key);

        }

        return $status;
    }


    /**
     * [addStringT description]
     * @param [type]  $key   [description]
     * @param integer $t [description]
     * @param [type]  $value [description]
     * @descripe 带过期时间
     */

    public function addStringT ($key = NULL, $t = 0, $value = NULL)
    {

        $status = false;

        if (!is_null($key) && !is_null($value)) {

            if (!is_array($value)) {

                $status = self::$conn->redis->setex($key, $t, $value);
            }
        }
        return $status;
    }


    /**
     * [deleteString description]
     * @param  [type] $key [description]
     * @return [type]      [description]
     * @descripe 删除一个字符串
     */

    public function deleteString ($key = NULL)
    {

        $status = false;

        if ($this->existString($key)) {

            $status = self::$conn->redis->del($key);
        }

        return $status;
    }


    /**
     * [incrString description]
     * @param  [type]  $key   [description]
     * @param  integer $value [description]
     * @return [type]         [description]
     */

    public function incrString ($key = NULL, $value = 0)
    {

        $status = false;

        //如果不存在该键值，则初始化
        if ($this->existString($key)) {

            $status = self::$conn->redis->incrby($key, $value);
        } else {

            $status = self::$conn->redis->addString($key, $value);
        }

        return $status;
    }


    /**
     * 情况数据库
     */
    public function flushDb ()
    {

        $status = self::$conn->redis->flushDb();

    }


    /**
     * @param int $num数据库编号
     * @descrise 选择数据库
     */
    public function selectDb ($num = 0)
    {

        self::$conn->redis->select($num);
    }


    public function __destruct ()
    {

        if (self::$conn->redis !== NULL) {

            $status = self::$conn->redis->close();;

            if ($status) {
                self::$conn->redis = NUll;
            }

        }
    }


    /**
     *
     */
    public function close ()
    {
        if (self::$conn->redis !== NULL) {

            $status = self::$conn->redis->close();;

            if ($status) {
                self::$conn->redis = NUll;
            }

        }
    }
}
