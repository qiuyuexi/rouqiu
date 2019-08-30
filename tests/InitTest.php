<?php

use PHPUnit\Framework\TestCase;

class testA
{
    public $bb;
    public $c;
    public $d;
    public $e;

    public function __construct(namespace\testB $b, $c = 1, $d = '', Closure $e = null)
    {
        $this->bb = $b;
        $this->c = $c;
        $this->d = $d;
        $this->e = $e;
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
        $this->assertEquals(1, $a->c);
        $this->assertEquals('', $a->d);
        $this->assertEquals(null, $a->e);
    }
}