<?php

namespace Rq\Common;

use Rq\Driver\Traits\Singleton;

/**
 * 保存请求过程中的变量
 * Class Request
 * User: qyx
 * Date: 2018/12/19
 * Time: 下午5:24
 * @package src\Common
 */
class Request
{
    use Singleton;

    private $post = [];
    private $get = [];
    private $request = [];
    private $pathUrl = '';
    const DATA_TYPE_INT = 1;
    const DATA_TYPE_STRING = 2;
    const DATA_TYPE_FLOAT = 3;
    const DATA_TYPE_ARRAY = 4;
    const DATA_TYPE_OBJECT = 5;
    const DATA_TYPE_BOOLEN = 6;

    private function __construct()
    {
        if (empty($this->request)) {
            if (PHP_SAPI == 'cli') {
                $cliArgs = getopt('', ['post:', 'get:']);
                if (isset($cliArgs['post'])) {
                    parse_str($cliArgs['post'], $_POST);
                }
                if (isset($cliArgs['get'])) {
                    parse_str($cliArgs['get'], $_GET);
                }
                $_REQUEST = array_merge($_GET, $_POST);
            }
            $this->post = $_POST;
            $this->get = $_GET;
            $this->request = $_REQUEST;
            $this->pathUrl = $this->getPathInfo();
        }
    }

    /**
     * @param $key
     * @param null $default
     * @param null $dataType
     * @return array|bool|float|int|mixed|null|object|string
     */
    public function get($key, $default = null, $dataType = null)
    {
        $data = $default;
        if (isset($this->get[$key])) {
            $data = $this->get[$key];
        }
        $data = $this->formatData($data, $dataType);
        return $data;
    }

    /**
     * @param $key
     * @param null $default
     * @param null $dataType
     * @return array|bool|float|int|mixed|null|object|string
     */
    public function post($key, $default = null, $dataType = null)
    {
        $data = $default;
        if (isset($this->post[$key])) {
            $data = $this->post[$key];
        }
        $data = $this->formatData($data, $dataType);
        return $data;
    }

    /**
     * @param $data
     * @param $dataType
     * @return array|bool|float|int|object|string
     */
    private function formatData($data, $dataType)
    {
        switch ($dataType) {
            case self::DATA_TYPE_INT:
                $data = intval($data);
                break;
            case self::DATA_TYPE_STRING:
                $data = (string)$data;
                break;
            case self::DATA_TYPE_FLOAT:
                $data = floatval($data);
                break;
            case self::DATA_TYPE_ARRAY:
                $data = (array)$data;
                break;
            case self::DATA_TYPE_OBJECT:
                $data = (object)$data;
                break;
            case self::DATA_TYPE_BOOLEN:
                $data = boolval($data);
                break;
            default:
                break;
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getPathInfo()
    {
        if (PHP_SAPI == 'cli') {
            $uri = getopt('', ['uri:']);
            $pathInfo = isset($uri['uri']) ? $uri['uri'] : '';
            $pathInfo = trim($pathInfo, '/');
            $pathInfo = ucwords($pathInfo, '/');
        } else {
            $requestUri = $_SERVER['REQUEST_URI'];
            $requestUri = parse_url($requestUri);
            $pathInfo = $requestUri['path'];
            $pathInfo = trim($pathInfo, '/');
            $pathInfo = ucwords($pathInfo, '/');
        }
        return $pathInfo;
    }
}