<?php


use PHPUnit\Framework\TestCase;


class MemCacheTest extends TestCase
{

    public function testMulti()
    {
        $data = [
            '12' => 11,
            '123' => 1234
        ];
        \Rq\Driver\Memcached::getInstance()->setMulti($data);
        $value = \Rq\Driver\Memcached::getInstance()->get(12);
        $this->assertEquals(11, $value);
        $value = \Rq\Driver\Memcached::getInstance()->get(123);
        $this->assertEquals(1234, $value);


        $data = [12, 123];
        $cattoken = [];
        $result = \Rq\Driver\Memcached::getInstance()->getMulti($data, $cattoken);
        $this->assertEquals(11,$result['mc:12']);
        $this->assertEquals(1234,$result['mc:123']);

        $result = \Rq\Driver\Memcached::getInstance()->deleteMulti($data);
        $value = \Rq\Driver\Memcached::getInstance()->get(12);
        $this->assertEquals(false, $value);
        $value = \Rq\Driver\Memcached::getInstance()->get(123);
        $this->assertEquals(false, $value);


    }

    public function testIncrease()
    {
        $result = \Rq\Driver\Memcached::getInstance()->delete(1);
        $result = \Rq\Driver\Memcached::getInstance()->increment(1, 5);
        $value = \Rq\Driver\Memcached::getInstance()->get(1);
        $this->assertEquals(5, $value);
        $value = \Rq\Driver\Memcached::getInstance()->decrement(1, 4);
        $this->assertEquals(1, $value);
    }

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