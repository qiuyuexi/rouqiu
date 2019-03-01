<?php

namespace Rq\Driver\Log;

abstract class Log
{
    protected $logDir = '';
    protected $envFile = 'log.php';
    const ERROR = 'error.log';
    const INFO = 'info.log';
    const DEBUG = 'debug.log';
    const EXCEPTION = 'exception.log';

    public function __construct()
    {
    }

    /**
     * @param $info
     * @param $fileName
     * @return mixed
     */
    abstract public function errorLog($info, $fileName);

    /**
     * @param $info
     * @param $fileName
     * @return mixed
     */
    abstract public function infoLog($info, $fileName);

    /**
     * @param $info
     * @param $fileName
     * @return mixed
     */
    abstract public function debugLog($info, $fileName);

    /**
     * @param \Exception $e
     * @param $fileName
     * @return mixed
     */
    abstract public function exceptionLog(\Exception $e, $fileName);

    /**
     * @param $info
     * @param $fileName
     * @return mixed
     */
    abstract public function customizeLog($info, $fileName);

    /**
     * @param $info
     * @param $fileName
     */
    private function log($info, $fileName)
    {

    }
}