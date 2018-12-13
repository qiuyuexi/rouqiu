<?php

require_once __DIR__.'/head.php';

use PHPUnit\Framework\TestCase;


/**
 * 网上找的开放接口。。
 * Class CurlTest
 * User: qyx
 * Date: 2018/11/29
 * Time: 上午11:16
 */
class CurlTest extends TestCase
{

    public function testGet1()
    {
        $url = "https://api.apiopen.top/singlePoetry";
        $result = \Lib\Driver\Curl::getInstance($url)->get()->exec()->getResult();
        $result = json_decode($result, true);
        $this->assertEquals(200, $result['code']);
    }

    public function testGet2()
    {
        $url = "https://www.apiopen.top/weatherApi?city=%E6%88%90%E9%83%BD";
        $result = \Lib\Driver\Curl::getInstance($url)->get()->exec()->getResult();
        $result = json_decode($result, true);
        $this->assertEquals(200, $result['code']);
    }

    public function testGet3()
    {
        $url = "https://www.apiopen.top/weatherApi";
        $data = ['city' => '成都'];
        $result = \Lib\Driver\Curl::getInstance($url, $data)->get()->exec()->getResult();
        $result = json_decode($result, true);
        $this->assertEquals(200, $result['code']);
    }

    public function testGet4()
    {
        $url = "https://www.apiopen.top/weatherApi?city1=%E6%88%90%E9%83%BD";
        $data = ['city' => '成都'];
        $result = \Lib\Driver\Curl::getInstance($url, $data)->get()->exec()->getResult();
        $result = json_decode($result, true);
        $this->assertEquals(200, $result['code']);
    }

    public function testPost1()
    {
        $url = "https://www.apiopen.top/login";
        $data = [
            'key' => '00d91e8e0cca2b76f515926a36db68f5',
            'phone' => 13594347817,
            'passwd' => 123456
        ];
        $result = \Lib\Driver\Curl::getInstance($url, $data)->post()->exec()->getResult();
        $result = json_decode($result, true);
        $this->assertEquals(200, $result['code']);
    }


}