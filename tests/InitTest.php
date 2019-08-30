<?php

use PHPUnit\Framework\TestCase;

class testA
{
    public $bb;

    public function __construct(namespace\testB $b)
    {
        $this->bb = $b;
    }
}

class  testB
{
    public function get()
    {
        return 'b';
    }
}

class InitTest extends TestCase
{
    public function testGetClass()
    {
        $a = \Rq\Common\Init::getClass('testA');
        $this->assertEquals('b', $a->bb->get());
    }
}