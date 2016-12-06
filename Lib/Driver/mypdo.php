<?php

namespace Driver;

/**
 * Class mypdo
 * @package Driver
 */
class mypdo
{

    private $db_type;//数据库 类型

    private $db_host;//数据库主机地址

    private $db_name;//数据有空迷彩

    private $db_user;//数据库用户

    private $db_pwd;//数据库密码

    private $pdo;//pdo链接

    static private $conn;

    public function __construct ($db_host = NULL, $db_user = NULL, $db_pwd = NULL, $db_name = NULL, $db_port = NULL,$db_type = NULL)
    {

        $this->db_host =  isset($db_host)  ?  $db_host : DB_HOST;

        $this->db_user  =  isset($db_user)   ?  $db_user  : DB_USER;

        $this->db_pwd  =  isset($db_pwd)   ?  $db_pwd  : DB_PWD;

        $this->db_name =  isset($db_name)  ?  $db_name : DB_NAME;

        $this->db_port =  isset($db_port)  ?  $db_port : DB_PORT;

        $this->db_type =  isset($db_type)  ?  $db_type : 'mysql';

        //初始化
        try {

            $this->pdo = new  \PDO("{$this->db_type}:host={$this->db_host};dbname={$this->db_name}", "{$this->db_user}", "{$this->db_pwd}", array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 "));

        } catch (Exception $e) {

            writeLog($e->getMessage());
            $this->pdo = NULL;
        }

    }


    /**
     * @return mixed
     * 初始化
     */
    public function init ()
    {

        if (!isset(self::$conn) || empty(self::$conn)) {
            self::$conn = new  mypdo();
        }
        return self::$conn;
    }


    /**
     * @descripe 执行查询一条数据的操作
     * @param string $string 执行的sql语句
     * @param array $param 执行结果
     * @return mixed
     */
    public function find ($string = '', $param = array())
    {

        if ($this->checkSqlParam($string, $param)) {

            $prepare = self::$conn->pdo->prepare($string);//预处理sql语句

            $prepare->execute($param);//执行结果

            $result = $prepare->fetch(\PDO::FETCH_ASSOC);

            return $result;
        } else {
            return array();
        }


    }


    /**
     * @descripe 执行查询一条数据的操作
     * @param string $string 执行的sql语句
     * @param array $param 执行结果
     * @return mixed
     */
    public function select ($string = '', $param = array())
    {

        if ($this->checkSqlParam($string, $param)) {

            $prepare = self::$conn->pdo->prepare($string);//预处理sql语句

            $prepare->execute($param);//执行结果

            $result = $prepare->fetchAll(\PDO::FETCH_ASSOC);

            return $result;
        } else {
            return array();
        }

    }


    /**
     * @descripe 执行删除操作
     * @param string $string执行的sql 语句。
     * @param array $param 参数
     * @return int 执行结果
     */
    public function add ($string = '', $param = array(), $name = 'id')
    {

        if ($this->checkSqlParam($string, $param)) {

            $prepare = self::$conn->pdo->prepare($string);//预处理sql语句

            $status = $prepare->execute($param);//执行结果

            if (false === $status) {
                writeLog(self::$conn->errorInfo());
                return 0;
            } else {
                $status = self::$conn->pdo->lastInsertId();
                return $status;
            }
        } else {
            return 0;
        }
    }

    /**
     * @descripe 执行删除操作
     * @param string $string执行的sql 语句。
     * @param array $param 参数
     * @return int 执行结果
     */
    public function delete ($string = '', $param = array())
    {

        if ($this->checkSqlParam($string, $param)) {

            $prepare = self::$conn->pdo->prepare($string);//预处理sql语句

            $status = $prepare->execute($param);//执行结果

            if (false === $status) {
                writeLog(self::$conn->errorInfo());
            }

            return (false === $status) ? 0 : 1;
        } else {
            return 0;
        }
    }


    /**
     * @descripe 执行更新操作
     * @param string $string执行的sql 语句。
     * @param array $param 参数
     * @return int 执行结果
     */
    public function update ($string = '', $param = array())
    {

        if ($this->checkSqlParam($string, $param)) {

            $prepare = self::$conn->pdo->prepare($string);//预处理sql语句

            $status = $prepare->execute($param);//执行结果

            if (false === $status) {
                writeLog(self::$conn->errorInfo());
            }

            return (false === $status) ? 0 : 1;
        } else {
            return 0;
        }
    }


    /**
     * @descirpe  判断参数是否正确
     * @param $string sql语句 例如 insert into a(num) values(?)
     * @param $param 数组  array(1)
     * @return int
     */
    public function checkSqlParam ($string, $param)
    {

        if (is_string($string) && !empty($string) && is_array($param)) {

            return 1;
        } else {
            return 0;
        }

    }

    /**
     * @return mixed
     *返回错误信息
     */
    public function errorInfo ()
    {
        return implode(',', self::$conn->pdo->errorInfo());
    }


    /**
     * 开启事务
     */
    public function beginTransaction ()
    {
        if (isset(self::$conn) && !empty(self::$conn)) {
            self::$conn->pdo->beginTransaction();
        }
    }

    /**
     * 事务回滚
     */
    public function rollBack ()
    {
        if (isset(self::$conn) && !empty(self::$conn)) {
            self::$conn->pdo->rollBack();
        }
    }

    /**
     *事务提交
     */
    public function commit ()
    {
        if (isset(self::$conn) && !empty(self::$conn)) {
            self::$conn->pdo->commit();
        }
    }


}