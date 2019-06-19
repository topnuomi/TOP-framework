<?php

namespace top\library\http;

/**
 * 请求类
 * @author topnuomi 2018年11月23日
 */
class Request
{

    private $server = [];

    private static $instance;

    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->server = (!empty($_SERVER)) ? $_SERVER : [];
    }

    public function method()
    {
        return (isset($this->server['REQUEST_METHOD']) && $this->server['REQUEST_METHOD'] != '') ? $this->server['REQUEST_METHOD'] : '';
    }

    /**
     * POST
     *
     * @return boolean
     */
    public function isPost()
    {
        return $this->method() == 'POST';
    }

    /**
     * GET
     *
     * @return boolean
     */
    public function isGet()
    {
        return $this->method() == 'GET';
    }

    /**
     * PUT
     *
     * @return boolean
     */
    public function isPut()
    {
        return $this->method() == 'PUT';
    }

    /**
     * DELETE
     *
     * @return boolean
     */
    public function isDelete()
    {
        return $this->method() == 'DELETE';
    }

    /**
     * HEAD
     *
     * @return boolean
     */
    public function isHead()
    {
        return $this->method() == 'HEAD';
    }

    /**
     * HEAD
     *
     * @return boolean
     */
    public function isPatch()
    {
        return $this->method() == 'PATCH';
    }

    /**
     * HEAD
     *
     * @return boolean
     */
    public function isOptions()
    {
        return $this->method() == 'OPTIONS';
    }

    /**
     * AJAX
     *
     * @return boolean
     */
    public function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
    }

    /**
     * 创建一个请求（post或get取决于data是否有值且不为空或空数组）
     *
     * @param string $url
     * @param array $data
     * @param array $header
     * @return boolean
     */
    public function create($url, $data = [], $header = [])
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, $header);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        $res = curl_exec($curl);
        curl_close($curl);
        if ($res) {
            return $res;
        }
        return false;
    }

    /**
     * 获取客户端IP
     * @param int $type
     * @param bool $client
     * @return mixed
     */
    public function ip($type = 0, $client = true)
    {
        $type = $type ? 1 : 0;
        static $ip = NULL;
        if ($ip !== NULL)
            return $ip[$type];
        if ($client) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos = array_search('unknown', $arr);
                if (false !== $pos)
                    unset($arr[$pos]);
                $ip = trim($arr[0]);
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        $long = sprintf("%u", ip2long($ip));
        $ip = $long ? [
            $ip,
            $long
        ] : [
            '0.0.0.0',
            0
        ];
        return $ip[$type];
    }

    public function post($name)
    {
        $data = (isset($_POST[$name])) ? $_POST[$name] : '';
        return $this->checkData($data);
    }

    public function get($name)
    {
        $data = (isset($_GET[$name])) ? $_GET[$name] : '';
        return $this->checkData($data);
    }

    public function checkData($data)
    {
        if (is_array($data)) {
            foreach ($data as $k => $v)
                $data[$k] = filter($v);
        } else {
            $data = filter($data);
        }
        return $data;
    }

    public function __destruct()
    {
    }
}
