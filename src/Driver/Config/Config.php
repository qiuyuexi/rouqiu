<?php

namespace Rq\Driver\Config;

abstract class Config
{
    /**
     * @param $envFile
     * @param $key
     * @return array
     */
    static public function getConfig($envFile, $key)
    {
        return [];
    }
}