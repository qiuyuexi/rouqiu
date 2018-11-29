<?php

namespace Lib\Driver;

use Lib\Common\Init;
use Lib\Driver\Traits\Singleton;

class Log
{
    use Singleton;
    protected $logDir = '';
    protected $envFile = '';
    const ERROR = 'error.log';
    const INFO = 'info.log';
    const DEBUG = 'debug.log';
    const EXCEPTION = 'exception.log';

    public function __construct()
    {
        $this->logDir = Config::getConfig($this->envFile, 'dir');
        if (empty($this->logDir)) {
            error_log('log_config_empty');
            $this->logDir = Init::getRoot() . '/log';
        }
        if (!is_dir($this->logDir)) {
            mkdir($this->logDir, 0755, true);
        }
    }

    public function errorLog($info, $fileName = '')
    {
        $this->log($info, $fileName, self::ERROR);
    }

    public function infoLog($info, $fileName = '')
    {
        $this->log($info, $fileName, self::INFO);
    }

    public function debugLog($info, $fileName = '')
    {
        $this->log($info, $fileName, self::DEBUG);
    }

    public function exceptionLog(\Exception $e, $fileName)
    {
        $errorInfo = [
            'msg' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'trace' => $e->getTrace()
        ];
        $this->log($errorInfo, $fileName,self::EXCEPTION);
    }

    public function customizeLog($info, $fileName = '', $logType = 'default')
    {
        $this->log($info, $fileName, $logType);
    }

    private function log($info, $infoName = '', $logType)
    {
        $tpl = [
            'date' => date('Y-m-d H:i:s', time()),
            'message' => $info,
            'info_name' => $infoName
        ];
        $tpl = json_encode($tpl) . PHP_EOL;
        if (is_dir($this->logDir)) {
            $file = $this->logDir . '/' . $logType;
            $result = file_put_contents($file, $tpl, FILE_APPEND);
        } else {
            $result = error_log($tpl);
        }
        $result = $result ? true : $result;
        return $result;
    }
}