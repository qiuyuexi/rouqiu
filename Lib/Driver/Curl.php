<?php

namespace Lib\Driver;

use Lib\Driver\Traits\Singleton;

/**
 * Class Curl
 * User: qyx
 * Date: 2018/11/23
 * Time: 下午5:10
 * @package Lib\Driver
 */
class Curl
{
    use Singleton;
    private $url = '';
    private $data = [];
    private $method = '';
    private $opts = [];
    private $headers = [];
    private $slowLogTime = 1000;
    private $result = false;

    const CURL_METHOD_POST = 'post';
    const CURL_METHOD_GET = 'get';

    public function __construct($url, $data = [], $headers = [])
    {
        $this->url = $url;
        $this->data = $data;
        $this->headers = $headers;
        $this->slowLogTime = 1000;//1s
        $this->opts = [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_TIMEOUT => 5,//总执行时间
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_VERBOSE => true,//错误时 输出全部信息,
            CURLOPT_URL => $this->url
        ];
        if (!empty($this->headers)) {
            $this->opts[CURLOPT_HTTPHEADER] = $this->headers;
        }
    }

    /**
     * @param bool $userHttpQuery
     * @return $this
     */
    public function post($userHttpQuery = true)
    {
        $data = $this->data;
        if ($userHttpQuery) {
            $data = http_build_query($this->data);
        }
        $this->method = self::CURL_METHOD_POST;
        $this->opts[CURLOPT_POST] = true;
        $this->opts[CURLOPT_POSTFIELDS] = $data;
        return $this;
    }

    /**
     * @return $this
     */
    public function get()
    {
        $this->method = self::CURL_METHOD_GET;
        $this->opts[CURLOPT_POST] = false;
        if (!empty($this->data)) {
            $data = http_build_query($this->data);
            $this->url = strpos($this->url, '?') ? $this->url . '&' . $data : $this->url . '?' . $data;
        }
        $this->opts[CURLOPT_URL] = $this->url;
        return $this;
    }

    /**
     * @param array $setOptions
     * @return $this
     */
    public function setOpts(array $setOptions)
    {
        foreach ($setOptions as $key => $value) {
            $this->opts[$key] = $value;
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function exec()
    {
        $ch = curl_init();
        if (!empty($this->opts[CURLOPT_VERBOSE])) {
            $verbose = fopen('php://temp', 'w+');
            $this->opts[CURLOPT_STDERR] = $verbose;
        }

        curl_setopt_array($ch, $this->opts);

        $startTime = round(microtime(true) * 1000);
        $this->result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlInfo = curl_getinfo($ch);
        if ($httpCode != 200) {
            if (!empty($this->opts[CURLOPT_VERBOSE])) {
                rewind($verbose);
                $verboseLog = stream_get_contents($verbose);
                $curlInfo = htmlspecialchars($verboseLog);
            }
            $logInfo = [
                'curl_info' => $curlInfo,
                'data' => $this->data,
                'header' => $this->headers,
                'url' => $this->url
            ];
            Log::getInstance()->errorLog($logInfo, 'curl_error');
        }
        $endTime = round(microtime(true) * 1000);

        if ($httpCode && ($endTime - $startTime > $this->slowLogTime)) {
            $logInfo = [
                'curl_info' => $curlInfo,
                'data' => $this->data,
                'header' => $this->headers,
                'url' => $this->url
            ];
            Log::getInstance()->infoLog($logInfo, 'curl_slow');
        }
        curl_close($ch);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }
}