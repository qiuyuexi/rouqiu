<?php

require_once __DIR__.'/head.php';

use PHPUnit\Framework\TestCase;


class MemCacheTest extends TestCase
{

    public function testSetAndGetAndDelete()
    {
        $key = 1;
        $value = 10;
        $result = \Lib\Driver\Memcached::set($key, $value);
        $this->assertEquals(true, $result);
        $data = \Lib\Driver\Memcached::get($key);
        $this->assertEquals($value, $data);
        $result = \Lib\Driver\Memcached::delete($key);
        $this->assertEquals(true, $result);
        $data = \Lib\Driver\Memcached::get($key);
        $this->assertEquals(false, $data);;
    }

    public function testArrSetAndGetAndDelete()
    {
        $key = 1;
        $value = range(1, 100);
        $result = \Lib\Driver\Memcached::set($key, $value);
        $this->assertEquals(true, $result);
        $data = \Lib\Driver\Memcached::get($key);
        $this->assertEquals($value, $data);
        $result = \Lib\Driver\Memcached::delete($key);
        $this->assertEquals(true, $result);
        $data = \Lib\Driver\Memcached::get($key);
        $this->assertEquals(false, $data);
    }

    public function testCas()
    {
        $key = 1;
        $v1 = 'k1';
        $v2 = 'k2';

        \Lib\Driver\Memcached::delete($key);

        $result = \Lib\Driver\Memcached::add($key,$v1);
        $this->assertEquals(true,$result);

        $result = \Lib\Driver\Memcached::get($key,null,$casToken);
        $this->assertEquals($v1,$result);

        $result = \Lib\Driver\Memcached::cas($casToken+1,$key,$v2);
        $this->assertEquals(false,$result);

        $result = \Lib\Driver\Memcached::get($key);
        $this->assertEquals($v1,$result);

        $result = \Lib\Driver\Memcached::cas($casToken,$key,$v2);
        $this->assertEquals(true,$result);

        $result = \Lib\Driver\Memcached::get($key);
        $this->assertEquals($v2,$result);
    }
}