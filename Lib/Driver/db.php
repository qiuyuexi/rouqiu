<?php

namespace Driver;

/**
 * Class db
 * @package Driver
 * descripe 封装的mysqli
 */
class db
{

    private $db_host;//ip

    private $db_user;//数据库登录用户

    private $db_pwd;//数据库登录密码

    private $db_name;//数据库

    private $db_port;//端口

    private $mysqli;

    static private $conn = NULL;

    public function __construct ($db_host = NULL, $db_user = NULL, $db_pwd = NULL, $db_name = NULL, $db_port = NULL)
    {

        $this->db_host = isset($db_host) ? $db_host : DB_HOST;

        $this->db_user = isset($db_user) ? $db_user : DB_USER;

        $this->db_pwd = isset($db_pwd) ? $db_pwd : DB_PWD;

        $this->db_name = isset($db_name) ? $db_name : DB_NAME;

        $this->db_port = isset($db_port) ? $db_port : DB_PORT;

        $this->mysqli = new \mysqli($this->db_host, $this->db_user, $this->db_pwd, $this->db_name, $this->db_port);

        if (mysqli_connect_error()) {

            writeLog(mysqli_connect_error());

            $this->mysqli = NULL;

        } else {
            $this->mysqli->set_charset("utf8mb4");

        }
    }


    /**
     * @descripe 获得数据库的链接
     */

    static public function init ($status = 0)
    {

        if (is_null(self::$conn)) {
            self::$conn = new db();
        }

        return self::$conn;
    }


    /**
     * @param string sql 要执行的sql语句
     * @return array  查询到的数据（多维数组）
     * @descripe  执行数据库查询语句，返回所有数据
     */

    public function select ($sql)
    {

        $data = NULL;

        try {

            if (is_string($sql) && strlen($sql) > 1) {

                $result = self::$conn->mysqli->query($sql);

                if ($result == true) {

                    $num = $result->num_rows;

                    if ($num > 0) {

                        while ($row = $result->fetch_assoc()) {

                            $data[] = $row;

                        }

                        $result->free();
                    }

                } else {

                    trigger_error('数据库执行出错' . $sql, E_USER_ERROR);

                    $this->log($sql);

                    $data = NULL;
                }
            } else {

                trigger_error('数据出错' . var_export($sql, true), E_USER_ERROR);

            }

        } catch (Exception $e) {

            $str = $e->getMessage();

            $this->log($str);

        }

        return $data;
    }


    /**
     * @param string sql 要执行的sql语句
     * @return array  查询到的数据（一维数组）
     * @descripe  执行数据库查询语句，只返回第一组数据
     */

    public function find ($sql)
    {

        $data = NULL;

        try {

            if (is_string($sql) && strlen($sql) > 1) {

                $result = self::$conn->mysqli->query($sql);

                if ($result == true) {

                    $num = $result->num_rows;

                    if ($num > 0) {

                        while ($row = $result->fetch_assoc()) {

                            $data = $row;

                            break;
                        }

                        $result->free();
                    }

                } else {

                    trigger_error('数据库执行出错' . $sql, E_USER_ERROR);

                    $this->log($sql);

                    $data = NULL;
                }

            } else {

                trigger_error('数据出错' . var_export($sql, true), E_USER_ERROR);
            }

        } catch (Exception $e) {

            $str = $e->getMessage();

            $this->log('错误' . $str);

        }

        return $data;
    }


    /**
     * @param string 要执行的sql语句
     * @return int 执行的结果，成功返回id
     * @descripe 执行数据库插入语句
     */

    public function add ($sql = '')
    {

        $status = false;

        try {

            if (is_string($sql) && strlen($sql) > 1) {

                $status = self::$conn->mysqli->query($sql);

                if ($status == true) {

                    $status = self::$conn->mysqli->insert_id;

                } else {

                    trigger_error('数据库执行出错' . $sql, E_USER_ERROR);

                    $this->log($sql);
                }

            } else {

                trigger_error('数据出错' . var_export($sql, true), E_USER_ERROR);
            }

        } catch (Exception $e) {

            $str = $e->getMessage();

            $this->log($str);
        }

        return $status;
    }


    /**
     * @param string 要执行的sql语句
     * @return boolen 执行的结果，true or false;
     * @descripe 执行数据库更新语句
     */

    public function update ($sql = '')
    {

        $status = false;

        try {

            if (is_string($sql) && strlen($sql) > 1) {

                $status = self::$conn->mysqli->query($sql);

                if ($status == false) {

                    trigger_error('数据库执行出错' . $sql, E_USER_ERROR);

                    $this->log($sql);
                }

            } else {

                trigger_error('数据出错' . var_export($sql, true), E_USER_ERROR);

            }

        } catch (Exception $e) {

            $str = $e->getMessage();

            $this->log($str);
        }

        return $status;
    }


    /**
     * @param string 要执行的sql语句
     * @return boolen 执行的结果，true or false;
     * @descripe 执行数据库删除语句
     */

    public function delete ($sql)
    {

        $status = false;

        try {

            if (is_string($sql) && strlen($sql) > 1) {

                $status = self::$conn->mysqli->query($sql);

                if ($status == false) {

                    trigger_error('数据库执行出错' . $sql, E_USER_ERROR);

                    $this->log($sql);
                }

            } else {

                trigger_error('数据出错' . var_export($sql, true), E_USER_ERROR);

            }

        } catch (Exception $e) {

            $str = $e->getMessage();

            $this->log($str);

        }

        return $status;
    }


    public function autocommit ($status)
    {

        self::$conn->mysqli->autocommit($status);
    }


    public function commit ()
    {

        self::$conn->mysqli->commit();
    }


    public function rollback ()
    {

        self::$conn->mysqli->rollback();
    }


    public function __destruct ()
    {

        if (self::$conn->mysqli !== NULL) {

            $status = self::$conn->mysqli->close();

            if ($status) {
                self::$conn->mysqli = NUll;
            }

        }
    }


    /**
     *
     */
    public function close ()
    {

        if (self::$conn->mysqli !== NULL) {

            $status = self::$conn->mysqli->close();

            if ($status) {
                self::$conn->mysqli = NUll;
            }

        }

    }

    /**
     * @param string sql 执行的sql语句
     * @descripe 将语句发生错误的sql写入数据库
     *
     */
    public function log ($sql)
    {
        writeLog($sql);
    }
}
