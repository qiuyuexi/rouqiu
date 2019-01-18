<?php

require_once __DIR__ . '/head.php';

use PHPUnit\Framework\TestCase;


class MemCacheTest extends TestCase
{

    public function testSetAndGetAndDelete()
    {
        $key = 1;
        $value = 10;
        $result = \Rq\Driver\Memcached::getInstance()->set($key, $value);
        $this->assertEquals(true, $result);
        $data = \Rq\Driver\Memcached::getInstance()->get($key);
        $this->assertEquals($value, $data);
        $result = \Rq\Driver\Memcached::getInstance()->delete($key);
        $this->assertEquals(true, $result);
        $data = \Rq\Driver\Memcached::getInstance()->get($key);
        $this->assertEquals(false, $data);;
    }

    public function testArrSetAndGetAndDelete()
    {
        $key = 1;
        $value = range(1, 100);
        $result = \Rq\Driver\Memcached::getInstance()->set($key, $value);
        $this->assertEquals(true, $result);
        $data = \Rq\Driver\Memcached::getInstance()->get($key);
        $this->assertEquals($value, $data);
        $result = \Rq\Driver\Memcached::getInstance()->delete($key);
        $this->assertEquals(true, $result);
        $data = \Rq\Driver\Memcached::getInstance()->get($key);
        $this->assertEquals(false, $data);
    }

    public function testCas()
    {
        $key = 1;
        $v1 = 'k1';
        $v2 = 'k2';

        \Rq\Driver\Memcached::getInstance()->delete($key);

        $result = \Rq\Driver\Memcached::getInstance()->add($key, $v1);
        $this->assertEquals(true, $result);

        $result = \Rq\Driver\Memcached::getInstance()->get($key, null, $casToken);
        $this->assertEquals($v1, $result);

        $result = \Rq\Driver\Memcached::getInstance()->cas($casToken + 1, $key, $v2);
        $this->assertEquals(false, $result);

        $result = \Rq\Driver\Memcached::getInstance()->get($key);
        $this->assertEquals($v1, $result);

        $result = \Rq\Driver\Memcached::getInstance()->cas($casToken, $key, $v2);
        $this->assertEquals(true, $result);

        $result = \Rq\Driver\Memcached::getInstance()->get($key);
        $this->assertEquals($v2, $result);
    }
}