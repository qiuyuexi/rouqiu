<?php

namespace Lib\Driver;

class Controller
{

    public function __construct()
    {

    }

    public function run()
    {
        $this->before();
        $this->index();
        $this->after();

    }

    public function index()
    {

    }

    public function before()
    {

    }

    public function after()
    {

    }

    public function output($code = 0, $info = '')
    {
        header('Content-type: application/json;charset=utf-8');
        $response = [
            'code' => $code,
            'response' => $info
        ];
        echo json_encode($response);
        return;
    }
}
