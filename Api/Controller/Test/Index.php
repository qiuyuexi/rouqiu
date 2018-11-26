<?php

namespace Api\Controller\Test;

use Api\Cache\Test2Cache;
use Api\Cache\TestCache;
use Api\Controller\ApiController;
use Lib\Driver\Curl;
Use Lib\Driver\Log;

class Index extends ApiController
{
    public function index()
    {
        $this->output(0, 'test');
    }
}