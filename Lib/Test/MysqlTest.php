<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../../Autoload.php';

use PHPUnit\Framework\TestCase;

/**
 * 函数名随便写。。。
 * Class MysqlTest
 * User: qyx
 * Date: 2018/11/29
 * Time: 上午10:57
 */
class MysqlTest extends TestCase
{
    public function testCreate()
    {
        $createTableSql = "
SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `t`
-- ----------------------------
DROP TABLE IF EXISTS `t`;
CREATE TABLE `t` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `t` tinyint(4) NOT NULL,
  `test` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;";
        $result = \Lib\Driver\Mysql::getInstance()->write($createTableSql);
        $this->assertTrue(true, $result);
    }

    public function testReadAndWrite()
    {
        $mysql = \Lib\Driver\Mysql::getInstance();
        $sql = "delete  from t ";
        $mysql->write($sql);

        //是否执行成功
        $sql = "select * from t ";
        $data = $mysql->read($sql);
        $this->assertEquals(0, count($data));

    }

    public function testWrite()
    {
        $mysql = \Lib\Driver\Mysql::getInstance();
        //插入
        $sql = "insert into t(`t`,`test`) values (1,2),(2,3),(3,3)";

        $result = $mysql->write($sql);
        $this->assertEquals(3, $result);
    }

    public function testQuery()
    {
        $mysql = \Lib\Driver\Mysql::getInstance();
        //查询
        $sql = "select t,test from t where t = 1 ";
        $data = $mysql->read($sql);
        $this->assertEquals(1, count($data));
        $data2 = ['t' => 1, 'test' => 2];
        $isDiff = empty(array_diff($data[0], $data2)) && empty(array_diff($data2, $data[0]));
        $this->assertTrue(true, $isDiff);

    }
}