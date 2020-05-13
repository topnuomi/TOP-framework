<?php

namespace top\library\http;

use top\library\View;
use top\traits\Instance;
use top\traits\Json;

/**
 * 响应类
 * Class Response
 * @package top\library\http
 */
class Response
{

    use Instance;

    use Json;

    /**
     * 响应内容
     * @var string
     */
    public $content = '';

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
    public function send($data)
    {
        if ($data instanceof Response) {
            return $data;
        } else {
            // 处理响应数据，并返回
            $this->content = $this->getContent($data);

            return $this;
        }
    }

    public function notFound()
    {
        $this->header([
            'HTTP/1.1 404 Not Found',
            'Status: 404 Not Found'
        ]);
        return <<<EOF
页面找不到了
EOF;

    }

    /**
     * 处理数据
     * @param $data
     * @return false|int|null|string
     */
    private function getContent($data)
    {
        if (is_array($data)) {
            $request = request();
            if ($request->is('ajax')) {
                $this->header('Content-Type: application/json');
                return $this->returnJson($data);
            } else {
                $this->header('Content-Type: text/html; charset=utf-8');
                $filename = $request->controller() . '/' . $request->method();
                return View::instance()->fetch($filename, $data);
            }
        } elseif (is_bool($data)) {
            return ($data) ? 'true' : 'false';
        } else if (is_object($data)) {
            return '[OBJECT]';
        }
        return $data;
    }

    /**
     * 直接echo处理
     * @return string
     */
    public function __toString()
    {
        return $this->content;
    }

}
