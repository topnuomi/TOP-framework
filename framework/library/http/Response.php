<?php

namespace top\library\http;

use top\library\exception\ResponseException;
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
     * HTTP状态码
     * @var integer
     */
    private $code = 200;

    /**
     * HTTP状态码详情
     * @var array
     */
    private $codeDetail = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Moved Temporarily ',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        509 => 'Bandwidth Limit Exceeded',
    ];

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
     * 设置响应状态码
     * @param $code
     * @return Response
     */
    public function code($code = 200)
    {
        if (isset($this->codeDetail[$code])) {
            $this->code = $code;
            $text = $code . ' ' . $this->codeDetail[$code];
            return $this->header([
                'HTTP/1.1 ' . $text,
                'Status ' . $text,
            ]);
        }
        throw new ResponseException('不支持的状态码：' . $code);
    }

    /**
     * 返回内容
     * @param $data
     * @return Response
     */
    public function send($data = null)
    {
        if ($data instanceof Response) {
            return $data;
        } else {
            $pages = config('error_pages');
            if (isset($pages[$this->code])) {
                $filename = $pages[$this->code];
                if (is_file($filename)) {
                    $this->content = file_get_contents($filename);
                } else {
                    $this->content = '';
                }
            } else {
                // 处理响应数据，并返回
                $this->content = $this->getContent($data);
            }

            return $this;
        }
    }

    /**
     * 输出文件
     * @param $filename
     * @param $name
     * @return Response
     */
    public function sendFile($filename = null, $name = null)
    {
        if (is_file($filename)) {
            $name = ($name) ? $name : uniqid() . '.' . substr($filename, strrpos($filename, '.') + 1);
            return $this->header([
                'Content-Disposition: attachment; filename="' . $name . '"',
            ])->code(200)->send(readfile($filename));
        }
        throw new ResponseException('不存在的文件：' . $filename);
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

}
