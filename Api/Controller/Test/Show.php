<?php

namespace Api\Controller\Test;

use Api\Controller\ApiController;
use Lib\Driver\Log;
use Lib\Driver\Verify;

class Show extends ApiController
{
    public function index()
    {
        $verify = new Verify();
        $verifyCode = $verify->getVerify();
        Log::infoLog($verifyCode,'verify_code');
        $verify->output();
    }
}