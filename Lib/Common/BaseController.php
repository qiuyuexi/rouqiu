<?php

namespace Lib\Common;

class BaseController
{

    public function __construct()
    {

    }

    public function index()
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
