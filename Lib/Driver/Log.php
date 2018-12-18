<?php

namespace Lib\Driver;

use Lib\Common\Init;
use Lib\Driver\Traits\Singleton;

/**
 * 日志类
 * Class Log
 * User: qyx
 * Date: 2018/12/18
 * Time: 上午11:39
 * @package Lib\Driver
 */
class Log
{
    use Singleton;
    protected $logDir = '';
    protected $envFile = '';
    const ERROR = 'error.log';
    const INFO = 'info.log';
    const DEBUG = 'debug.log';
    const EXCEPTION = 'exception.log';

    private function __construct()
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

    /**
     * 异常
     * @param \Exception $e
     * @param $fileName
     */
    public function exceptionLog(\Exception $e, $fileName)
    {
        $errorInfo = [
            'msg' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'trace' => $e->getTrace()
        ];
        $this->log($errorInfo, $fileName, self::EXCEPTION);
    }

    /**
     * 自定义日志
     * @param $info
     * @param string $fileName
     * @param string $logType
     */
    public function customizeLog($info, $fileName = '', $logType = 'default.log')
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
            if ($result === false) {
                error_log($tpl);
            }
        } else {
            $result = error_log($tpl);
        }
        $result = $result ? true : $result;
        return $result;
    }
}