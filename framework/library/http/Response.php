<?php

namespace top\library\http;

use top\library\http\response\ResponseData;

/**
 * 响应类
 * Class Response
 * @package top\library\http
 */
class Response
{

    /**
     * 当前类实例
     * @var null
     */
    private static $instance = null;

    /**
     * 响应内容
     * @var null
     */
    private $content = null;

    /**
     * 响应头
     * @var array
     */
    private $header = [];

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * 获取当前类单例
     * @return null|Response
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 设置Header
     * @param array $header
     * @return $this
     */
    public function header($header = [])
    {
        return $this;
    }

    /**
     * 返回内容
     * @param $data
     * @return false|int|null|string
     */
    public function dispatch($data)
    {
        // 处理响应数据，并返回
        $responseData = new ResponseData($data);
        $this->content = $responseData->dispatch();

        return $this->content;
    }

}