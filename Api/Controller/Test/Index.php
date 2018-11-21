<?php

namespace Api\Controller\Test;

use Api\Controller\ApiController;
Use Lib\Driver\Log;

class Index extends ApiController
{
    public function index()
    {
        Log::infoLog('test');
        Log::errorLog('test');
        Log::debugLog('test');
        $this->output(0, 'test');
    }
}