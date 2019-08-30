<?php

namespace top\library\http\response;

use top\library\View;
use top\traits\Json;

/**
 * 处理响应数据
 * Class FormatResponse
 * @package top\library\http
 */
class ResponseData
{
    use Json;

    /**
     * 执行程序后返回的实际数据
     * @var false|int|null|string
     */
    public $data = null;

    public function __construct($data)
    {
        if (DEBUG === false) {
            ob_clean();
        }
        $this->data = $this->checkData($data);
    }

    /**
     * 检查并处理数据
     * @param $data
     * @return false|int|null|string
     */
    private function checkData($data)
    {
        $responseData = null;
        if (is_array($data)) {
            if (request()->isAjax()) {
                $responseData = $this->returnJson($data);
            } else {
                $view = View::instance();
                $filename = request()->controller() . '/' . request()->method();
                $responseData = $view->fetch($filename, $data);
                unset($filename);
            }
        } elseif (is_bool($data)) {
            if ($data) {
                $responseData = 1;
            } else {
                $responseData = 0;
            }
        } else if (is_object($data)) {
            $responseData = '[OBJECT]';
        } else {
            // 否则数据作为字符串处理
            $responseData = $data;
        }
        return $responseData;
    }

    /**
     * 返回处理后的数据
     * @return false|int|null|string
     */
    public function dispatch()
    {
        return $this->data;
    }
}