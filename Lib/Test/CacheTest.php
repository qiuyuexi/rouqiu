<?php

require_once __DIR__.'/head.php';

use PHPUnit\Framework\TestCase;


class CacheTest extends TestCase
{

    public function testSetAndGetAndDelete()
    {
        $key = 1;
        $value = 10;
        $result = \Lib\Driver\Memcached::set($key, $value);
        $this->assertTrue(true, $result);
        $data = \Lib\Driver\Memcached::get($key);
        $this->assertEquals($value, $data);
        $result = \Lib\Driver\Memcached::delete($key);
        $this->assertTrue(true, $result);
        $data = \Lib\Driver\Memcached::get($key);
        $this->assertEquals(false, $data);;
    }

    public function testArrSetAndGetAndDelete()
    {
        $key = 1;
        $value = range(1, 100);
        $result = \Lib\Driver\Memcached::set($key, $value);
        $this->assertTrue(true, $result);
        $data = \Lib\Driver\Memcached::get($key);
        $this->assertEquals($value, $data);
        $result = \Lib\Driver\Memcached::delete($key);
        $this->assertTrue(true, $result);
        $data = \Lib\Driver\Memcached::get($key);
        $this->assertEquals(false, $data);
    }
}