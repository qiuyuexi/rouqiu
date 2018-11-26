<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../../Autoload.php';

use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
{

    public function testSetAndGetAndDelete()
    {
        $key = 1;
        $value = 10;
        $result = \Lib\Common\BaseMemcache::set($key, $value);
        $this->assertTrue(true, $result);
        $data = \Lib\Common\BaseMemcache::get($key);
        $this->assertEquals($value, $data);
        $result = \Lib\Common\BaseMemcache::delete($key);
        $this->assertTrue(true, $result);
    }

    public function testArrSetAndGetAndDelete()
    {
        $key = 1;
        $value = range(1, 100);
        $result = \Lib\Common\BaseMemcache::set($key, $value);
        $this->assertTrue(true, $result);
        $data = \Lib\Common\BaseMemcache::get($key);
        $this->assertEquals($value, $data);
        $result = \Lib\Common\BaseMemcache::delete($key);
        $this->assertTrue(true, $result);
    }
}