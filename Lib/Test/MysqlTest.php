<?php

require_once __DIR__ . '/head.php';

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
    /**
     * 生成表
     * @throws Exception
     */
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
  `t` int(4) NOT NULL,
  `test` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;";
        $result = \Lib\Driver\Mysql::getInstance()->write($createTableSql);
        $this->assertTrue(true, $result);

        //是否执行成功
        $data = \Lib\Driver\Mysql::getInstance()->select(\Lib\Driver\Mysql::getInstance()->getTable())->fetchAll();
        $this->assertEquals(0, count($data));
    }

    /**
     * 测试insert
     * @throws Exception
     */
    public function testInsert()
    {
        $mysql = \Lib\Driver\Mysql::getInstance();
        $result = $mysql->insert($mysql->getTable(), ['t' => 1, 'test' => 2])->exec();
        $this->assertEquals(1, $result);
        $data = \Lib\Driver\Mysql::getInstance()->select(\Lib\Driver\Mysql::getInstance()->getTable())->fetchAll();
        $this->assertEquals(1, count($data));
    }

    /**
     * 测试批量插入
     * @throws Exception
     */
    public function testInsertBatch()
    {
        $mysql = \Lib\Driver\Mysql::getInstance();
        $data = [
            [
                't' => 11,
                'test' => 22
            ],
            [
                't' => 22,
                'test' => 33
            ]
        ];
        $result = $mysql->insertBatch($mysql->getTable(), $data)->exec();
        $this->assertEquals(2, $result);

    }

    /**
     * 查询
     * @throws Exception
     */
    public function testSelect()
    {
        $mysql = \Lib\Driver\Mysql::getInstance();

        //普通查询
        $data = $mysql->select($mysql->getTable(), ['t', 'test'])->setWhere("t=?")->setParams([1])->fetch();
        $data2 = ['t' => 1, 'test' => 2];
        $isDiff = false;
        foreach ($data2 as $k => $v) {
            if ($data[$k] != $v) {
                $isDiff = true;
            }
        }
        $this->assertTrue(true, !$isDiff);

        //排序查询
        $data = $mysql->select($mysql->getTable(), ['t', 'test'])->orderby(['test' => 'asc', 't' => 'desc'])->fetch();
        $data2 = ['t' => 22, 'test' => 33];
        foreach ($data2 as $k => $v) {
            if ($data[$k] != $v) {
                $isDiff = true;
            }
        }
        $this->assertTrue(true, !$isDiff);
    }

    /**
     * 测试删除
     * @throws Exception
     */
    public function testDelete()
    {
        $mysql = \Lib\Driver\Mysql::getInstance();
        $data = $mysql->select($mysql->getTable(), ['id'])->fetchAll();
        $result = $mysql->delete($mysql->getTable())->exec();
        $this->assertEquals(count($data), $result);
    }

    public function testTran()
    {
        $mysql = \Lib\Driver\Mysql::getInstance();
        $result = $mysql->transaction(function () use ($mysql) {
            $data = [
                [
                    't' => 1,
                    'test' => 2
                ]
            ];
            $result = $mysql->insertBatch($mysql->getTable(), $data)->exec();
            if ($result === false) {
                throw new \Exception("写入失败", 500);
            }
            $data = [
                [
                    't' => 1,
                    'test' => 2
                ]
            ];

            $result = $mysql->insertBatch($mysql->getTable(), $data)->exec();
            if ($result === false) {
                throw new \Exception("写入失败", 500);
            }
        });
        $this->assertTrue(true, $result);
        $result = $mysql->delete($mysql->getTable())->exec();
        $this->assertTrue(true, $result);
    }
}