<?php

namespace Api\Cache;

use Lib\Common\BaseMemcache;

class TestCache extends BaseMemcache
{
    const PREFIX = 'test_cache';
}