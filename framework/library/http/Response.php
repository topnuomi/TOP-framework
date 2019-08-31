<?php

namespace top\library\http;

use top\library\http\response\ResponseData;
use top\traits\Instance;

/**
 * 响应类
 * Class Response
 * @package top\library\http
 */
class Response
{

    use Instance;

    /**
     * 响应内容
     * @var null
     */
    public $content = null;

    /**
     * 响应头
     * @var array
     */
    private $header = [];

    /**
     * 设置Header
     * @param null $header
     * @return $this
     */
    public function header($header = null)
    {
        if (is_array($header)) {
            $this->header = array_merge($this->header, $header);
        } else {
            $this->header[] = $header;
        }
        foreach ($this->header as $value) {
            header($value);
        }
        return $this;
    }

    /**
     * 返回内容
     * @param $data
     * @return false|int|null|string
     */
    public function dispatch($data)
    {
        if ($data instanceof Response) {
            return $data;
        } else {
            // 处理响应数据，并返回
            $responseData = new ResponseData($data);
            $this->content = $responseData->dispatch();

            return $this;
        }
    }

}