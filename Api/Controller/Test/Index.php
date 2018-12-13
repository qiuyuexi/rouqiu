<?php

namespace Api\Controller\Test;

use Api\Controller\ApiController;

class Index extends ApiController
{
    public function index()
    {
        $this->output(0, 'test');
    }
}