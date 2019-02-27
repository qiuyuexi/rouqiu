<?php

use PHPUnit\Framework\TestCase;

class RedisTest extends TestCase
{
    public function testGetSetDelete()
    {
            $key = 1;
            $value = 112;
            $result = \Rq\Driver\Redis::getInstance()->set($key,$value);
            $this->assertTrue($result);
            $v = \Rq\Driver\Redis::getInstance()->get($key);
            $this->assertEquals($value,$v);
            $result = \Rq\Driver\Redis::getInstance()->delete($key);
            $this->assertEquals($result,1);
            $v = \Rq\Driver\Redis::getInstance()->get($key);
            $this->assertEquals(false,$v);
    }
}