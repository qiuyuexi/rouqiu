<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../../Autoload.php';

use PHPUnit\Framework\TestCase;

class LogTest extends TestCase
{
    public function testLog()
    {
        $string = 'test';
        $result = \Lib\Driver\Log::errorLog($string);
        $this->assertTrue(true, $result);
        $string = 'test';
        $result = \Lib\Driver\Log::infoLog($string);
        $this->assertTrue(true, $result);
        $string = 'test';
        $result = \Lib\Driver\Log::debugLog($string);
        $this->assertTrue(true, $result);
    }
}